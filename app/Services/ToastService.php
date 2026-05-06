<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

class ToastService
{
    protected string $baseUrl;
    protected string $clientId;
    protected string $clientSecret;
    protected string $restaurantGuid;
    protected string $userAccessType;

    public function __construct()
    {
        $this->baseUrl = rtrim(config('services.toast.base_url'), '/');
        $this->clientId = config('services.toast.client_id');
        $this->clientSecret = config('services.toast.client_secret');
        $this->restaurantGuid = config('services.toast.restaurant_guid');
        $this->userAccessType = config('services.toast.user_access_type', 'TOAST_MACHINE_CLIENT');
    }

    public function getAccessToken(): string
    {
        return Cache::remember('toast_access_token', 3600, function () {
            $response = Http::timeout(30)
                ->acceptJson()
                ->asJson()
                ->post($this->baseUrl . '/authentication/v1/authentication/login', [
                    'clientId' => $this->clientId,
                    'clientSecret' => $this->clientSecret,
                    'userAccessType' => $this->userAccessType,
                ]);

            if (!$response->successful()) {
                throw new \Exception('Toast authentication failed: ' . $response->status() . ' - ' . $response->body());
            }

            $token = $response->json('token.accessToken');

            if (!$token) {
                throw new \Exception('Toast access token missing: ' . $response->body());
            }

            return $token;
        });
    }

    public function getMetadata(): array
    {
        $response = Http::timeout(30)
            ->withToken($this->getAccessToken())
            ->acceptJson()
            ->withHeaders([
                'Toast-Restaurant-External-ID' => $this->restaurantGuid,
            ])
            ->get($this->baseUrl . '/menus/v2/metadata');

        if (!$response->successful()) {
            throw new \Exception('Toast metadata failed: ' . $response->status() . ' - ' . $response->body());
        }

        return $response->json();
    }

    public function getMenus(): array
    {
        $response = Http::timeout(60)
            ->withToken($this->getAccessToken())
            ->acceptJson()
            ->withHeaders([
                'Toast-Restaurant-External-ID' => $this->restaurantGuid,
            ])
            ->get($this->baseUrl . '/menus/v2/menus');

        if (!$response->successful()) {
            throw new \Exception('Toast menu fetch failed: ' . $response->status() . ' - ' . $response->body());
        }

        return $response->json();
    }
}
