<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menu</title>

    <style>
        :root {
            --primary: #8b1248;
            --primary-light: #f9edf3;
            --text: #151515;
            --muted: #686868;
            --border: #ead2dd;
            --bg: #ffffff;
            --card: #ffffff;
        }

        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: transparent;
            color: var(--text);
            font-family: Georgia, 'Times New Roman', serif;
        }

        .menu-wrapper {
            width: 100%;
            max-width: 1050px;
            margin: 0 auto;
            padding: 24px 18px 60px;
        }

        .menu-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 26px;
        }

        .menu-title {
            margin: 0;
            font-size: 42px;
            line-height: 1;
            letter-spacing: -1px;
        }

        .menu-updated {
            font-family: Arial, sans-serif;
            font-size: 13px;
            color: var(--muted);
        }

        .menu-tabs {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            position: sticky;
            top: 0;
            background: rgba(255,255,255,0.94);
            backdrop-filter: blur(8px);
            z-index: 20;
            padding: 12px 0;
            border-bottom: 1px solid var(--border);
            margin-bottom: 34px;
        }

        .menu-tab {
            appearance: none;
            border: 1px solid var(--primary);
            background: #fff;
            color: var(--primary);
            padding: 11px 18px;
            border-radius: 999px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: 0.2s ease;
            font-family: Arial, sans-serif;
        }

        .menu-tab:hover,
        .menu-tab.active {
            background: var(--primary);
            color: #fff;
        }

        .menu-empty {
            text-align: center;
            padding: 60px 20px;
            color: var(--muted);
            font-family: Arial, sans-serif;
        }

        .menu-category {
            margin-bottom: 52px;
            scroll-margin-top: 90px;
        }

        .category-header {
            display: flex;
            align-items: center;
            gap: 16px;
            margin-bottom: 24px;
        }

        .category-header h2 {
            margin: 0;
            font-size: 34px;
            line-height: 1.1;
            text-transform: uppercase;
            letter-spacing: -0.6px;
            white-space: nowrap;
        }

        .category-line {
            height: 1px;
            background: var(--primary);
            flex: 1;
            opacity: 0.7;
        }

        .menu-items {
            display: grid;
            grid-template-columns: 1fr;
            gap: 0;
        }

        .menu-item {
            display: grid;
            grid-template-columns: 1fr auto;
            gap: 24px;
            padding: 18px 0;
            border-bottom: 1px solid rgba(139, 18, 72, 0.16);
        }

        .menu-item:last-child {
            border-bottom: 0;
        }

        .item-name-row {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-wrap: wrap;
            margin-bottom: 7px;
        }

        .item-name {
            margin: 0;
            font-size: 19px;
            line-height: 1.25;
            font-weight: 700;
        }

        .sold-out {
            display: inline-block;
            font-family: Arial, sans-serif;
            font-size: 11px;
            letter-spacing: 0.6px;
            text-transform: uppercase;
            color: #9b1c1c;
            background: #fdecec;
            border: 1px solid #f5b8b8;
            padding: 4px 7px;
            border-radius: 999px;
        }

        .item-desc {
            margin: 0;
            color: var(--muted);
            font-size: 17px;
            line-height: 1.45;
        }

        .item-price {
            font-size: 18px;
            font-weight: 700;
            white-space: nowrap;
            padding-top: 2px;
        }

        .menu-item.unavailable {
            opacity: 0.52;
        }

        .modifiers {
            margin-top: 12px;
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }

        .modifier-pill {
            font-family: Arial, sans-serif;
            font-size: 12px;
            color: var(--muted);
            background: #f7f7f7;
            border: 1px solid #ececec;
            padding: 5px 8px;
            border-radius: 999px;
        }

        .floating-back-top {
            display: none;
            position: fixed;
            right: 16px;
            bottom: 16px;
            border: 0;
            background: var(--primary);
            color: #fff;
            width: 44px;
            height: 44px;
            border-radius: 999px;
            cursor: pointer;
            font-size: 18px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.18);
        }

        .floating-back-top.show {
            display: block;
        }

        @media (min-width: 900px) {
            .menu-items.two-col {
                grid-template-columns: 1fr 1fr;
                gap: 0 42px;
            }
        }

        @media (max-width: 700px) {
            .menu-wrapper {
                padding: 18px 14px 45px;
            }

            .menu-top {
                align-items: flex-start;
                flex-direction: column;
            }

            .menu-title {
                font-size: 34px;
            }

            .menu-tabs {
                overflow-x: auto;
                flex-wrap: nowrap;
                padding-bottom: 14px;
            }

            .menu-tab {
                flex: 0 0 auto;
                padding: 10px 15px;
            }

            .category-header h2 {
                font-size: 27px;
                white-space: normal;
            }

            .menu-item {
                grid-template-columns: 1fr;
                gap: 8px;
                padding: 16px 0;
            }

            .item-desc {
                font-size: 15px;
            }

            .item-price {
                font-size: 17px;
            }
        }
    </style>
</head>
<body>

<div class="menu-wrapper" id="top">
    <div class="menu-top">
        <h1 class="menu-title">Menu</h1>

        @if($lastSyncedAt)
            <div class="menu-updated">
                Updated {{ $lastSyncedAt->format('M d, Y h:i A') }}
            </div>
        @endif
    </div>

    @if(empty($categories))
        <div class="menu-empty">
            Menu is currently unavailable. Please check back soon.
        </div>
    @else
        <div class="menu-tabs">
            @foreach($categories as $index => $category)
                <button
                    class="menu-tab {{ $index === 0 ? 'active' : '' }}"
                    data-target="category-{{ $index }}"
                    type="button"
                >
                    {{ $category['name'] ?? 'Menu' }}
                </button>
            @endforeach
        </div>

        @foreach($categories as $index => $category)
            <section class="menu-category" id="category-{{ $index }}">
                <div class="category-header">
                    <h2>{{ $category['name'] ?? 'Menu' }}</h2>
                    <div class="category-line"></div>
                </div>

                <div class="menu-items {{ count($category['items'] ?? []) > 8 ? 'two-col' : '' }}">
                    @foreach(($category['items'] ?? []) as $item)
                        <article class="menu-item {{ !($item['available'] ?? true) ? 'unavailable' : '' }}">
                            <div class="item-content">
                                <div class="item-name-row">
                                    <h3 class="item-name">{{ $item['name'] ?? '' }}</h3>

                                    @if(!($item['available'] ?? true))
                                        <span class="sold-out">Sold Out</span>
                                    @endif
                                </div>

                                @if(!empty($item['description']))
                                    <p class="item-desc">{{ $item['description'] }}</p>
                                @endif

                                @if(!empty($item['modifiers']))
                                    <div class="modifiers">
                                        @foreach($item['modifiers'] as $modifierGroup)
                                            @foreach(($modifierGroup['items'] ?? []) as $modifier)
                                                @if(!empty($modifier['name']))
                                                    <span class="modifier-pill">
                                                        {{ $modifier['name'] }}
                                                        @if(isset($modifier['price']) && $modifier['price'] > 0)
                                                            +${{ number_format($modifier['price'], 2) }}
                                                        @endif
                                                    </span>
                                                @endif
                                            @endforeach
                                        @endforeach
                                    </div>
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
    @endif
</div>

<button class="floating-back-top" id="backTop" type="button">↑</button>

<script>
    const tabs = document.querySelectorAll('.menu-tab');
    const backTop = document.getElementById('backTop');

    tabs.forEach(function(tab) {
        tab.addEventListener('click', function() {
            tabs.forEach(function(t) {
                t.classList.remove('active');
            });

            tab.classList.add('active');

            const target = document.getElementById(tab.dataset.target);

            if (target) {
                target.scrollIntoView({
                    behavior: 'smooth',
                    block: 'start'
                });
            }
        });
    });

    window.addEventListener('scroll', function() {
        if (window.scrollY > 500) {
            backTop.classList.add('show');
        } else {
            backTop.classList.remove('show');
        }
    });

    backTop.addEventListener('click', function() {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
</script>

</body>
</html>
