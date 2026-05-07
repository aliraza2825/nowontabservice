<?php

namespace App\Http\Controllers;

use App\Models\ToastMenu;
use App\Services\ToastMenuFormatter;
use App\Services\ToastService;
use Illuminate\Support\Facades\Log;

class ToastMenuController extends Controller
{
    public function publicMenu()
    {
        $menu = ToastMenu::latest()->first();

        if (! $menu) {
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

    public function syncMenu(ToastService $toastService, ToastMenuFormatter $formatter)
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
            $formattedMenu = $formatter->format($rawMenu);

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

    public function forceSyncMenu(ToastService $toastService, ToastMenuFormatter $formatter)
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
}
