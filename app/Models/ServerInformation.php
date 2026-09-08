<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServerInformation extends Model
{
    protected $table = 'server_information';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'ssl_enabled' => 'boolean',
            'ssl_expires_on' => 'date',
            'domain_expires_on' => 'date',
            'basic_auth_enabled' => 'boolean',
            'backup_enabled' => 'boolean',
            'security_headers' => 'array',
            'last_checked_at' => 'datetime',
        ];
    }
}
