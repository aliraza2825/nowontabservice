<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu Widget Admin</title>
    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #f7f5ef;
            color: #222;
            font-family: Arial, sans-serif;
        }

        .page {
            max-width: 1180px;
            margin: 0 auto;
            padding: 28px 18px 44px;
        }

        .topbar {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 22px;
        }

        h1 {
            margin: 0 0 8px;
            font-size: 28px;
        }

        .muted {
            color: #6f6a60;
            font-size: 14px;
        }

        .actions {
            display: flex;
            gap: 10px;
            align-items: center;
        }

        .location-tabs {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            margin: 0 0 18px;
        }

        .location-tab {
            background: #fff;
            border: 1px solid #d8d2c6;
            border-radius: 6px;
            color: #222;
            display: inline-flex;
            flex-direction: column;
            gap: 3px;
            min-width: 220px;
            padding: 10px 12px;
            text-decoration: none;
        }

        .location-tab.active {
            background: #2c2924;
            border-color: #2c2924;
            color: #fff;
        }

        .location-name {
            font-size: 14px;
            font-weight: 700;
        }

        .location-guid {
            color: inherit;
            font-size: 11px;
            opacity: .72;
            overflow-wrap: anywhere;
        }

        .button {
            border: 0;
            border-radius: 6px;
            background: #111;
            color: #fff;
            cursor: pointer;
            display: inline-block;
            font-size: 14px;
            font-weight: 700;
            padding: 11px 16px;
            text-decoration: none;
        }

        .button.secondary {
            background: #fff;
            border: 1px solid #d8d2c6;
            color: #222;
        }

        .button.sync {
            background: #6f4f1f;
        }

        .notice {
            background: #eaf7ef;
            border: 1px solid #b9dfc5;
            border-radius: 6px;
            margin-bottom: 18px;
            padding: 12px 14px;
        }

        .notice.error {
            background: #fff0ee;
            border-color: #efb7ad;
        }

        .empty {
            background: #fff;
            border: 1px solid #ddd5c8;
            border-radius: 6px;
            padding: 20px;
        }

        .menu-block {
            background: #fff;
            border: 1px solid #ddd5c8;
            border-radius: 6px;
            margin-bottom: 16px;
            overflow: hidden;
        }

        .menu-block[open] {
            border-color: #c7bda9;
        }

        .menu-head,
        .category-head,
        .item-row {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .menu-head {
            background: #2c2924;
            color: #fff;
            cursor: pointer;
            list-style: none;
            padding: 14px 16px;
        }

        .menu-head::-webkit-details-marker,
        .category-head::-webkit-details-marker {
            display: none;
        }

        .chevron {
            display: inline-flex;
            align-items: center;
            color: inherit;
            font-size: 18px;
            justify-content: center;
            line-height: 1;
            transition: transform 160ms ease;
            width: 18px;
        }

        .menu-block[open] > .menu-head .chevron,
        .category[open] > .category-head .chevron {
            transform: rotate(90deg);
        }

        .count {
            color: inherit;
            font-size: 13px;
            opacity: .72;
            white-space: nowrap;
        }

        .category {
            border-top: 1px solid #eee8dc;
            padding: 0 16px;
        }

        .category[open] {
            padding-bottom: 12px;
        }

        .category-head {
            cursor: pointer;
            list-style: none;
            padding: 14px 0 10px;
            font-weight: 700;
        }

        .items {
            display: grid;
            gap: 8px;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
        }

        .item-row {
            background: #fbfaf6;
            border: 1px solid #ece5d8;
            border-radius: 6px;
            min-height: 44px;
            padding: 9px 10px;
        }

        input[type="checkbox"] {
            height: 18px;
            width: 18px;
        }

        .name {
            flex: 1;
            min-width: 0;
        }

        .price {
            color: #766f63;
            font-size: 13px;
            white-space: nowrap;
        }

        @media (max-width: 700px) {
            .topbar {
                display: block;
            }

            .actions {
                margin-top: 14px;
            }
        }
    </style>
</head>
<body>
    <main class="page">
        <div class="topbar">
            <div>
                <h1>Menu Widget Admin</h1>
                <div class="muted">
                    @if($lastSyncedAt)
                        Last synced {{ $lastSyncedAt->format('M d, Y h:i A') }}
                    @else
                        Menu has not been synced yet.
                    @endif
                </div>
            </div>

            <div class="actions">
                <a class="button secondary" href="{{ url('/menu-widget') }}?location={{ urlencode($currentLocation['guid']) }}" target="_blank">View Widget</a>
                <button class="button sync" form="fetch-new-menu" type="submit">Fetch New Menu</button>
                <button class="button" form="widget-settings" type="submit">Save Selection</button>
                <button class="button secondary" form="admin-logout" type="submit">Logout</button>
            </div>
        </div>

        @if(session('status'))
            <div class="notice">{{ session('status') }}</div>
        @endif

        @if(session('error'))
            <div class="notice error">{{ session('error') }}</div>
        @endif

        <nav class="location-tabs" aria-label="Locations">
            @foreach($locations as $location)
                <a
                    class="location-tab {{ $location['guid'] === $currentLocation['guid'] ? 'active' : '' }}"
                    href="{{ route('menu-widget.admin.edit', ['location' => $location['guid']]) }}"
                >
                    <span class="location-name">{{ $location['name'] }}</span>
                    <span class="location-guid">{{ $location['guid'] }}</span>
                </a>
            @endforeach
        </nav>

        <form id="fetch-new-menu" method="POST" action="{{ route('menu-widget.admin.fetch') }}">
            @csrf
            <input type="hidden" name="location_guid" value="{{ $currentLocation['guid'] }}">
        </form>

        <form id="admin-logout" method="POST" action="{{ route('menu-widget.admin.logout') }}">
            @csrf
        </form>

        @if(empty($menus))
            <div class="empty">No synced menu found. Run force sync first, then come back here.</div>
        @else
            <form id="widget-settings" method="POST" action="{{ route('menu-widget.admin.update') }}">
                @csrf
                <input type="hidden" name="location_guid" value="{{ $currentLocation['guid'] }}">

                @foreach($menus as $menu)
                    @php
                        $menuName = $menu['name'] ?? 'Menu';
                        $menuGuid = $menu['guid'] ?? 'menu:'.$menuName;
                        $menuSelected = in_array($menuGuid, $allowedMenuGuids, true);
                        $menuCategories = $menu['categories'] ?? [];
                        $menuItemsCount = collect($menuCategories)->sum(fn ($category) => count($category['items'] ?? []));
                    @endphp

                    <details class="menu-block" @open($menuSelected)>
                        <summary class="menu-head">
                            <span class="chevron">›</span>
                            <input
                                type="checkbox"
                                name="menus[]"
                                value="{{ $menuGuid }}"
                                @checked($menuSelected)
                            >
                            <span class="name">{{ $menuName }}</span>
                            <span class="count">{{ count($menuCategories) }} categories · {{ $menuItemsCount }} items</span>
                        </summary>

                        @foreach($menuCategories as $category)
                            @php
                                $categoryName = $category['name'] ?? 'Category';
                                $categoryGuid = $category['guid'] ?? 'category:'.$menuName.'|'.$categoryName;
                                $categorySelected = in_array($categoryGuid, $allowedCategoryGuids, true);
                                $categoryItems = $category['items'] ?? [];
                            @endphp

                            <details class="category" @open($categorySelected)>
                                <summary class="category-head">
                                    <span class="chevron">›</span>
                                    <input
                                        type="checkbox"
                                        name="categories[]"
                                        value="{{ $categoryGuid }}"
                                        @checked($categorySelected)
                                    >
                                    <span class="name">{{ $categoryName }}</span>
                                    <span class="count">{{ count($categoryItems) }} items</span>
                                </summary>

                                <div class="items">
                                    @foreach($categoryItems as $item)
                                        @php
                                            $itemName = $item['name'] ?? 'Item';
                                            $itemGuid = $item['guid'] ?? 'item:'.$menuName.'|'.$categoryName.'|'.$itemName;
                                        @endphp

                                        <label class="item-row">
                                            <input
                                                type="checkbox"
                                                name="items[]"
                                                value="{{ $itemGuid }}"
                                                @checked(in_array($itemGuid, $allowedItemGuids, true))
                                            >
                                            <span class="name">{{ $itemName }}</span>
                                            @if(isset($item['price']) && $item['price'] !== null)
                                                <span class="price">${{ number_format($item['price'], 2) }}</span>
                                            @endif
                                        </label>
                                    @endforeach
                                </div>
                            </details>
                        @endforeach
                    </details>
                @endforeach
            </form>
        @endif
    </main>
</body>
</html>
