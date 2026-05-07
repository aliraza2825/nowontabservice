<?php

$csv = static function (?string $value): array {
    if ($value === null || trim($value) === '') {
        return [];
    }

    return array_values(array_filter(array_map('trim', explode(',', $value))));
};

return [
    'widget' => [
        'allowed_menu_names' => $csv(env('TOAST_WIDGET_ALLOWED_MENU_NAMES', 'Kitchen,Bar')),
        'allowed_menu_guids' => $csv(env('TOAST_WIDGET_ALLOWED_MENU_GUIDS')),
        'allowed_category_names' => $csv(env('TOAST_WIDGET_ALLOWED_CATEGORY_NAMES')),
        'allowed_category_guids' => $csv(env('TOAST_WIDGET_ALLOWED_CATEGORY_GUIDS')),
        'allowed_item_names' => $csv(env('TOAST_WIDGET_ALLOWED_ITEM_NAMES')),
        'allowed_item_guids' => $csv(env('TOAST_WIDGET_ALLOWED_ITEM_GUIDS')),
    ],
];
