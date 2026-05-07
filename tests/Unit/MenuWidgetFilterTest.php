<?php

namespace Tests\Unit;

use App\Models\ToastMenuWidgetSetting;
use App\Services\MenuWidgetFilter;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MenuWidgetFilterTest extends TestCase
{
    use RefreshDatabase;

    public function test_it_filters_widget_menu_by_allowed_names_and_guids(): void
    {
        config([
            'toast_menu.widget.allowed_menu_names' => ['Kitchen'],
            'toast_menu.widget.allowed_menu_guids' => [],
            'toast_menu.widget.allowed_category_names' => [],
            'toast_menu.widget.allowed_category_guids' => ['cat-breakfast'],
            'toast_menu.widget.allowed_item_names' => [],
            'toast_menu.widget.allowed_item_guids' => ['item-omelette'],
        ]);

        $filtered = app(MenuWidgetFilter::class)->filter([
            'menus' => [
                [
                    'guid' => 'menu-kitchen',
                    'name' => 'Kitchen',
                    'categories' => [
                        [
                            'guid' => 'cat-breakfast',
                            'name' => 'Breakfast',
                            'items' => [
                                ['guid' => 'item-omelette', 'name' => 'Omelette'],
                                ['guid' => 'item-toast', 'name' => 'Toast'],
                            ],
                        ],
                        [
                            'guid' => 'cat-lunch',
                            'name' => 'Lunch',
                            'items' => [
                                ['guid' => 'item-burger', 'name' => 'Burger'],
                            ],
                        ],
                    ],
                ],
                [
                    'guid' => 'menu-bar',
                    'name' => 'Bar',
                    'categories' => [
                        [
                            'guid' => 'cat-drinks',
                            'name' => 'Drinks',
                            'items' => [
                                ['guid' => 'item-soda', 'name' => 'Soda'],
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $this->assertSame(['Kitchen'], array_column($filtered['menus'], 'name'));
        $this->assertSame(['Breakfast'], array_column($filtered['menus'][0]['categories'], 'name'));
        $this->assertSame(['Omelette'], array_column($filtered['menus'][0]['categories'][0]['items'], 'name'));
        $this->assertSame($filtered['menus'][0]['categories'], $filtered['categories']);
    }

    public function test_empty_allow_lists_keep_that_level_open(): void
    {
        config([
            'toast_menu.widget.allowed_menu_names' => ['Kitchen'],
            'toast_menu.widget.allowed_menu_guids' => [],
            'toast_menu.widget.allowed_category_names' => [],
            'toast_menu.widget.allowed_category_guids' => [],
            'toast_menu.widget.allowed_item_names' => [],
            'toast_menu.widget.allowed_item_guids' => [],
        ]);

        $filtered = app(MenuWidgetFilter::class)->filter([
            'menus' => [
                [
                    'guid' => 'menu-kitchen',
                    'name' => 'Kitchen',
                    'categories' => [
                        [
                            'guid' => 'cat-breakfast',
                            'name' => 'Breakfast',
                            'items' => [
                                ['guid' => 'item-omelette', 'name' => 'Omelette'],
                                ['guid' => 'item-toast', 'name' => 'Toast'],
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $this->assertCount(2, $filtered['menus'][0]['categories'][0]['items']);
    }

    public function test_saved_admin_selection_overrides_config_allow_lists(): void
    {
        config([
            'toast_menu.widget.allowed_menu_names' => ['Kitchen'],
            'toast_menu.widget.allowed_menu_guids' => [],
            'toast_menu.widget.allowed_category_names' => [],
            'toast_menu.widget.allowed_category_guids' => [],
            'toast_menu.widget.allowed_item_names' => [],
            'toast_menu.widget.allowed_item_guids' => [],
        ]);

        ToastMenuWidgetSetting::create([
            'allowed_menu_guids' => ['menu-bar'],
            'allowed_category_guids' => ['cat-drinks'],
            'allowed_item_guids' => ['item-soda'],
        ]);

        $filtered = app(MenuWidgetFilter::class)->filter([
            'menus' => [
                [
                    'guid' => 'menu-kitchen',
                    'name' => 'Kitchen',
                    'categories' => [
                        [
                            'guid' => 'cat-food',
                            'name' => 'Food',
                            'items' => [
                                ['guid' => 'item-burger', 'name' => 'Burger'],
                            ],
                        ],
                    ],
                ],
                [
                    'guid' => 'menu-bar',
                    'name' => 'Bar',
                    'categories' => [
                        [
                            'guid' => 'cat-drinks',
                            'name' => 'Drinks',
                            'items' => [
                                ['guid' => 'item-soda', 'name' => 'Soda'],
                                ['guid' => 'item-tea', 'name' => 'Tea'],
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $this->assertSame(['Bar'], array_column($filtered['menus'], 'name'));
        $this->assertSame(['Drinks'], array_column($filtered['menus'][0]['categories'], 'name'));
        $this->assertSame(['Soda'], array_column($filtered['menus'][0]['categories'][0]['items'], 'name'));
    }

    public function test_unselected_parent_menu_hides_selected_child_items(): void
    {
        ToastMenuWidgetSetting::create([
            'allowed_menu_guids' => [],
            'allowed_category_guids' => [],
            'allowed_item_guids' => ['item-omelette'],
        ]);

        $filtered = app(MenuWidgetFilter::class)->filter([
            'menus' => [
                [
                    'guid' => null,
                    'name' => 'Kitchen',
                    'categories' => [
                        [
                            'guid' => null,
                            'name' => 'Breakfast',
                            'items' => [
                                ['guid' => 'item-omelette', 'name' => 'Omelette'],
                                ['guid' => 'item-toast', 'name' => 'Toast'],
                            ],
                        ],
                    ],
                ],
            ],
        ]);

        $this->assertSame([], $filtered['menus']);
        $this->assertSame([], $filtered['categories']);
    }
}
