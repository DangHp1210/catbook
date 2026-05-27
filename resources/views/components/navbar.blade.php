@php
/*
|--------------------------------------------------------------------------
| CatBook — components/navbar.blade.php
| Dùng: <x-navbar />
|
| CSS variables dùng bộ token --cb-* đồng bộ với admin navbar.
| Tất cả trang chỉ cần dùng component là có đầy đủ design tokens.
|--------------------------------------------------------------------------
*/
$cartCount = 0;
if (auth()->check()) {
    $cart = auth()->user()->cart()->with('items')->first();
    $cartCount = $cart ? (int) $cart->items->sum('quantity') : 0;
}

$isHome    = request()->routeIs('home');
$isCatalog = request()->routeIs('catalog.*') || request()->routeIs('admin.categories*');
$isOrders  = request()->routeIs('orders.*');
$isAccount = request()->routeIs('account.*');

$initial = auth()->check()
    ? mb_strtoupper(mb_substr(auth()->user()->full_name, 0, 1))
    : '';

$avatarSrc = null;
if (auth()->check()) {
    $avatarPath = trim((string) (auth()->user()->avatar_url ?? ''));

    if ($avatarPath !== '') {
        $avatarSrc = \Illuminate\Support\Str::startsWith($avatarPath, ['http://', 'https://', '/'])
            ? $avatarPath
            : asset('storage/' . ltrim($avatarPath, '/'));
    }
}
@endphp

<style>
.cb-navbar-shell { position: sticky; top: 0; z-index: 50; }
.cb-nav {
    /* Đổi màu nền một chút cho khớp admin */
    background: rgba(248,246,241,0.96); 
    backdrop-filter: blur(16px);
    -webkit-backdrop-filter: blur(16px);
    border-bottom: 1px solid var(--cb-border);
    padding: 0 28px; 
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
    height: 60px; 
}
.cb-logo { display: flex; align-items: center; gap: 10px; text-decoration: none; flex-shrink: 0; margin-right: 32px; }
.cb-logo-img { width: 40px; height: 36px; object-fit: contain; display: block; }
.cb-logo-text { font-family: var(--cb-serif); font-size: 24px; font-weight: 900; color: var(--cb-text); letter-spacing: -.5px; line-height: 1; }
.cb-logo-text span { font-style: normal; color: var(--cb-accent); }
.cb-nav-links { display: flex; gap: 28px; align-items: center; }
.cb-nav-link {
    font-family: var(--cb-font-sans);
    font-size: 14px;
    font-weight: 500;
    color: var(--cb-muted);
    text-decoration: none;
    transition: color 0.2s;
    white-space: nowrap;
    padding-bottom: 2px;
    border-bottom: 2px solid transparent;
}
.cb-nav-link:hover  { color: var(--cb-text); }
.cb-nav-link.active { color: var(--cb-accent); border-bottom-color: var(--cb-accent); }
.cb-nav-right { display: flex; align-items: center; gap: 10px; flex-shrink: 0; }
.cb-cart-icon {
    position: relative;
    width: 38px; height: 38px;
    border-radius: 50%;
    border: 1.5px solid #a0a1a0;
    background: var(--cb-white);
    display: flex; align-items: center; justify-content: center;
    text-decoration: none;
    color: var(--cb-text);
    transition: border-color 0.2s;
    flex-shrink: 0;
    transition: border-color .18s, transform .18s;
}
.cb-cart-icon:hover { border-color: var(--cb-accent);transform: translateY(-3px); }
.cb-cart-badge {
    position: absolute; top: -5px; right: -5px;
    background: var(--cb-accent);
    color: #fff;
    min-width: 18px; height: 18px;
    border-radius: 999px;
    font-family: var(--cb-font-sans);
    font-size: 10px; font-weight: 700;
    display: flex; align-items: center; justify-content: center;
    padding: 0 4px;
    border: 2px solid var(--cb-bg);
}
.cb-btn-ghost {
    font-family: var(--cb-font-sans, 'DM Sans', sans-serif);
    font-size: 13px; font-weight: 500;
    padding: 8px 18px;
    border-radius: 999px;
    border: 1.5px solid #e0dbd0;
    /* Thêm fallback màu trắng và đen */
    background: var(--cb-white, #ffffff);
    color: var(--cb-text, #1a1a1a);
    cursor: pointer;
    transition: all 0.2s;
    text-decoration: none;
    display: inline-flex; align-items: center;
    white-space: nowrap;
}
.cb-btn-ghost:hover { 
    border-color: var(--cb-accent, #2d6a4f); 
    color: var(--cb-accent, #2d6a4f); 
    background: var(--cb-white, #ffffff); 
}

.cb-btn-solid {
    font-family: var(--cb-font-sans, 'DM Sans', sans-serif);
    font-size: 13px; font-weight: 600;
    padding: 8px 20px;
    border-radius: 999px;
    border: none;
    /* Thêm fallback màu đen (#1a1a1a) để nút không bao giờ bị trong suốt */
    background: var(--cb-text, #1a1a1a);
    color: #fff;
    cursor: pointer;
    transition: background 0.2s;
    text-decoration: none;
    display: inline-flex; align-items: center;
    white-space: nowrap;
}
.cb-btn-solid:hover { 
    background: var(--cb-accent, #2d6a4f); 
}
.cb-user-menu { position: relative; }
.cb-user-trigger {
    font-family: var(--cb-font-sans);
    font-size: 13px; font-weight: 500;
    padding: 5px 12px 5px 5px;
    border-radius: 999px;
    background: var(--cb-white);
    border: 1.5px solid #e0dbd0;
    color: var(--cb-text);
    cursor: pointer;
    transition: all 0.2s;
    display: flex; align-items: center;
    gap: 8px;
    white-space: nowrap;
    max-width: 220px;
}
.cb-user-trigger:hover { border-color: var(--cb-accent); background: var(--cb-white); }
.cb-avatar-sm {
    width: 28px; height: 28px;
    border-radius: 50%;
    overflow: hidden;
    background: var(--cb-accent);
    color: #fff;
    font-size: 11px; font-weight: 700;
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
    font-family: var(--cb-font-sans);
}
.cb-avatar-sm img { width: 100%; height: 100%; object-fit: cover; border-radius: 50%; display: block; }
.cb-user-trigger-name { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 130px; }
.cb-chevron { flex-shrink: 0; transition: transform 0.2s; }
.cb-user-menu.open .cb-chevron { transform: rotate(180deg); }
.cb-user-dropdown {
    position: absolute;
    top: calc(100% + 10px);
    right: 0;
    background: var(--cb-white);
    border: 1px solid var(--cb-border);
    border-radius: 14px;
    box-shadow: 0 8px 28px rgba(0,0,0,0.11);
    min-width: 220px;
    z-index: 100;
    padding: 6px;
    opacity: 0;
    visibility: hidden;
    transform: translateY(-6px);
    transition: opacity 0.18s ease, visibility 0.18s ease, transform 0.18s ease;
    pointer-events: none;
}
.cb-user-menu.open .cb-user-dropdown { background: var(--cb-white); opacity: 1; visibility: visible; transform: translateY(0); pointer-events: auto; }
.cb-dropdown-role { padding: 8px 12px; font-family: var(--cb-font-sans); font-size: 11px; color: var(--cb-muted); display: flex; align-items: center; gap: 8px; }
.cb-dropdown-role-email { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
.cb-role-pill { font-size: 10px; font-weight: 700; padding: 2px 8px; border-radius: 999px; letter-spacing: 0.4px; flex-shrink: 0; }
.cb-role-pill--admin { background: #fef3c7; color: #92400e; }
.cb-role-pill--staff { background: #e0eaff; color: #1e3a8a; }
.cb-dropdown-item {
    display: flex; align-items: center; gap: 10px;
    width: 100%;
    padding: 10px 12px;
    border-radius: 9px;
    border: none; background: var(--cb-white);
    cursor: pointer;
    font-family: var(--cb-font-sans);
    font-size: 13px; font-weight: 500;
    color: #333;
    text-decoration: none;
    transition: background 0.15s, color 0.15s;
    text-align: left;
    box-sizing: border-box;
}
.cb-dropdown-item svg        { flex-shrink: 0; opacity: 0.55; }
.cb-dropdown-item:hover      { background: var(--cb-bg); color: var(--cb-accent); }
.cb-dropdown-item:hover svg  { opacity: 1; }
.cb-dropdown-item--admin:hover { background: #fffbeb; color: #92400e; }
.cb-dropdown-item--staff:hover { background: #eff6ff; color: #1e3a8a; }
.cb-dropdown-item--danger      { color: #dc2626; }
.cb-dropdown-item--danger:hover { background: #fff1f2; color: #b91c1c; }
.cb-dropdown-divider { height: 1px; background: var(--cb-border); margin: 4px 0; }
.cb-hamburger {
    display: none;
    flex-direction: column;
    justify-content: center;
    gap: 5px;
    width: 36px; height: 36px;
    border: 1.5px solid #ddd;
    border-radius: 8px;
    background: transparent;
    cursor: pointer;
    padding: 8px;
    flex-shrink: 0;
    transition: border-color 0.2s;
}
.cb-hamburger:hover { border-color: var(--cb-text); }
.cb-hamburger span { display: block; height: 2px; background: var(--cb-text); border-radius: 2px; transition: transform 0.25s, opacity 0.25s; }
.cb-hamburger.open span:nth-child(1) { transform: translateY(7px) rotate(45deg); }
.cb-hamburger.open span:nth-child(2) { opacity: 0; }
.cb-hamburger.open span:nth-child(3) { transform: translateY(-7px) rotate(-45deg); }
.cb-mobile-drawer { display: none; flex-direction: column; background: var(--cb-bg); border-bottom: 1px solid var(--cb-border); padding: 6px 16px 16px; }
.cb-mobile-drawer.open { display: flex; }
.cb-mobile-link {
    font-family: var(--cb-font-sans);
    font-size: 14px; font-weight: 500;
    color: #555;
    text-decoration: none;
    padding: 13px 8px;
    border-bottom: 1px solid #f0ede6;
    display: flex; align-items: center; justify-content: space-between;
    background: transparent;
    border-left: none; border-right: none; border-top: none;
    cursor: pointer;
    transition: color 0.2s;
}
.cb-mobile-link:last-child    { border-bottom: none; }
.cb-mobile-link:hover         { color: var(--cb-accent); }
.cb-mobile-link--active       { color: var(--cb-accent); font-weight: 600; }
.cb-mobile-link--danger       { color: #dc2626; }
.cb-mobile-link--danger:hover { color: #b91c1c; }
.cb-mobile-link--role         { color: #92400e; font-size: 13px; }
.cb-mobile-link--cta { margin-top: 8px; background: var(--cb-accent); color: #fff !important; border-radius: 10px; justify-content: center; padding: 13px; border: none; }
.cb-mobile-link--cta:hover { background: var(--cb-accent-dark); }
.cb-mobile-badge { background: var(--cb-accent); color: #fff; font-size: 10px; font-weight: 700; padding: 2px 8px; border-radius: 999px; font-family: var(--cb-font-sans); }
.cb-admin-strip {
    background: #0d1b10;
    color: #4ade80;
    font-family: var(--cb-sans);
    font-size: 12px;
    padding: 6px 24px;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 16px;
}
.cb-admin-strip-left,
.cb-admin-strip-right {
    display: flex;
    align-items: center;
    gap: 8px;
}
.cb-admin-strip-right { gap: 16px; }
.cb-admin-strip-role {
    background: var(--cb-accent);
    color: #fff;
    font-size: 10px;
    font-weight: 700;
    padding: 2px 8px;
    border-radius: 999px;
}
.cb-admin-strip-link {
    color: #86efac;
    text-decoration: none;
    font-weight: 600;
}
.cb-mobile-logout-form { margin: 0; }
.cb-mobile-logout-btn { width: 100%; text-align: left; }
.cb-dropdown-logout-form { margin: 0; }
@media (max-width: 860px) { .cb-nav-links { display: none; } .cb-hamburger { display: flex; } }
@media (max-width: 600px) { .cb-nav { padding: 0 16px; } .cb-user-trigger-name { display: none; } .cb-user-trigger { padding: 5px; border-radius: 50%; } .cb-chevron { display: none; } .cb-btn-ghost { display: none; } }
</style>

{{-- Banner thông báo cho Admin/Staff khi xem trang khách hàng --}}
@if(auth()->check() && in_array(auth()->user()->role, ['admin', 'staff']))
<div class="cb-admin-strip">
    <span class="cb-admin-strip-left">
        <span class="cb-admin-strip-role">
            {{ strtoupper(auth()->user()->role) }}
        </span>
        Bạn đang xem trang khách hàng
    </span>
    <span class="cb-admin-strip-right">
        @if(auth()->user()->role === 'admin')
            <a href="{{ route('admin.panel') }}" class="cb-admin-strip-link">
                ← Vào Admin Dashboard
            </a>
        @else
            <a href="{{ route('staff.panel') }}" class="cb-admin-strip-link">
                ← Vào Staff Dashboard
            </a>
        @endif
    </span>
</div>
@endif

<header class="cb-navbar-shell">
    <nav class="cb-nav">

        {{-- Logo --}}
            <a href="{{ route('home') }}" class="cb-logo">
                <img src="{{ asset('images/logocatbook3.png') }}" alt="CatBook Logo" class="cb-logo-img" width="40" height="36" decoding="async" fetchpriority="high">
                <span class="cb-logo-text">Cat<span>Book</span></span>
            </a>
        {{-- Desktop links --}}
        <div class="cb-nav-links">
            <a href="{{ route('home') }}"               class="cb-nav-link {{ $isHome    ? 'active' : '' }}">Trang chủ</a>
            <a href="{{ route('catalog.categories') }}" class="cb-nav-link {{ $isCatalog ? 'active' : '' }}">Danh mục</a>
            <a href="#"                            class="cb-nav-link">Giới thiệu</a>
        </div>

        {{-- Right actions --}}
        <div class="cb-nav-right">
            <x-realtime-notification />

            {{-- Giỏ hàng --}}
            <a href="{{ route('cart.index') }}" class="cb-cart-icon" title="Giỏ hàng">
                <svg width="18" height="18" fill="none" stroke="currentColor" stroke-width="2"
                     viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
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
                {{-- Đang ở trang Login thì nút Login sẽ bôi đen (Solid) --}}
                <a href="{{ route('login') }}" 
                   class="{{ request()->routeIs('login') ? 'cb-btn-solid' : 'cb-btn-ghost' }}">
                   Đăng nhập
                </a>
                
                {{-- Nút Đăng ký luôn nổi bật, hoặc đổi bôi đen nếu đang ở trang Register --}}
                <a href="{{ route('register') }}" 
                   class="{{ request()->routeIs('register') ? 'cb-btn-solid' : 'cb-btn-solid' }}">
                   Đăng ký
                </a>
            @else
                {{-- User dropdown --}}
                <div class="cb-user-menu" id="cb-user-menu">
                    <button type="button" class="cb-user-trigger" id="cb-user-trigger"
                            aria-haspopup="true" aria-expanded="false">
                        <span class="cb-avatar-sm">
                            @if($avatarSrc)
                                <img src="{{ $avatarSrc }}" alt="{{ auth()->user()->full_name }}">
                            @else
                                {{ $initial }}
                            @endif
                        </span>
                        <span class="cb-user-trigger-name">{{ auth()->user()->full_name }}</span>
                        <svg width="14" height="14" fill="none" stroke="currentColor"
                             stroke-width="2" viewBox="0 0 24 24" class="cb-chevron">
                            <polyline points="6 9 12 15 18 9"/>
                        </svg>
                    </button>

                    <div class="cb-user-dropdown" id="cb-user-dropdown" role="menu">

                        {{-- Role header (chỉ hiện với admin/staff) --}}
                        @if(auth()->user()->role !== 'customer')
                            <div class="cb-dropdown-role">
                                <span class="cb-role-pill cb-role-pill--{{ auth()->user()->role }}">
                                    {{ ucfirst(auth()->user()->role) }}
                                </span>
                                <span class="cb-dropdown-role-email">
                                    {{ auth()->user()->email }}
                                </span>
                            </div>
                            <div class="cb-dropdown-divider"></div>
                        @endif

                        {{-- Admin/Staff Dashboard links removed from user dropdown per request --}}

                        <a href="{{ route('account.show') }}" class="cb-dropdown-item" role="menuitem">
                            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <circle cx="12" cy="8" r="4"/><path d="M4 20c0-4 3.6-7 8-7s8 3 8 7"/>
                            </svg>
                            Trang cá nhân
                        </a>

                        <a href="{{ route('orders.index') }}" class="cb-dropdown-item" role="menuitem">
                            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/>
                                <rect x="9" y="3" width="6" height="4" rx="1"/>
                            </svg>
                            Đơn hàng của tôi
                        </a>

                        <a href="{{ route('account.addresses.index') }}" class="cb-dropdown-item" role="menuitem">
                            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/>
                                <circle cx="12" cy="9" r="2.5"/>
                            </svg>
                            Địa chỉ giao hàng
                        </a>
                        <div class="cb-dropdown-divider"></div>

                        <form method="POST" action="{{ route('logout') }}" class="cb-dropdown-logout-form">
                            @csrf
                            <button type="submit" class="cb-dropdown-item cb-dropdown-item--danger" role="menuitem">
                                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="1.8" viewBox="0 0 24 24">
                                    <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/>
                                    <polyline points="16 17 21 12 16 7"/>
                                    <line x1="21" y1="12" x2="9" y2="12"/>
                                </svg>
                                Đăng xuất
                            </button>
                        </form>

                    </div>
                </div>
            @endguest

            {{-- Hamburger --}}
            <button class="cb-hamburger" id="cb-menu-toggle"
                    aria-label="Mở menu" aria-expanded="false" type="button">
                <span></span><span></span><span></span>
            </button>

        </div>
    </nav>

    {{-- Mobile drawer --}}
    <div class="cb-mobile-drawer" id="cb-mobile-drawer">
        <a href="{{ route('home') }}"               class="cb-mobile-link {{ $isHome    ? 'cb-mobile-link--active' : '' }}">Trang chủ</a>
        <a href="{{ route('catalog.categories') }}" class="cb-mobile-link {{ $isCatalog ? 'cb-mobile-link--active' : '' }}">Danh mục</a>
        <a href="{{ route('cart.index') }}"          class="cb-mobile-link">
            Giỏ hàng @if($cartCount > 0)<span class="cb-mobile-badge">{{ $cartCount }}</span>@endif
        </a>
        @auth
            <a href="{{ route('account.show') }}"           class="cb-mobile-link {{ $isAccount ? 'cb-mobile-link--active' : '' }}">Tài khoản</a>
            <a href="{{ route('orders.index') }}"            class="cb-mobile-link {{ $isOrders  ? 'cb-mobile-link--active' : '' }}">Đơn hàng</a>
            <a href="{{ route('account.addresses.index') }}" class="cb-mobile-link">Địa chỉ</a>
            @if(auth()->user()->role === 'admin')
                <a href="{{ route('admin.panel') }}" class="cb-mobile-link cb-mobile-link--role">⚙ Admin Dashboard</a>
            @elseif(auth()->user()->role === 'staff')
                <a href="{{ route('staff.panel') }}" class="cb-mobile-link cb-mobile-link--role">✦ Staff Dashboard</a>
            @endif
            <form method="POST" action="{{ route('logout') }}" class="cb-mobile-logout-form">
                @csrf
                <button type="submit" class="cb-mobile-link cb-mobile-link--danger cb-mobile-logout-btn">
                    Đăng xuất
                </button>
            </form>
        @else
            <a href="{{ route('login') }}"    class="cb-mobile-link">Đăng nhập</a>
            <a href="{{ route('register') }}" class="cb-mobile-link cb-mobile-link--cta">Đăng ký</a>
        @endauth
    </div>
</header>

<script>
document.addEventListener('DOMContentLoaded', function () {
    /* Mobile hamburger */
    const toggle = document.getElementById('cb-menu-toggle');
    const drawer = document.getElementById('cb-mobile-drawer');
    if (toggle && drawer) {
        toggle.addEventListener('click', function () {
            const open = drawer.classList.toggle('open');
            toggle.classList.toggle('open', open);
            toggle.setAttribute('aria-expanded', String(open));
        });
    }

    /* User dropdown */
    const trigger = document.getElementById('cb-user-trigger');
    const menu    = document.getElementById('cb-user-menu');
    if (trigger && menu) {
        trigger.addEventListener('click', function (e) {
            e.stopPropagation();
            const open = menu.classList.toggle('open');
            trigger.setAttribute('aria-expanded', String(open));
        });
    }

    /* Click outside — đóng cả hai */
    document.addEventListener('click', function (e) {
        if (menu && !menu.contains(e.target)) {
            menu.classList.remove('open');
            trigger && trigger.setAttribute('aria-expanded', 'false');
        }
        if (toggle && drawer && !toggle.contains(e.target) && !drawer.contains(e.target)) {
            drawer.classList.remove('open');
            toggle.classList.remove('open');
            toggle.setAttribute('aria-expanded', 'false');
        }
    });

    /* Escape key */
    document.addEventListener('keydown', function (e) {
        if (e.key !== 'Escape') return;
        if (menu) {
            menu.classList.remove('open');
        }
        if (trigger) {
            trigger.setAttribute('aria-expanded', 'false');
        }
        if (drawer) {
            drawer.classList.remove('open');
        }
        if (toggle) {
            toggle.classList.remove('open');
            toggle.setAttribute('aria-expanded', 'false');
        }
    });
});
</script>