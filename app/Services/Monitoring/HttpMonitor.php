<?php

namespace App\Services\Monitoring;

use Illuminate\Support\Facades\Http;
use Throwable;

class HttpMonitor
{
    public function check(string $url): HttpCheckResult
    {
        $startedAt = microtime(true);

        try {
            $response = Http::timeout(12)
                ->connectTimeout(5)
                ->retry(1, 300, throw: false)
                ->withUserAgent(config('app.name', 'Web Maintenance Dashboard').'/1.0')
                ->get($url);

            $responseTimeMs = (int) round((microtime(true) - $startedAt) * 1000);
            $status = $response->status();

            return new HttpCheckResult(
                status: $status,
                responseTimeMs: $responseTimeMs,
                result: $status >= 200 && $status < 400 ? 'ok' : 'needs_check',
                securityHeaders: $this->extractSecurityHeaders($response->headers()),
                errorMessage: $status >= 400 ? "HTTP {$status}" : null,
            );
        } catch (Throwable $exception) {
            return new HttpCheckResult(
                status: null,
                responseTimeMs: null,
                result: 'incident',
                errorMessage: $exception->getMessage(),
            );
        }
    }

    private function extractSecurityHeaders(array $headers): array
    {
        $names = [
            'content-security-policy',
            'strict-transport-security',
            'x-frame-options',
            'x-content-type-options',
            'referrer-policy',
            'permissions-policy',
        ];

        $normalized = collect($headers)
            ->mapWithKeys(fn (array $value, string $key) => [strtolower($key) => $value[0] ?? null]);

        return collect($names)
            ->mapWithKeys(fn (string $name) => [$name => $normalized->get($name)])
            ->all();
    }
}
