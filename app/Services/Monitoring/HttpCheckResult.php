<?php

namespace App\Services\Monitoring;

class HttpCheckResult
{
    public function __construct(
        public readonly ?int $status,
        public readonly ?int $responseTimeMs,
        public readonly string $result,
        public readonly array $securityHeaders = [],
        public readonly ?string $errorMessage = null,
    ) {
    }
}
