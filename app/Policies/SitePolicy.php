<?php

namespace App\Policies;

use App\Models\Site;
use App\Models\User;

class SitePolicy
{
    public function viewAny(User $user): bool
    {
        return true;
    }

    public function view(User $user, Site $site): bool
    {
        if ($user->canManageOperations()) {
            return true;
        }

        return $site->users()
            ->whereKey($user->id)
            ->wherePivot('can_view_site', true)
            ->exists();
    }

    public function create(User $user): bool
    {
        return $user->canManageOperations();
    }

    public function update(User $user, Site $site): bool
    {
        return $user->canManageOperations();
    }

    public function delete(User $user, Site $site): bool
    {
        return $user->canManageOperations();
    }

    public function addMaintenanceLog(User $user, Site $site): bool
    {
        if ($user->canManageOperations()) {
            return true;
        }

        return $site->users()
            ->whereKey($user->id)
            ->wherePivot('can_add_maintenance_log', true)
            ->exists();
    }
}
