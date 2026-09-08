<?php

namespace App\Policies;

use App\Models\Credential;
use App\Models\User;

class CredentialPolicy
{
    public function view(User $user, Credential $credential): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $credential->site->users()
            ->whereKey($user->id)
            ->wherePivot('can_view_credentials', true)
            ->exists();
    }

    public function update(User $user, Credential $credential): bool
    {
        if ($user->isAdmin()) {
            return true;
        }

        return $credential->site->users()
            ->whereKey($user->id)
            ->wherePivot('can_edit_credentials', true)
            ->exists();
    }
}
