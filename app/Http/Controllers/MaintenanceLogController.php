<?php

namespace App\Http\Controllers;

use App\Models\MaintenanceLog;
use App\Models\Site;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;

class MaintenanceLogController extends Controller
{
    public function index(Request $request)
    {
        $query = $request->user()->canManageOperations()
            ? MaintenanceLog::query()
            : MaintenanceLog::query()->whereHas('site.users', function ($q) use ($request) {
                $q->whereKey($request->user()->id)
                    ->wherePivot('can_view_maintenance', true);
            });

        $logs = $query->with(['site', 'user'])
            ->when($request->filled('keyword'), function ($q) use ($request) {
                $keyword = $request->string('keyword');
                $q->where(function ($inner) use ($keyword) {
                    $inner->where('description', 'like', "%{$keyword}%")
                        ->orWhere('category', 'like', "%{$keyword}%")
                        ->orWhereHas('site', fn ($site) => $site
                            ->where('name', 'like', "%{$keyword}%")
                            ->orWhere('company_name', 'like', "%{$keyword}%"));
                });
            })
            ->latest('worked_on')
            ->paginate(30);

        return view('maintenance.index', ['logs' => $logs]);
    }

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

    public function edit(MaintenanceLog $maintenanceLog)
    {
        $this->authorize('update', $maintenanceLog->site);

        return view('maintenance.edit', ['log' => $maintenanceLog->load('site')]);
    }

    public function update(MaintenanceLog $maintenanceLog, Request $request, ActivityLogger $logger)
    {
        $this->authorize('update', $maintenanceLog->site);

        $validated = $request->validate($this->rules());
        $maintenanceLog->update($validated);
        $logger->log('maintenance_log_updated', $request->user(), $maintenanceLog->site, $maintenanceLog, [], $request);

        return redirect()->route('maintenance.index');
    }

    public function destroy(MaintenanceLog $maintenanceLog, Request $request, ActivityLogger $logger)
    {
        $this->authorize('delete', $maintenanceLog->site);

        $site = $maintenanceLog->site;
        $logger->log('maintenance_log_deleted', $request->user(), $site, $maintenanceLog, [
            'worked_on' => $maintenanceLog->worked_on?->toDateString(),
            'category' => $maintenanceLog->category,
        ], $request);
        $maintenanceLog->delete();

        return redirect()->route('maintenance.index');
    }

    private function rules(): array
    {
        return [
            'worked_on' => ['required', 'date'],
            'category' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string'],
            'minutes_spent' => ['nullable', 'integer', 'min:0'],
            'version_before' => ['nullable', 'string', 'max:255'],
            'version_after' => ['nullable', 'string', 'max:255'],
            'repository_url' => ['nullable', 'url', 'max:255'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
