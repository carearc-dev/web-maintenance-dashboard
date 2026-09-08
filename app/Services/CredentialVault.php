<?php

namespace App\Services;

use App\Models\Credential;
use App\Models\User;
use Illuminate\Http\Request;

class CredentialVault
{
    public function __construct(private readonly ActivityLogger $activityLogger)
    {
    }

    public function reveal(Credential $credential, User $user, Request $request): array
    {
        $credential->forceFill(['last_viewed_at' => now()])->save();

        $this->activityLogger->log(
            'credential_viewed',
            $user,
            $credential->site,
            $credential,
            ['service_name' => $credential->service_name],
            $request
        );

        return [
            'login_id' => $credential->login_id,
            'password' => $credential->password,
            'notes' => $credential->notes,
        ];
    }

    public function recordCopy(Credential $credential, User $user, Request $request): void
    {
        $this->activityLogger->log(
            'credential_copied',
            $user,
            $credential->site,
            $credential,
            ['service_name' => $credential->service_name],
            $request
        );
    }
}
