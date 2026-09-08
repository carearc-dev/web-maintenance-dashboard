<?php

namespace App\Console\Commands;

use App\Models\Site;
use App\Services\Monitoring\SiteHealthCheckService;
use Illuminate\Console\Command;

class CheckSiteHealth extends Command
{
    protected $signature = 'sites:check-health {site? : Site ID to check only one site}';

    protected $description = 'Check site HTTP, SSL, security headers, and WordPress status.';

    public function handle(SiteHealthCheckService $checker): int
    {
        $query = Site::query()->with(['serverInformation', 'wordpressInformation']);

        if ($siteId = $this->argument('site')) {
            $query->whereKey($siteId);
        }

        $checked = 0;

        $query->orderBy('id')->chunkById(50, function ($sites) use ($checker, &$checked) {
            foreach ($sites as $site) {
                $this->line("Checking {$site->name}...");
                $checker->check($site);
                $checked += 1;
            }
        });

        $this->info("Checked {$checked} site(s).");

        return self::SUCCESS;
    }
}
