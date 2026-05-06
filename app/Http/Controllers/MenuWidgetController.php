<?php

namespace App\Http\Controllers;

use App\Models\ToastMenu;

class MenuWidgetController extends Controller
{
    public function index()
    {
        $menu = ToastMenu::latest()->first();

        $menus = [];
        $categories = [];

        if ($menu) {
            $formatted = $menu->formatted_data;
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
