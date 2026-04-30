@php
    $cartCount = 0;
    if (auth()->check()) {
        $cart = auth()->user()->cart()->with('items')->first();
        $cartCount = $cart ? (int) $cart->items->sum('quantity') : 0;
    }

    $isHome = request()->routeIs('home');
    $isCatalog = request()->routeIs('catalog.*');
    $initial = auth()->check() ? mb_strtoupper(mb_substr(auth()->user()->full_name, 0, 1)) : '';
@endphp

<header class="cb-navbar-shell">
    <nav class="cb-nav">
        <a href="{{ route('home') }}" class="cb-logo">Cat<span>Book</span></a>

        <div class="cb-nav-links">
            <a href="{{ route('home') }}" class="{{ $isHome ? 'active' : '' }}">Trang chủ</a>
            <a href="{{ route('catalog.categories') }}" class="{{ $isCatalog ? 'active' : '' }}">Danh mục</a>
            <a href="#">Tác giả</a>
            <a href="#">Giới thiệu</a>
        </div>

        <div class="cb-nav-right">
            <a href="{{ route('cart.index') }}" class="cb-cart-icon" title="Giỏ hàng">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/>
                    <line x1="3" y1="6" x2="21" y2="6"/>
                    <path d="M16 10a4 4 0 01-8 0"/>
                </svg>
                @auth
                    @if($cartCount > 0)
                        <span class="cb-cart-badge">{{ $cartCount > 99 ? '99+' : $cartCount }}</span>
                    @endif
                @endauth
            </a>

            @guest
                <a href="{{ route('login') }}" class="cb-btn-ghost">Đăng nhập</a>
                <a href="{{ route('register') }}" class="cb-btn-solid">Đăng ký</a>
            @else
                <div class="cb-user-menu" id="cb-user-menu">
                    <button type="button" class="cb-user-trigger" onclick="document.getElementById('cb-user-menu').classList.toggle('active')">
                        {{ auth()->user()->full_name }}
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <polyline points="6 9 12 15 18 9"></polyline>
                        </svg>
                    </button>
                    <div class="cb-user-dropdown">
                        <a href="{{ route('account.show') }}">Trang cá nhân</a>
                        @if(auth()->user()->role === 'admin')
                            <a href="{{ route('admin.panel') }}">Admin Dashboard</a>
                        @elseif(auth()->user()->role === 'staff')
                            <a href="{{ route('staff.panel') }}">Staff Dashboard</a>
                        @endif
                        <div class="cb-user-dropdown-divider"></div>
                        <form method="POST" action="{{ route('logout') }}" style="margin:0">
                            @csrf
                            <button type="submit" class="cb-logout-btn">Đăng xuất</button>
                        </form>
                    </div>
                </div>
            @endguest
        </div>

        <button class="cb-hamburger" id="cb-menu-toggle" aria-label="Mở menu" type="button">
            <span></span><span></span><span></span>
        </button>
    </nav>

    <div class="cb-mobile-drawer" id="cb-mobile-drawer">
        <a href="{{ route('home') }}" class="cb-mobile-link">Trang chủ</a>
        <a href="{{ route('catalog.categories') }}" class="cb-mobile-link">Danh mục</a>
        <a href="#" class="cb-mobile-link">Tác giả</a>
        <a href="#" class="cb-mobile-link">Giới thiệu</a>
        <a href="{{ route('cart.index') }}" class="cb-mobile-link">
            Giỏ hàng @if($cartCount > 0) <span class="cb-mobile-badge">{{ $cartCount }}</span> @endif
        </a>
        @auth
            <a href="{{ route('account.show') }}" class="cb-mobile-link">Tài khoản</a>
            @if(auth()->user()->role === 'admin')
                <a href="{{ route('admin.panel') }}" class="cb-mobile-link">Admin Dashboard</a>
            @elseif(auth()->user()->role === 'staff')
                <a href="{{ route('staff.panel') }}" class="cb-mobile-link">Staff Dashboard</a>
            @endif
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="cb-mobile-link cb-mobile-link--danger" style="width:100%;text-align:left">
                    Đăng xuất
                </button>
            </form>
        @else
            <a href="{{ route('login') }}" class="cb-mobile-link">Đăng nhập</a>
            <a href="{{ route('register') }}" class="cb-mobile-link cb-mobile-link--cta">Đăng ký</a>
        @endauth
    </div>
</header>

<style>
    .cb-navbar-shell {
        position: sticky;
        top: 0;
        z-index: 50;
    }

    .cb-nav {
        background: rgba(248,246,241,0.92);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
        border-bottom: 1px solid #e8e3d8;
        padding: 0 40px;
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 16px;
        height: 64px;
    }

    .cb-logo {
        font-family: 'Playfair Display', serif;
        font-size: 22px;
        font-weight: 900;
        color: #1a1a1a;
        letter-spacing: -0.5px;
        text-decoration: none;
        white-space: nowrap;
    }

    .cb-logo span { color: #2d6a4f; }

    .cb-nav-links {
        display: flex;
        gap: 28px;
        align-items: center;
    }

    .cb-nav-links a {
        font-size: 14px;
        font-weight: 500;
        color: #555;
        text-decoration: none;
        transition: color 0.2s;
        white-space: nowrap;
    }

    .cb-nav-links a:hover { color: #1a1a1a; }
    .cb-nav-links a.active { color: #2d6a4f; }

    .cb-nav-right {
        display: flex;
        gap: 10px;
        align-items: center;
        flex-shrink: 0;
    }

    .cb-cart-icon {
        width: 38px;
        height: 38px;
        border-radius: 50%;
        border: 1.5px solid #ddd;
        background: transparent;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: border-color 0.2s;
        position: relative;
        text-decoration: none;
        color: #1a1a1a;
        flex-shrink: 0;
    }

    .cb-cart-icon:hover { border-color: #1a1a1a; }

    .cb-cart-badge {
        position: absolute;
        top: -5px;
        right: -5px;
        background: #2d6a4f;
        color: #fff;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        font-size: 10px;
        font-weight: 700;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .cb-btn-ghost {
        font-family: 'DM Sans', sans-serif;
        font-size: 13px;
        font-weight: 500;
        padding: 8px 18px;
        border-radius: 999px;
        border: 1.5px solid #ccc;
        background: transparent;
        color: #333;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        white-space: nowrap;
    }

    .cb-btn-ghost:hover { border-color: #1a1a1a; color: #1a1a1a; }

    .cb-btn-solid {
        font-family: 'DM Sans', sans-serif;
        font-size: 13px;
        font-weight: 600;
        padding: 8px 20px;
        border-radius: 999px;
        border: none;
        background: #1a1a1a;
        color: #fff;
        cursor: pointer;
        transition: all 0.2s;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        white-space: nowrap;
    }

    .cb-btn-solid:hover { background: #2d6a4f; }

    .cb-user-menu {
        position: relative;
        display: inline-block;
    }

    .cb-user-trigger {
        font-family: 'DM Sans', sans-serif;
        font-size: 13px;
        font-weight: 500;
        padding: 8px 14px;
        border-radius: 999px;
        background: transparent;
        border: none;
        color: #333;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 6px;
        white-space: nowrap;
    }

    .cb-user-trigger:hover { color: #2d6a4f; }

    .cb-user-dropdown {
        position: absolute;
        top: 100%;
        right: 0;
        background: #fff;
        border: 1px solid #e0dbd0;
        border-radius: 12px;
        box-shadow: 0 8px 24px rgba(0,0,0,0.12);
        min-width: 200px;
        margin-top: 8px;
        display: none;
        flex-direction: column;
        z-index: 100;
        opacity: 0;
        transition: opacity 0.2s, visibility 0.2s;
        visibility: hidden;
    }

    .cb-user-menu.active .cb-user-dropdown {
        display: flex;
        opacity: 1;
        visibility: visible;
    }

    .cb-user-dropdown a,
    .cb-user-dropdown button {
        padding: 12px 16px;
        text-align: left;
        border: none;
        background: transparent;
        cursor: pointer;
        font-family: 'DM Sans', sans-serif;
        font-size: 13px;
        font-weight: 500;
        color: #333;
        text-decoration: none;
        transition: background 0.15s, color 0.15s;
    }

    .cb-user-dropdown a:hover,
    .cb-user-dropdown button:hover {
        background: #f8f6f1;
        color: #2d6a4f;
    }

    .cb-user-dropdown a:first-child { border-radius: 12px 12px 0 0; }
    .cb-user-dropdown a:last-child,
    .cb-user-dropdown button:last-child { border-radius: 0 0 12px 12px; }

    .cb-user-dropdown-divider {
        height: 1px;
        background: #e8e3d8;
        margin: 4px 0;
    }

    .cb-logout-btn {
        color: #d32f2f;
        font-weight: 600;
    }

    .cb-logout-btn:hover {
        background: #ffebee;
        color: #d32f2f;
    }

    .cb-hamburger {
        display: none;
        flex-direction: column;
        justify-content: center;
        gap: 5px;
        width: 36px;
        height: 36px;
        border: 1.5px solid #ddd;
        border-radius: 8px;
        background: transparent;
        cursor: pointer;
        padding: 8px;
        flex-shrink: 0;
        transition: border-color 0.2s;
    }

    .cb-hamburger:hover { border-color: #1a1a1a; }

    .cb-hamburger span {
        display: block;
        height: 2px;
        background: #1a1a1a;
        border-radius: 2px;
        transition: transform 0.25s, opacity 0.25s;
    }

    .cb-hamburger.open span:nth-child(1) { transform: translateY(7px) rotate(45deg); }
    .cb-hamburger.open span:nth-child(2) { opacity: 0; }
    .cb-hamburger.open span:nth-child(3) { transform: translateY(-7px) rotate(-45deg); }

    .cb-mobile-drawer {
        display: none;
        flex-direction: column;
        background: #f8f6f1;
        border-bottom: 1px solid #e8e3d8;
        padding: 8px 16px 16px;
    }

    .cb-mobile-drawer.open { display: flex; }

    .cb-mobile-link {
        font-family: 'DM Sans', sans-serif;
        font-size: 14px;
        font-weight: 500;
        color: #444;
        text-decoration: none;
        padding: 12px 8px;
        border-bottom: 1px solid #f0ede6;
        display: flex;
        align-items: center;
        justify-content: space-between;
        background: transparent;
        border-left: none;
        border-right: none;
        border-top: none;
        cursor: pointer;
        transition: color 0.2s;
    }

    .cb-mobile-link:last-child { border-bottom: none; }
    .cb-mobile-link:hover { color: #2d6a4f; }
    .cb-mobile-link--danger { color: #dc2626; }
    .cb-mobile-link--danger:hover { color: #b91c1c; }
    .cb-mobile-link--cta {
        margin-top: 8px;
        background: #2d6a4f;
        color: #fff;
        border-radius: 10px;
        justify-content: center;
        padding: 12px;
        border: none;
    }
    .cb-mobile-link--cta:hover { background: #1b4332; color: #fff; }

    .cb-mobile-badge {
        background: #2d6a4f;
        color: #fff;
        font-size: 10px;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 999px;
    }

    @media (max-width: 860px) {
        .cb-nav-links { display: none; }
        .cb-logout-btn { padding: 7px 10px; }
        .cb-hamburger { display: flex; }
    }

    @media (max-width: 600px) {
        .cb-nav { padding: 0 16px; }
        .cb-user-menu { display: none; }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const toggle = document.getElementById('cb-menu-toggle');
    const drawer = document.getElementById('cb-mobile-drawer');
    if (toggle && drawer) {
        toggle.addEventListener('click', function () {
            const open = drawer.classList.toggle('open');
            toggle.classList.toggle('open', open);
            toggle.setAttribute('aria-expanded', open);
        });

        document.addEventListener('click', function (e) {
            if (!toggle.contains(e.target) && !drawer.contains(e.target)) {
                drawer.classList.remove('open');
                toggle.classList.remove('open');
            }
        });
    }

    document.addEventListener('click', function (e) {
        const userMenu = document.getElementById('cb-user-menu');
        if (userMenu && !userMenu.contains(e.target)) {
            userMenu.classList.remove('active');
        }
    });
});
</script>
