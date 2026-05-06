<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ToastMenu extends Model
{
    protected $fillable = [
        'raw_json',
        'formatted_json',
        'metadata_hash',
        'last_synced_at',
    ];

    protected $casts = [
        'last_synced_at' => 'datetime',
    ];

    public function getRawDataAttribute(): array
    {
        return $this->raw_json ? json_decode($this->raw_json, true) : [];
    }

    public function getFormattedDataAttribute(): array
    {
        return $this->formatted_json ? json_decode($this->formatted_json, true) : [];
    }
}
