<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ToastMenuWidgetSetting extends Model
{
    protected $fillable = [
        'location_guid',
        'location_name',
        'allowed_menu_guids',
        'allowed_category_guids',
        'allowed_item_guids',
    ];

    protected $casts = [
        'allowed_menu_guids' => 'array',
        'allowed_category_guids' => 'array',
        'allowed_item_guids' => 'array',
    ];

    public static function current(?string $locationGuid = null): ?self
    {
        if (! $locationGuid) {
            return self::latest()->first();
        }

        $settings = self::where('location_guid', $locationGuid)->latest()->first();

        if (! $settings && $locationGuid === config('services.toast.restaurant_guid')) {
            return self::whereNull('location_guid')->latest()->first();
        }

        return $settings;
    }
}
