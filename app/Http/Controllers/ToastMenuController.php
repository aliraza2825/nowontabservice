<?php

namespace App\Http\Controllers;

use App\Models\ToastMenu;
use App\Services\ToastService;
use Illuminate\Support\Facades\Log;

class ToastMenuController extends Controller
{
    public function publicMenu()
    {
        $menu = ToastMenu::latest()->first();

        if (!$menu) {
            return response()->json([
                'success' => false,
                'message' => 'Menu is not synced yet.',
                'categories' => [],
            ], 404);
        }

        return response()->json([
            'success' => true,
            'last_synced_at' => optional($menu->last_synced_at)->toDateTimeString(),
            'categories' => $menu->formatted_data['categories'] ?? [],
        ]);
    }

    public function syncMenu(ToastService $toastService)
    {
        try {
            $metadata = $toastService->getMetadata();
            $metadataHash = md5(json_encode($metadata));

            $latestMenu = ToastMenu::latest()->first();

            if ($latestMenu && $latestMenu->metadata_hash === $metadataHash) {
                return response()->json([
                    'success' => true,
                    'message' => 'Menu already up to date.',
                    'last_synced_at' => optional($latestMenu->last_synced_at)->toDateTimeString(),
                ]);
            }

            $rawMenu = $toastService->getMenus();
            $formattedMenu = $this->formatMenu($rawMenu);

            ToastMenu::create([
                'raw_json' => json_encode($rawMenu),
                'formatted_json' => json_encode($formattedMenu),
                'metadata_hash' => $metadataHash,
                'last_synced_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Menu synced successfully.',
                'categories_count' => count($formattedMenu['categories'] ?? []),
            ]);

        } catch (\Throwable $e) {
            Log::error('Toast menu sync failed', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function forceSyncMenu(ToastService $toastService)
    {
        try {
            $metadata = $toastService->getMetadata();
            $rawMenu = $toastService->getMenus();
            $formattedMenu = $this->formatMenu($rawMenu);

            ToastMenu::create([
                'raw_json' => json_encode($rawMenu),
                'formatted_json' => json_encode($formattedMenu),
                'metadata_hash' => md5(json_encode($metadata)),
                'last_synced_at' => now(),
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Menu force synced successfully.',
                'categories_count' => count($formattedMenu['categories'] ?? []),
            ]);

        } catch (\Throwable $e) {
            Log::error('Toast force sync failed', [
                'error' => $e->getMessage(),
            ]);

            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    private function formatMenu(array $rawMenu): array
    {
        $menus = [];

        foreach (($rawMenu['menus'] ?? []) as $menu) {
            $menuName = $menu['name'] ?? 'Menu';

            $allowedMenus = ['Kitchen', 'Bar'];

            if (!in_array($menuName, $allowedMenus, true)) {
                continue;
            }

            $categories = [];

            foreach (($menu['menuGroups'] ?? []) as $group) {
                $items = $this->extractItemsFromGroup($group);

                if (!empty($items)) {
                    $categories[] = [
                        'name' => $group['name'] ?? 'Menu',
                        'items' => $items,
                    ];
                }
            }

            if (!empty($categories)) {
                $menus[] = [
                    'name' => $menuName,
                    'categories' => $categories,
                ];
            }
        }

        return [
            'menus' => $menus,
            'categories' => $menus[0]['categories'] ?? [],
        ];
    }
    private function extractItemsFromGroup(array $group): array
    {
        $items = [];

        foreach (($group['menuItems'] ?? []) as $item) {
            if ($this->shouldHideItem($item)) {
                continue;
            }

            $items[] = [
                'guid' => $item['guid'] ?? null,
                'name' => $item['name'] ?? '',
                'description' => $item['description'] ?? '',
                'price' => $this->getItemPrice($item),
                'available' => $this->isAvailable($item),
                'image' => $item['image'] ?? null,
                'modifiers' => [],
            ];
        }

        foreach (($group['menuGroups'] ?? []) as $subgroup) {
            $items = array_merge($items, $this->extractItemsFromGroup($subgroup));
        }

        return $items;
    }

    private function getItemPrice(array $item): ?float
    {
        if (isset($item['price']) && is_numeric($item['price'])) {
            return (float) $item['price'];
        }

        if (isset($item['pricingStrategy']) && isset($item['pricingStrategy']['basePrice'])) {
            return (float) $item['pricingStrategy']['basePrice'];
        }

        if (isset($item['multiLocationPrice']) && is_numeric($item['multiLocationPrice'])) {
            return (float) $item['multiLocationPrice'];
        }

        return null;
    }

    private function isAvailable(array $item): bool
    {
        if (isset($item['visibility']) && $item['visibility'] === false) {
            return false;
        }

        if (isset($item['outOfStock']) && $item['outOfStock']) {
            return false;
        }

        if (isset($item['isAvailable']) && !$item['isAvailable']) {
            return false;
        }

        return true;
    }

    private function getModifiers(array $item): array
    {
        $modifiers = [];

        foreach (($item['modifierGroups'] ?? []) as $modifierGroup) {
            $modifierItems = [];

            foreach (($modifierGroup['modifiers'] ?? []) as $modifier) {
                $modifierItems[] = [
                    'name' => $modifier['name'] ?? '',
                    'price' => $this->getItemPrice($modifier),
                    'available' => $this->isAvailable($modifier),
                ];
            }

            $modifiers[] = [
                'name' => $modifierGroup['name'] ?? '',
                'items' => $modifierItems,
            ];
        }

        return $modifiers;
    }

    private function shouldHideItem(array $item): bool
    {
        $name = trim($item['name'] ?? '');

        if ($name === '') {
            return true;
        }

        // Hide Toast internal/course/helper items
        if (str_starts_with($name, '**')) {
            return true;
        }

        if (str_starts_with($name, '---')) {
            return true;
        }

        // Hide zero price items that are likely internal placeholders
        $salesCategory = $item['salesCategory']['name'] ?? '';

        if ((float)($item['price'] ?? 0) <= 0 && in_array($salesCategory, ['Course Lines'], true)) {
            return true;
        }

        return false;
    }
}
