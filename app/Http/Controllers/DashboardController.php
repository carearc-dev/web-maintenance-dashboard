<?php

namespace App\Http\Controllers;

use App\Enums\SiteStatus;
use App\Models\Site;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __invoke(Request $request)
    {
        $query = $request->user()->isAdmin()
            ? Site::query()
            : $request->user()->assignedSites()->getQuery();

        return view('dashboard', [
            'siteCount' => (clone $query)->count(),
            'normalCount' => (clone $query)->where('status', SiteStatus::Normal->value)->count(),
            'needsCheckCount' => (clone $query)->where('status', SiteStatus::NeedsCheck->value)->count(),
            'updatesCount' => (clone $query)->where('status', SiteStatus::UpdatesAvailable->value)->count(),
            'incidentCount' => (clone $query)->where('status', SiteStatus::Incident->value)->count(),
            'recentSites' => (clone $query)->latest('last_maintained_on')->limit(6)->get(),
        ]);
    }
}
