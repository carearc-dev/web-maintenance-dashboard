<?php

namespace App\Services\Monitoring;

use Illuminate\Support\Facades\Http;
use Throwable;

class WordpressStatusInspector
{
    public function inspect(string $siteUrl): array
    {
        $baseUrl = rtrim($siteUrl, '/');

        return $this->fromCarearcEndpoint($baseUrl)
            ?? $this->fromPublicRestApi($baseUrl)
            ?? [
                'connection_status' => 'unavailable',
                'security_status' => 'needs_manual_check',
                'status_message' => 'WordPress状態を自動取得できませんでした。管理画面または専用プラグインで確認してください。',
                'raw_payload' => null,
            ];
    }

    private function fromCarearcEndpoint(string $baseUrl): ?array
    {
        try {
            $response = Http::timeout(10)
                ->acceptJson()
                ->get("{$baseUrl}/wp-json/carearc-maintenance/v1/status");

            if (! $response->ok() || ! is_array($response->json())) {
                return null;
            }

            $payload = $response->json();
            $pluginUpdates = (int) ($payload['plugin_update_count'] ?? 0);
            $coreUpdate = (bool) ($payload['core_update_available'] ?? false);

            return [
                'wordpress_version' => $payload['wordpress_version'] ?? null,
                'php_version' => $payload['php_version'] ?? null,
                'theme_name' => $payload['theme_name'] ?? null,
                'theme_version' => $payload['theme_version'] ?? null,
                'uses_child_theme' => (bool) ($payload['uses_child_theme'] ?? false),
                'core_update_available' => $coreUpdate,
                'plugin_update_count' => $pluginUpdates,
                'connection_status' => 'connected',
                'security_status' => $coreUpdate || $pluginUpdates > 0 ? 'updates_available' : 'ok',
                'status_message' => '専用プラグインからWordPress状態を取得しました。',
                'plugins' => $payload['plugins'] ?? [],
                'raw_payload' => $payload,
            ];
        } catch (Throwable) {
            return null;
        }
    }

    private function fromPublicRestApi(string $baseUrl): ?array
    {
        try {
            $response = Http::timeout(10)
                ->acceptJson()
                ->get("{$baseUrl}/wp-json");

            if (! $response->ok() || ! is_array($response->json())) {
                return null;
            }

            $payload = $response->json();

            return [
                'wordpress_version' => null,
                'php_version' => null,
                'theme_name' => null,
                'theme_version' => null,
                'uses_child_theme' => false,
                'core_update_available' => false,
                'plugin_update_count' => 0,
                'connection_status' => 'public_rest_detected',
                'security_status' => 'needs_manual_check',
                'status_message' => '公開REST APIを確認しました。バージョンや更新有無は専用プラグイン導入後に取得できます。',
                'plugins' => [],
                'raw_payload' => [
                    'name' => $payload['name'] ?? null,
                    'url' => $payload['url'] ?? null,
                    'home' => $payload['home'] ?? null,
                ],
            ];
        } catch (Throwable) {
            return null;
        }
    }
}
