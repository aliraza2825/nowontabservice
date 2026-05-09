<?php

namespace App\Services;

use App\Models\ToastMenuWidgetSetting;

class MenuWidgetFilter
{
    private ?ToastMenuWidgetSetting $settings = null;

    private ?string $locationGuid = null;

    public function filter(array $formattedMenu, ?string $locationGuid = null): array
    {
        $this->locationGuid = $locationGuid;
        $this->settings = null;

        if ($this->currentSettings()) {
            return $this->filterUsingSavedSettings($formattedMenu);
        }

        $menus = [];

        foreach (($formattedMenu['menus'] ?? []) as $menu) {
            if (! $this->isAllowed($menu, 'menu')) {
                continue;
            }

            $categories = [];

            foreach (($menu['categories'] ?? []) as $category) {
                if (! $this->isAllowed($category, 'category')) {
                    continue;
                }

                $items = array_values(array_filter(
                    $category['items'] ?? [],
                    fn (array $item): bool => $this->isAllowed($item, 'item')
                ));

                if ($items === []) {
                    continue;
                }

                $category['items'] = $items;
                $categories[] = $category;
            }

            if ($categories === []) {
                continue;
            }

            $menu['categories'] = $categories;
            $menus[] = $menu;
        }

        return [
            'menus' => $menus,
            'categories' => $menus[0]['categories'] ?? [],
        ];
    }

    private function filterUsingSavedSettings(array $formattedMenu): array
    {
        $menus = [];
        $allowedMenuIds = $this->normalizedSettingGuids('menu');
        $allowedCategoryIds = $this->normalizedSettingGuids('category');
        $allowedItemIds = $this->normalizedSettingGuids('item');

        foreach (($formattedMenu['menus'] ?? []) as $menu) {
            $menuSelected = $this->matchesAny($this->menuIds($menu), $allowedMenuIds);

            if (! $menuSelected) {
                continue;
            }

            $categories = [];

            foreach (($menu['categories'] ?? []) as $category) {
                $categorySelected = $this->matchesAny($this->categoryIds($menu, $category), $allowedCategoryIds);

                if (! $categorySelected) {
                    continue;
                }

                if ($allowedItemIds !== []) {
                    $items = array_values(array_filter(
                        $category['items'] ?? [],
                        fn (array $item): bool => $this->matchesAny($this->itemIds($menu, $category, $item), $allowedItemIds)
                    ));
                } else {
                    $items = $category['items'] ?? [];
                }

                if ($items === []) {
                    continue;
                }

                $category['items'] = $items;
                $categories[] = $category;
            }

            if ($categories === []) {
                continue;
            }

            $menu['categories'] = $categories;
            $menus[] = $menu;
        }

        return [
            'menus' => $menus,
            'categories' => $menus[0]['categories'] ?? [],
        ];
    }

    private function isAllowed(array $entity, string $type): bool
    {
        $settings = $this->currentSettings();
        $allowedNames = $settings ? [] : $this->normalizedConfig("toast_menu.widget.allowed_{$type}_names");
        $allowedGuids = $settings
            ? $this->normalizedSettingGuids($type)
            : $this->normalizedConfig("toast_menu.widget.allowed_{$type}_guids");

        if ($allowedNames === [] && $allowedGuids === []) {
            return $settings === null;
        }

        $name = $this->normalize($entity['name'] ?? null);
        $guid = $this->normalize($entity['guid'] ?? null);

        return ($name !== null && in_array($name, $allowedNames, true))
            || ($guid !== null && in_array($guid, $allowedGuids, true));
    }

    private function normalizedConfig(string $key): array
    {
        return array_values(array_filter(array_map(
            fn ($value): ?string => $this->normalize($value),
            config($key, [])
        )));
    }

    private function currentSettings(): ?ToastMenuWidgetSetting
    {
        return $this->settings ??= ToastMenuWidgetSetting::current($this->locationGuid);
    }

    private function normalizedSettingGuids(string $type): array
    {
        $settings = $this->currentSettings();

        if (! $settings) {
            return [];
        }

        return array_values(array_filter(array_map(
            fn ($value): ?string => $this->normalize($value),
            $settings->getAttribute("allowed_{$type}_guids") ?? []
        )));
    }

    private function menuIds(array $menu): array
    {
        return $this->normalizeIds([
            $menu['guid'] ?? null,
            $menu['_widget_id'] ?? null,
            'menu:'.($menu['name'] ?? 'Menu'),
        ]);
    }

    private function categoryIds(array $menu, array $category): array
    {
        return $this->normalizeIds([
            $category['guid'] ?? null,
            $category['_widget_id'] ?? null,
            'category:'.($menu['name'] ?? 'Menu').'|'.($category['name'] ?? 'Category'),
        ]);
    }

    private function itemIds(array $menu, array $category, array $item): array
    {
        return $this->normalizeIds([
            $item['guid'] ?? null,
            $item['_widget_id'] ?? null,
            'item:'.($menu['name'] ?? 'Menu').'|'.($category['name'] ?? 'Category').'|'.($item['name'] ?? 'Item'),
        ]);
    }

    private function normalizeIds(array $ids): array
    {
        return array_values(array_filter(array_map(
            fn ($value): ?string => $this->normalize($value),
            $ids
        )));
    }

    private function matchesAny(array $candidateIds, array $allowedIds): bool
    {
        return array_intersect($candidateIds, $allowedIds) !== [];
    }

    private function normalize(mixed $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $value = mb_strtolower(trim((string) $value));

        return $value === '' ? null : $value;
    }
}
