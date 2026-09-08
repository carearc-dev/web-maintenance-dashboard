<?php

namespace App\Services\Monitoring;

use App\Enums\SiteStatus;
use App\Enums\SiteType;
use App\Models\Site;
use Carbon\CarbonImmutable;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;

class SiteHealthCheckService
{
    public function __construct(
        private readonly HttpMonitor $httpMonitor,
        private readonly SslCertificateInspector $sslInspector,
        private readonly WordpressStatusInspector $wordpressInspector,
    ) {
    }

    public function check(Site $site): Site
    {
        $http = $this->httpMonitor->check($site->url);
        $ssl = $this->sslInspector->inspect($site->url);
        $wordpress = $site->type === SiteType::Wordpress
            ? $this->wordpressInspector->inspect($site->url)
            : null;

        return DB::transaction(function () use ($site, $http, $ssl, $wordpress) {
            $site->monitoringLogs()->create([
                'checked_at' => now(),
                'http_status' => $http->status,
                'response_time_ms' => $http->responseTimeMs,
                'result' => $http->result,
                'security_headers' => $http->securityHeaders,
                'error_message' => $http->errorMessage,
            ]);

            $site->serverInformation()->updateOrCreate([], [
                'ssl_enabled' => $ssl['enabled'],
                'ssl_expires_on' => $ssl['expires_on'],
                'security_headers' => $http->securityHeaders,
                'last_checked_at' => now(),
            ]);

            if ($wordpress) {
                $wordpressInformation = $site->wordpressInformation()->updateOrCreate([], Arr::only($wordpress, [
                    'wordpress_version',
                    'php_version',
                    'theme_name',
                    'theme_version',
                    'uses_child_theme',
                    'core_update_available',
                    'plugin_update_count',
                    'connection_status',
                    'security_status',
                    'status_message',
                    'raw_payload',
                ]) + ['last_checked_at' => now()]);

                $wordpressInformation->plugins()->delete();
                foreach ($wordpress['plugins'] ?? [] as $plugin) {
                    $wordpressInformation->plugins()->create([
                        'name' => $plugin['name'] ?? $plugin['slug'] ?? 'unknown',
                        'current_version' => $plugin['current_version'] ?? $plugin['version'] ?? null,
                        'latest_version' => $plugin['latest_version'] ?? null,
                        'update_status' => ($plugin['update_available'] ?? false) ? 'update_available' : 'current',
                        'last_checked_on' => now()->toDateString(),
                    ]);
                }
            }

            $decision = $this->decideStatus($http, $ssl, $wordpress);
            $site->forceFill([
                'status' => $decision['status'],
                'last_checked_at' => now(),
                'next_check_on' => now()->addDay()->toDateString(),
                'auto_status_reason' => $decision['reason'],
            ])->save();

            return $site->refresh();
        });
    }

    private function decideStatus(HttpCheckResult $http, array $ssl, ?array $wordpress): array
    {
        if ($http->result === 'incident') {
            return ['status' => SiteStatus::Incident, 'reason' => $http->errorMessage ?? 'サイトへ接続できませんでした。'];
        }

        if ($http->result === 'needs_check') {
            return ['status' => SiteStatus::NeedsCheck, 'reason' => $http->errorMessage ?? 'HTTPステータスの確認が必要です。'];
        }

        if (($ssl['enabled'] ?? false) === false) {
            return ['status' => SiteStatus::NeedsCheck, 'reason' => $ssl['error'] ?? 'SSL証明書の確認が必要です。'];
        }

        if (! empty($ssl['expires_on'])) {
            $warningDays = (int) config('maintenance.ssl_warning_days', 30);
            $expiresOn = CarbonImmutable::parse($ssl['expires_on']);
            if ($expiresOn->isBefore(now()->addDays($warningDays))) {
                return ['status' => SiteStatus::NeedsCheck, 'reason' => "SSL期限が{$warningDays}日以内です。"];
            }
        }

        if ($wordpress && ($wordpress['security_status'] ?? null) === 'updates_available') {
            return ['status' => SiteStatus::UpdatesAvailable, 'reason' => 'WordPress本体またはプラグインに更新があります。'];
        }

        if ($wordpress && ($wordpress['security_status'] ?? null) === 'needs_manual_check') {
            return ['status' => SiteStatus::NeedsCheck, 'reason' => $wordpress['status_message'] ?? 'WordPress状態は手動確認が必要です。'];
        }

        return ['status' => SiteStatus::Normal, 'reason' => '自動チェックで異常は検出されませんでした。'];
    }
}
