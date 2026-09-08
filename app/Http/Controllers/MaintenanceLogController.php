<?php

namespace App\Http\Controllers;

use App\Models\Site;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;

class MaintenanceLogController extends Controller
{
    public function store(Site $site, Request $request, ActivityLogger $logger)
    {
        $this->authorize('addMaintenanceLog', $site);

        $validated = $request->validate([
            'worked_on' => ['required', 'date'],
            'category' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'minutes_spent' => ['nullable', 'integer', 'min:0'],
            'version_before' => ['nullable', 'string', 'max:255'],
            'version_after' => ['nullable', 'string', 'max:255'],
            'repository_url' => ['nullable', 'url', 'max:255'],
            'notes' => ['nullable', 'string'],
        ]);

        $log = $site->maintenanceLogs()->create($validated + ['user_id' => $request->user()->id]);
        $site->forceFill(['last_maintained_on' => $validated['worked_on']])->save();
        $logger->log('maintenance_log_created', $request->user(), $site, $log, [], $request);

        return redirect()->route('sites.show', $site);
    }
}
