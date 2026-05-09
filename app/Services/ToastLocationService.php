<?php

namespace App\Services;

class ToastLocationService
{
    public function locations(): array
    {
        return array_values(array_filter(
            config('services.toast.locations', []),
            fn (array $location): bool => ! empty($location['guid'])
        ));
    }

    public function default(): array
    {
        $defaultGuid = config('services.toast.restaurant_guid');

        foreach ($this->locations() as $location) {
            if (($location['guid'] ?? null) === $defaultGuid) {
                return $location;
            }
        }

        return $this->locations()[0] ?? [
            'name' => 'Location',
            'guid' => $defaultGuid,
        ];
    }

    public function find(?string $guid): array
    {
        foreach ($this->locations() as $location) {
            if (($location['guid'] ?? null) === $guid) {
                return $location;
            }
        }

        return $this->default();
    }
}
