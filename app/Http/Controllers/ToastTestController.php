<?php

namespace App\Http\Controllers;

use App\Services\ToastService;

class ToastTestController extends Controller
{
    public function token(ToastService $toastService)
    {
        $token = $toastService->getAccessToken();

        return response()->json([
            'success' => true,
            'message' => 'Toast token received successfully.',
            'token_preview' => substr($token, 0, 12) . '...',
        ]);
    }

    public function metadata(ToastService $toastService)
    {
        return response()->json([
            'success' => true,
            'data' => $toastService->getMenuMetadata(),
        ]);
    }

    public function menus(ToastService $toastService)
    {
        $menus = $toastService->getMenus();

        return response()->json([
            'success' => true,
            'top_level_keys' => array_keys($menus),
            'data' => $menus,
        ]);
    }
}
