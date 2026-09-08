<?php

namespace App\Services\Monitoring;

use Carbon\CarbonImmutable;
use Throwable;

class SslCertificateInspector
{
    public function inspect(string $url): array
    {
        $host = parse_url($url, PHP_URL_HOST);

        if (! $host) {
            return ['enabled' => false, 'expires_on' => null, 'error' => 'URL host is missing.'];
        }

        try {
            $context = stream_context_create([
                'ssl' => [
                    'capture_peer_cert' => true,
                    'verify_peer' => true,
                    'verify_peer_name' => true,
                    'SNI_enabled' => true,
                    'peer_name' => $host,
                ],
            ]);

            $client = stream_socket_client(
                "ssl://{$host}:443",
                $errno,
                $errstr,
                8,
                STREAM_CLIENT_CONNECT,
                $context,
            );

            if (! $client) {
                return ['enabled' => false, 'expires_on' => null, 'error' => $errstr ?: "SSL connection failed ({$errno})."];
            }

            $params = stream_context_get_params($client);
            $certificate = openssl_x509_parse($params['options']['ssl']['peer_certificate'] ?? null);

            if (! isset($certificate['validTo_time_t'])) {
                return ['enabled' => true, 'expires_on' => null, 'error' => 'SSL certificate expiry could not be parsed.'];
            }

            return [
                'enabled' => true,
                'expires_on' => CarbonImmutable::createFromTimestamp($certificate['validTo_time_t'])->toDateString(),
                'error' => null,
            ];
        } catch (Throwable $exception) {
            return ['enabled' => false, 'expires_on' => null, 'error' => $exception->getMessage()];
        }
    }
}
