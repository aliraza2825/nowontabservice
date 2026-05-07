<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ToastMenuWidgetSetting extends Model
{
    protected $fillable = [
        'allowed_menu_guids',
        'allowed_category_guids',
        'allowed_item_guids',
    ];

    protected $casts = [
        'allowed_menu_guids' => 'array',
        'allowed_category_guids' => 'array',
        'allowed_item_guids' => 'array',
    ];

    public static function current(): ?self
    {
        return self::latest()->first();
    }
}
