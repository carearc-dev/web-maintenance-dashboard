<?php

namespace App\Services;

use App\Models\ActivityLog;
use App\Models\Site;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Request;

class ActivityLogger
{
    public function log(
        string $action,
        ?User $user = null,
        ?Site $site = null,
        ?Model $target = null,
        array $metadata = [],
        ?Request $request = null
    ): ActivityLog {
        return ActivityLog::create([
            'user_id' => $user?->id,
            'site_id' => $site?->id,
            'action' => $action,
            'target_type' => $target ? $target::class : null,
            'target_id' => $target?->getKey(),
            'ip_address' => $request?->ip(),
            'user_agent' => $request?->userAgent(),
            'metadata' => $metadata ?: null,
        ]);
    }
}
