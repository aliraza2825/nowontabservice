<?php

namespace App\Http\Controllers;

use App\Models\ToastMenu;
use App\Models\ToastMenuWidgetSetting;
use App\Services\ToastMenuFormatter;
use App\Services\ToastService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

class MenuWidgetAdminController extends Controller
{
    public function edit(): View
    {
        $menu = ToastMenu::latest()->first();
        $settings = ToastMenuWidgetSetting::current();

        return view('menu-widget-admin', [
            'menus' => $menu?->formatted_data['menus'] ?? [],
            'lastSyncedAt' => $menu?->last_synced_at,
            'allowedMenuGuids' => $settings?->allowed_menu_guids ?? [],
            'allowedCategoryGuids' => $settings?->allowed_category_guids ?? [],
            'allowedItemGuids' => $settings?->allowed_item_guids ?? [],
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        ToastMenuWidgetSetting::create([
            'allowed_menu_guids' => $this->cleanGuids($request->input('menus', [])),
            'allowed_category_guids' => $this->cleanGuids($request->input('categories', [])),
            'allowed_item_guids' => $this->cleanGuids($request->input('items', [])),
        ]);

        return redirect()
            ->route('menu-widget.admin.edit')
            ->with('status', 'Widget menu selection saved.');
    }

    public function fetchNewMenu(ToastService $toastService, ToastMenuFormatter $formatter): RedirectResponse
    {
        try {
            $metadata = $toastService->getMetadata();
            $rawMenu = $toastService->getMenus();
            $formattedMenu = $formatter->format($rawMenu);

            ToastMenu::create([
                'raw_json' => json_encode($rawMenu),
                'formatted_json' => json_encode($formattedMenu),
                'metadata_hash' => md5(json_encode($metadata)),
                'last_synced_at' => now(),
            ]);

            return redirect()
                ->route('menu-widget.admin.edit')
                ->with('status', 'Fresh Toast menu fetched. Your previous selection is still saved.');
        } catch (\Throwable $e) {
            Log::error('Toast admin menu fetch failed', [
                'error' => $e->getMessage(),
            ]);

            return redirect()
                ->route('menu-widget.admin.edit')
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
