<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WordpressInformation extends Model
{
    protected $table = 'wordpress_information';

    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'uses_child_theme' => 'boolean',
            'core_update_available' => 'boolean',
            'raw_payload' => 'array',
            'last_checked_at' => 'datetime',
        ];
    }

    public function plugins()
    {
        return $this->hasMany(WordpressPlugin::class);
    }
}
