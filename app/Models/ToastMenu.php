<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ToastMenu extends Model
{
    protected $fillable = [
        'location_guid',
        'location_name',
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

    public static function latestForLocation(string $locationGuid): ?self
    {
        $menu = self::where('location_guid', $locationGuid)->latest()->first();

        if (! $menu && $locationGuid === config('services.toast.restaurant_guid')) {
            return self::whereNull('location_guid')->latest()->first();
        }

        return $menu;
    }
}
