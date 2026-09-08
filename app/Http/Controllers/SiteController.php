<?php

namespace App\Http\Controllers;

use App\Models\Site;
use App\Services\ActivityLogger;
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

        $validated = $request->validate([
            'company_name' => ['required', 'string', 'max:255'],
            'name' => ['required', 'string', 'max:255'],
            'url' => ['required', 'url', 'max:255'],
            'type' => ['required', 'string'],
            'status' => ['required', 'string'],
        ]);

        $site = Site::create($validated + ['created_by' => $request->user()->id]);
        $logger->log('site_created', $request->user(), $site, $site, [], $request);

        return redirect()->route('sites.show', $site);
    }
}
