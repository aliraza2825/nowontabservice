<?php

namespace App\Http\Controllers;

use App\Models\ToastMenu;
use App\Models\ToastMenuWidgetSetting;
use App\Services\ToastLocationService;
use App\Services\ToastMenuFormatter;
use App\Services\ToastService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class MenuWidgetAdminController extends Controller
{
    public function edit(Request $request, ToastLocationService $locations): View
    {
        $location = $locations->find($request->query('location'));
        $menu = ToastMenu::latestForLocation($location['guid']);
        $settings = ToastMenuWidgetSetting::current($location['guid']);

        return view('menu-widget-admin', [
            'locations' => $locations->locations(),
            'currentLocation' => $location,
            'menus' => $menu?->formatted_data['menus'] ?? [],
            'lastSyncedAt' => $menu?->last_synced_at,
            'allowedMenuGuids' => $settings?->allowed_menu_guids ?? [],
            'allowedCategoryGuids' => $settings?->allowed_category_guids ?? [],
            'allowedItemGuids' => $settings?->allowed_item_guids ?? [],
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $location = app(ToastLocationService::class)->find($request->input('location_guid'));

        ToastMenuWidgetSetting::create([
            'location_guid' => $location['guid'],
            'location_name' => $location['name'],
            'allowed_menu_guids' => $this->cleanGuids($request->input('menus', [])),
            'allowed_category_guids' => $this->cleanGuids($request->input('categories', [])),
            'allowed_item_guids' => $this->cleanGuids($request->input('items', [])),
        ]);

        return redirect()
            ->route('menu-widget.admin.edit', ['location' => $location['guid']])
            ->with('status', 'Widget menu selection saved.');
    }

    public function fetchNewMenu(Request $request, ToastService $toastService, ToastMenuFormatter $formatter, ToastLocationService $locations): RedirectResponse
    {
        $location = $locations->find($request->input('location_guid'));

        try {
            $metadata = $toastService->getMetadata($location['guid']);
            $rawMenu = $toastService->getMenus($location['guid']);
            $formattedMenu = $formatter->format($rawMenu);

            ToastMenu::create([
                'location_guid' => $location['guid'],
                'location_name' => $location['name'],
                'raw_json' => json_encode($rawMenu),
                'formatted_json' => json_encode($formattedMenu),
                'metadata_hash' => md5(json_encode($metadata)),
                'last_synced_at' => now(),
            ]);

            return redirect()
                ->route('menu-widget.admin.edit', ['location' => $location['guid']])
                ->with('status', 'Fresh Toast menu fetched for '.$location['name'].'. Your previous selection is still saved.');
        } catch (\Throwable $e) {
            Log::error('Toast admin menu fetch failed', [
                'location_guid' => $location['guid'],
                'error' => $e->getMessage(),
            ]);

            return redirect()
                ->route('menu-widget.admin.edit', ['location' => $location['guid']])
                ->with('error', 'Could not fetch Toast menu: '.$e->getMessage());
        }
    }

    private function cleanGuids(mixed $values): array
    {
        if (! is_array($values)) {
            return [];
        }

        return array_values(array_unique(array_filter(array_map(
            fn ($value): string => trim((string) $value),
            $values
        ))));
    }
}
