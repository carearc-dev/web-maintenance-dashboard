<?php

namespace App\Models;

use App\Enums\SiteStatus;
use App\Enums\SiteType;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Site extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'company_name',
        'name',
        'url',
        'admin_url',
        'type',
        'status',
        'published_on',
        'maintenance_started_on',
        'maintenance_ended_on',
        'director_id',
        'engineer_id',
        'github_url',
        'notes',
        'last_maintained_on',
        'next_check_on',
        'last_checked_at',
        'auto_status_reason',
        'created_by',
    ];

    protected function casts(): array
    {
        return [
            'type' => SiteType::class,
            'status' => SiteStatus::class,
            'published_on' => 'date',
            'maintenance_started_on' => 'date',
            'maintenance_ended_on' => 'date',
            'last_maintained_on' => 'date',
            'next_check_on' => 'date',
            'last_checked_at' => 'datetime',
        ];
    }

    public function users()
    {
        return $this->belongsToMany(User::class, 'site_users')->withPivot([
            'can_view_site',
            'can_view_server',
            'can_view_credentials',
            'can_edit_credentials',
            'can_view_maintenance',
            'can_add_maintenance_log',
        ])->withTimestamps();
    }

    public function serverInformation()
    {
        return $this->hasOne(ServerInformation::class);
    }

    public function wordpressInformation()
    {
        return $this->hasOne(WordpressInformation::class);
    }

    public function maintenanceLogs()
    {
        return $this->hasMany(MaintenanceLog::class)->latest('worked_on');
    }

    public function monitoringLogs()
    {
        return $this->hasMany(MonitoringLog::class)->latest('checked_at');
    }

    public function credentials()
    {
        return $this->hasMany(Credential::class);
    }
}
