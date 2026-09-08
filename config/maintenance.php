<?php

return [
    'ssl_warning_days' => (int) env('SSL_WARNING_DAYS', 30),
    'domain_warning_days' => (int) env('DOMAIN_WARNING_DAYS', 30),
    'monitoring_interval_minutes' => (int) env('MONITORING_INTERVAL_MINUTES', 10),
];
