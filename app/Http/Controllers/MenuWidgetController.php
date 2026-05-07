<?php

namespace App\Http\Controllers;

use App\Models\ToastMenu;
use App\Services\MenuWidgetFilter;

class MenuWidgetController extends Controller
{
    public function index(MenuWidgetFilter $filter)
    {
        $menu = ToastMenu::latest()->first();

        $menus = [];
        $categories = [];

        if ($menu) {
            $formatted = $filter->filter($menu->formatted_data);
            $menus = $formatted['menus'] ?? [];
            $categories = $formatted['categories'] ?? [];
        }

        return view('menu-widget', [
            'menus' => $menus,
            'categories' => $categories,
            'lastSyncedAt' => $menu?->last_synced_at,
        ]);
    }
}
