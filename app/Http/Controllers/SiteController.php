<?php

namespace App\Http\Controllers;

use App\Models\Site;
use App\Services\ActivityLogger;
use App\Services\Monitoring\SiteHealthCheckService;
use Illuminate\Http\Request;

class SiteController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('viewAny', Site::class);

        $query = $request->user()->canManageOperations()
            ? Site::query()
            : $request->user()->assignedSites()->getQuery();

        $query->with(['serverInformation', 'wordpressInformation', 'users'])
            ->when($request->filled('keyword'), function ($q) use ($request) {
                $keyword = $request->string('keyword');
                $q->where(function ($inner) use ($keyword) {
                    $inner->where('name', 'like', "%{$keyword}%")
                        ->orWhere('company_name', 'like', "%{$keyword}%")
                        ->orWhere('url', 'like', "%{$keyword}%");
                });
            })
            ->when($request->filled('type'), fn ($q) => $q->where('type', $request->string('type')))
            ->when($request->filled('status'), fn ($q) => $q->where('status', $request->string('status')));

        return view('sites.index', ['sites' => $query->latest()->paginate(20)]);
    }

    public function create()
    {
        $this->authorize('create', Site::class);

        return view('sites.create');
    }

    public function show(Site $site)
    {
        $this->authorize('view', $site);

        $site->load([
            'serverInformation',
            'wordpressInformation',
            'wordpressInformation.plugins',
            'credentials',
            'maintenanceLogs',
            'users',
        ]);

        return view('sites.show', ['site' => $site]);
    }

    public function store(Request $request, ActivityLogger $logger)
    {
        $this->authorize('create', Site::class);

        $validated = $request->validate($this->rules());

        $site = Site::create($validated + ['created_by' => $request->user()->id]);
        $logger->log('site_created', $request->user(), $site, $site, [], $request);

        return redirect()->route('sites.show', $site);
    }

    public function edit(Site $site)
    {
        $this->authorize('update', $site);

        return view('sites.edit', ['site' => $site]);
    }

    public function update(Site $site, Request $request, ActivityLogger $logger)
    {
        $this->authorize('update', $site);

        $site->update($request->validate($this->rules()));
        $logger->log('site_updated', $request->user(), $site, $site, [], $request);

        return redirect()->route('sites.show', $site);
    }

    public function destroy(Site $site, Request $request, ActivityLogger $logger)
    {
        $this->authorize('delete', $site);

        $logger->log('site_deleted', $request->user(), $site, $site, ['name' => $site->name], $request);
        $site->delete();

        return redirect()->route('sites.index');
    }

    public function check(Site $site, Request $request, SiteHealthCheckService $checker, ActivityLogger $logger)
    {
        $this->authorize('update', $site);

        $checker->check($site);
        $logger->log('site_health_checked', $request->user(), $site, $site, [], $request);

        return redirect()->route('sites.show', $site);
    }

    private function rules(): array
    {
        return [
            'company_name' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'url' => ['required', 'url', 'max:255'],
            'admin_url' => ['nullable', 'url', 'max:255'],
            'type' => ['required', 'string'],
            'status' => ['required', 'string'],
            'github_url' => ['nullable', 'url', 'max:255'],
            'notes' => ['nullable', 'string'],
        ];
    }
}
