<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Credential extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'site_id',
        'type',
        'service_name',
        'login_url',
        'login_id',
        'password',
        'notes',
        'last_viewed_at',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'login_id' => 'encrypted',
            'password' => 'encrypted',
            'notes' => 'encrypted',
            'last_viewed_at' => 'datetime',
        ];
    }

    public function site()
    {
        return $this->belongsTo(Site::class);
    }
}
