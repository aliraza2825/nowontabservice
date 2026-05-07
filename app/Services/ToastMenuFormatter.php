<?php

namespace App\Services;

class ToastMenuFormatter
{
    public function format(array $rawMenu): array
    {
        $menus = [];

        foreach (($rawMenu['menus'] ?? []) as $menu) {
            $menuName = $menu['name'] ?? 'Menu';

            $categories = [];

            foreach (($menu['menuGroups'] ?? []) as $group) {
                $items = $this->extractItemsFromGroup($group);

                if (! empty($items)) {
                    $categories[] = [
                        'guid' => $group['guid'] ?? null,
                        'name' => $group['name'] ?? 'Menu',
                        'items' => $items,
                    ];
                }
            }

            if (! empty($categories)) {
                $menus[] = [
                    'guid' => $menu['guid'] ?? null,
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

        if (isset($item['isAvailable']) && ! $item['isAvailable']) {
            return false;
        }

        return true;
    }
}
