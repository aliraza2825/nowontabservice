<?php

namespace App\Http\Controllers;

use App\Models\ToastMenu;
use App\Services\MenuWidgetFilter;
use App\Services\ToastLocationService;
use Illuminate\Http\Request;

class MenuWidgetController extends Controller
{
    public function index(Request $request, MenuWidgetFilter $filter, ToastLocationService $locations)
    {
        $location = $locations->find($request->query('location'));
        $menu = ToastMenu::latestForLocation($location['guid']);

        $menus = [];
        $categories = [];

        if ($menu) {
            $formatted = $filter->filter($menu->formatted_data, $location['guid']);
            $menus = $formatted['menus'] ?? [];
            $categories = $formatted['categories'] ?? [];
        }

        return view('menu-widget', [
            'menus' => $menus,
            'categories' => $categories,
            'lastSyncedAt' => $menu?->last_synced_at,
            'location' => $location,
        ]);
    }
}
