<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu</title>

    <style>
        :root {
            --accent: #b3346b;
            --text: #111;
            --muted: #777;
            --tab-border: #e2dfe0;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #fff;
            color: var(--text);
            font-family: Georgia, 'Times New Roman', serif;
        }

        .menu-wrapper {
            width: 100%;
            margin: 0;
            padding: 0 23px 30px;
        }

        .main-menu-tabs {
            display: flex;
            align-items: flex-start;
            gap: 16px;
            margin: 0 0 24px;
        }

        .main-menu-tab {
            appearance: none;
            background: #fff;
            border: 1px solid transparent;
            color: var(--text);
            cursor: pointer;
            font: 700 16px/1 Georgia, 'Times New Roman', serif;
            min-height: 58px;
            padding: 22px 6px 14px;
        }

        .main-menu-tab.active {
            border-color: var(--tab-border);
            border-bottom: 2px solid var(--accent);
            color: var(--accent);
        }

        .menu-panel {
            display: none;
        }

        .menu-panel.active {
            display: block;
        }

        .menu-empty {
            color: var(--muted);
            font: 16px/1.45 Arial, sans-serif;
            padding: 35px 0;
        }

        .menu-category {
            margin: 0 0 12px;
        }

        .category-title {
            border-bottom: 1px solid var(--accent);
            margin: 0 0 15px;
            padding: 0 0 20px;
        }

        .category-title h2 {
            font-size: 27px;
            line-height: 1.05;
            margin: 0;
            text-transform: uppercase;
        }

        .menu-item {
            display: grid;
            grid-template-columns: minmax(0, 1fr) auto;
            gap: 18px;
            margin: 0 0 22px;
        }

        .item-name {
            font-size: 16px;
            font-weight: 700;
            line-height: 1.25;
            margin: 0 0 8px;
        }

        .item-desc {
            color: var(--muted);
            font-size: 16px;
            font-weight: 700;
            line-height: 1.35;
            margin: 0;
            white-space: pre-line;
        }

        .item-price {
            font-size: 16px;
            font-weight: 700;
            line-height: 1.25;
            padding-top: 1px;
            white-space: nowrap;
        }

        @media (max-width: 700px) {
            .menu-wrapper {
                padding-left: 16px;
                padding-right: 16px;
            }

            .main-menu-tabs {
                gap: 12px;
                margin-bottom: 22px;
            }

            .category-title h2 {
                font-size: 25px;
            }

            .menu-item {
                gap: 12px;
            }

            .item-name,
            .item-desc,
            .item-price {
                font-size: 15px;
            }
        }
    </style>
</head>
<body>
    <div class="menu-wrapper">
        @if(empty($menus))
            <div class="menu-empty">
                Menu is currently unavailable. Please check back soon.
            </div>
        @else
            <div class="main-menu-tabs">
                @foreach($menus as $menuIndex => $menu)
                    <button
                        class="main-menu-tab {{ $menuIndex === 0 ? 'active' : '' }}"
                        data-menu-target="menu-panel-{{ $menuIndex }}"
                        type="button"
                    >
                        {{ $menu['name'] ?? 'Menu' }}
                    </button>
                @endforeach
            </div>

            @foreach($menus as $menuIndex => $menu)
                <div class="menu-panel {{ $menuIndex === 0 ? 'active' : '' }}" id="menu-panel-{{ $menuIndex }}">
                    @foreach(($menu['categories'] ?? []) as $categoryIndex => $category)
                        <section class="menu-category" id="menu-{{ $menuIndex }}-category-{{ $categoryIndex }}">
                            <header class="category-title">
                                <h2>{{ $category['name'] ?? 'Menu' }}</h2>
                            </header>

                            <div class="menu-items">
                                @foreach(($category['items'] ?? []) as $item)
                                    <article class="menu-item">
                                        <div class="item-content">
                                            <h3 class="item-name">{{ $item['name'] ?? '' }}</h3>

                                            @if(!empty($item['description']))
                                                <p class="item-desc">{{ $item['description'] }}</p>
                                            @endif
                                        </div>

                                        <div class="item-price">
                                            @if(isset($item['price']) && $item['price'] !== null)
                                                ${{ number_format($item['price'], 2) }}
                                            @endif
                                        </div>
                                    </article>
                                @endforeach
                            </div>
                        </section>
                    @endforeach
                </div>
            @endforeach
        @endif
    </div>

    <script>
        const mainTabs = document.querySelectorAll('.main-menu-tab');
        const panels = document.querySelectorAll('.menu-panel');

        mainTabs.forEach(function(tab) {
            tab.addEventListener('click', function() {
                mainTabs.forEach(function(t) {
                    t.classList.remove('active');
                });

                panels.forEach(function(panel) {
                    panel.classList.remove('active');
                });

                tab.classList.add('active');

                const targetPanel = document.getElementById(tab.dataset.menuTarget);

                if (targetPanel) {
                    targetPanel.classList.add('active');
                    window.scrollTo(0, 0);
                }
            });
        });
    </script>
</body>
</html>
