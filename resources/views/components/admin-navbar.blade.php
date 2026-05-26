@php
    $role    = auth()->user()?->role;
    $isAdmin = $role === 'admin';
    $isStaff = $role === 'staff';

    $initial = auth()->check()
        ? mb_strtoupper(mb_substr(auth()->user()->full_name, 0, 1))
        : '';

    $avatarSrc = null;
    if (auth()->check()) {
        $avatarPath = trim((string)(auth()->user()->avatar_url ?? ''));
        if ($avatarPath !== '') {
            $avatarSrc = \Illuminate\Support\Str::startsWith($avatarPath, ['http://', 'https://', '/'])
                ? $avatarPath
                : asset('storage/' . ltrim($avatarPath, '/'));
        }
    }
@endphp

<style>
.an-shell { position: sticky; top: 0; z-index: 60; }
.an-bar { height: 60px; background: rgba(248,246,241,.96); backdrop-filter: blur(16px); -webkit-backdrop-filter: blur(16px); border-bottom: 1px solid var(--cb-border); display: flex; align-items: center; padding: 0 28px; gap: 0; }
.an-logo { display: flex; align-items: center; gap: 10px; text-decoration: none; flex-shrink: 0; margin-right: 32px; }
.an-logo-img { width: 40px; height: 36px; object-fit: contain; display: block; }
.an-logo-text { font-family: var(--cb-serif); font-size: 24px; font-weight: 900; color: var(--cb-text); letter-spacing: -.5px; line-height: 1; }
.an-logo-text em { font-style: normal; color: var(--cb-accent); }
.an-role-pill { font-family: var(--cb-sans); font-size: 10px; font-weight: 700; letter-spacing: 1px; text-transform: uppercase; padding: 3px 9px; border-radius: 999px; }
.an-role-admin { background: #fef3c7; color: #92400e; }
.an-role-staff { background: #e0eaff; color: #1e3a8a; }
.an-links { display: flex; align-items: center; gap: 2px; flex: 1; }
.an-link { font-family: var(--cb-sans); font-size: 13px; font-weight: 500; color: var(--cb-muted); text-decoration: none; padding: 6px 12px; border-radius: 8px; white-space: nowrap; transition: color .18s, background .18s; display: flex; align-items: center; gap: 6px; }
.an-link:hover { color: var(--cb-text); background: rgba(0,0,0,.04); }
.an-link.active { color: var(--cb-accent); background: var(--cb-accent-light); font-weight: 600; }
.an-link-dot { width: 5px; height: 5px; border-radius: 50%; background: currentColor; opacity: .5; }
.an-link.active .an-link-dot { opacity: 1; }
.an-right { display: flex; align-items: center; gap: 8px; margin-left: auto; }
.an-site-btn { font-family: var(--cb-sans); font-size: 12px; font-weight: 500; padding: 7px 14px; border-radius: 8px; border: 1.5px solid var(--cb-border); background: transparent; color: var(--cb-muted); text-decoration: none; display: inline-flex; align-items: center; gap: 6px; transition: all .18s; white-space: nowrap; }
.an-site-btn:hover { border-color: var(--cb-accent); color: var(--cb-accent); background: var(--cb-accent-light); }
.an-bell-wrap { position: relative; }
.an-bell-btn { width: 36px; height: 36px; border-radius: 9px; border: 1.5px solid var(--cb-border); background: transparent; display: flex; align-items: center; justify-content: center; cursor: pointer; color: var(--cb-muted); transition: all .18s; }
.an-bell-btn:hover { border-color: var(--cb-accent); color: var(--cb-accent); background: var(--cb-accent-light); }
.an-bell-badge { position: absolute; top: -5px; right: -5px; width: 16px; height: 16px; border-radius: 50%; background: #dc2626; color: #fff; font-family: var(--cb-sans); font-size: 9px; font-weight: 700; display: flex; align-items: center; justify-content: center; border: 2px solid var(--cb-bg); line-height: 1; }
.an-sep { width: 1px; height: 22px; background: var(--cb-border); margin: 0 4px; flex-shrink: 0; }
.an-user-wrap { position: relative; }
.an-user-btn { display: flex; align-items: center; gap: 8px; padding: 4px 10px 4px 4px; border-radius: 999px; border: 1.5px solid var(--cb-border); background: var(--cb-white); cursor: pointer; transition: all .18s; max-width: 220px; }
.an-user-btn:hover { border-color: var(--cb-accent); }
.an-avatar { width: 28px; height: 28px; border-radius: 50%; flex-shrink: 0; overflow: hidden; background: var(--cb-accent); color: #fff; font-family: var(--cb-sans); font-size: 11px; font-weight: 700; display: flex; align-items: center; justify-content: center; }
.an-avatar img { width: 100%; height: 100%; object-fit: cover; display: block; }
.an-user-name { font-family: var(--cb-sans); font-size: 13px; font-weight: 500; color: var(--cb-text); white-space: nowrap; overflow: hidden; text-overflow: ellipsis; max-width: 120px; }
.an-chevron { flex-shrink: 0; color: var(--cb-muted); transition: transform .2s; }
.an-user-wrap.open .an-chevron { transform: rotate(180deg); }
.an-dropdown { position: absolute; top: calc(100% + 8px); right: 0; min-width: 220px; background: var(--cb-white); border: 1px solid var(--cb-border); border-radius: 14px; box-shadow: 0 8px 32px rgba(0,0,0,.1); padding: 6px; z-index: 200; opacity: 0; visibility: hidden; transform: translateY(-6px); transition: opacity .18s, visibility .18s, transform .18s; pointer-events: none; }
.an-user-wrap.open .an-dropdown { opacity: 1; visibility: visible; transform: translateY(0); pointer-events: auto; }
.an-drop-header { padding: 10px 12px 8px; }
.an-drop-fullname { font-family: var(--cb-sans); font-size: 13px; font-weight: 600; color: var(--cb-text); margin-bottom: 2px; }
.an-drop-meta { font-family: var(--cb-sans); font-size: 11px; color: var(--cb-muted); }
.an-drop-hr { height: 1px; background: var(--cb-border); margin: 4px 0; }
.an-drop-item { display: flex; align-items: center; gap: 9px; width: 100%; padding: 9px 12px; border-radius: 9px; border: none; background: transparent; cursor: pointer; font-family: var(--cb-sans); font-size: 13px; font-weight: 500; color: var(--cb-text); text-decoration: none; text-align: left; transition: background .15s, color .15s; }
.an-drop-item:hover { background: var(--cb-bg); color: var(--cb-accent); }
.an-drop-item-danger { color: #dc2626; }
.an-drop-item-danger:hover { background: #fff1f2; color: #b91c1c; }
.an-burger { display: none; flex-direction: column; justify-content: center; gap: 5px; width: 36px; height: 36px; border: 1.5px solid var(--cb-border); border-radius: 8px; background: transparent; cursor: pointer; padding: 8px; }
.an-burger span { display: block; height: 2px; border-radius: 2px; background: var(--cb-text); transition: transform .22s, opacity .22s; }
.an-burger.open span:nth-child(1) { transform: translateY(7px) rotate(45deg); }
.an-burger.open span:nth-child(2) { opacity: 0; }
.an-burger.open span:nth-child(3) { transform: translateY(-7px) rotate(-45deg); }
.an-mobile { display: none; flex-direction: column; background: var(--cb-bg); border-bottom: 1px solid var(--cb-border); padding: 8px 16px 16px; }
.an-mobile.open { display: flex; }
.an-mobile-section { font-family: var(--cb-sans); font-size: 10px; font-weight: 700; letter-spacing: 1.2px; text-transform: uppercase; color: #b0a898; padding: 14px 8px 6px; margin-top: 4px; }
.an-mobile-link { font-family: var(--cb-sans); font-size: 14px; font-weight: 500; color: var(--cb-muted); text-decoration: none; padding: 12px 8px; border-bottom: 1px solid #f0ede6; display: flex; align-items: center; gap: 8px; transition: color .15s; background: transparent; border-left: none; border-right: none; border-top: none; }
.an-mobile-link:last-child { border-bottom: none; }
.an-mobile-link:hover { color: var(--cb-accent); }
.an-mobile-link.active { color: var(--cb-accent); font-weight: 600; }
@media (max-width: 1000px) { .an-links { display: none; } .an-burger { display: flex; } }
@media (max-width: 600px) { .an-bar { padding: 0 16px; } .an-site-btn { display: none; } .an-user-name { display: none; } .an-user-btn { padding: 4px; border-radius: 50%; } .an-chevron { display: none; } .an-role-pill { display: none; } }
</style>

<header class="an-shell">
    {{-- ── Main bar ──────────────────────────────────────── --}}
    <nav class="an-bar">

        {{-- Logo --}}
        <a href="{{ route('home') }}" class="an-logo">
            <img src="{{ asset('images/logocatbook3.png') }}" alt="CatBook" class="an-logo-img" width="40" height="36" decoding="async" fetchpriority="high">
            <span class="an-logo-text">Cat<em>Book</em></span>
        </a>

        {{-- Role pill --}}
        @if($isAdmin)
            <span class="an-role-pill an-role-admin">Admin</span>
        @elseif($isStaff)
            <span class="an-role-pill an-role-staff">Staff</span>
        @endif

        {{-- Desktop nav links --}}
        <nav class="an-links" style="margin-left:20px">
            @if($isAdmin)
                <a href="{{ route('admin.panel') }}"
                   class="an-link {{ request()->routeIs('admin.panel') ? 'active' : '' }}">
                    <span class="an-link-dot"></span>Dashboard
                </a>
            @elseif($isStaff)
                <a href="{{ route('staff.panel') }}"
                   class="an-link {{ request()->routeIs('staff.panel') ? 'active' : '' }}">
                    <span class="an-link-dot"></span>Dashboard
                </a>
            @endif
        </nav>

        {{-- Right side --}}
        <div class="an-right">

            {{-- Real-time notification component --}}
            <x-realtime-notification />

            {{-- Separator --}}
            <div class="an-sep"></div>

            {{-- View customer site --}}
            <a href="{{ route('home') }}" target="_blank" class="an-site-btn">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"/>
                    <polyline points="15 3 21 3 21 9"/>
                    <line x1="10" y1="14" x2="21" y2="3"/>
                </svg>
                Trang khách
            </a>

            {{-- User menu --}}
            <div class="an-user-wrap" id="an-user-wrap">
                <button type="button" class="an-user-btn"
                        id="an-user-btn"
                        aria-haspopup="true" aria-expanded="false">
                    <span class="an-avatar">
                        @if($avatarSrc)
                            <img src="{{ $avatarSrc }}" alt="{{ auth()->user()->full_name }}">
                        @else
                            {{ $initial }}
                        @endif
                    </span>
                    <span class="an-user-name">{{ auth()->user()->full_name }}</span>
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2"
                         viewBox="0 0 24 24" class="an-chevron">
                        <polyline points="6 9 12 15 18 9"/>
                    </svg>
                </button>

                <div class="an-dropdown" id="an-dropdown" role="menu">
                    {{-- Header --}}
                    <div class="an-drop-header">
                        <div class="an-drop-fullname">{{ auth()->user()->full_name }}</div>
                        <div class="an-drop-meta">{{ auth()->user()->email }} · {{ ucfirst((string)$role) }}</div>
                    </div>

                    <div class="an-drop-hr"></div>

                    <a href="{{ route('account.show') }}" class="an-drop-item" role="menuitem">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/>
                        </svg>
                        Trang cá nhân
                    </a>
                    <a href="{{ route('orders.index') }}" class="an-drop-item" role="menuitem">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/>
                            <rect x="9" y="3" width="6" height="4" rx="1"/>
                        </svg>
                        Đơn hàng cá nhân
                    </a>

                    <div class="an-drop-hr"></div>

                    <form method="POST" action="{{ route('logout') }}" style="margin:0">
                        @csrf
                        <button type="submit" class="an-drop-item an-drop-item-danger" role="menuitem">
                            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                <path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/>
                                <polyline points="16 17 21 12 16 7"/>
                                <line x1="21" y1="12" x2="9" y2="12"/>
                            </svg>
                            Đăng xuất
                        </button>
                    </form>
                </div>
            </div>

            {{-- Hamburger --}}
            <button class="an-burger" id="an-burger"
                    aria-label="Mở menu" aria-expanded="false" type="button">
                <span></span><span></span><span></span>
            </button>
        </div>
    </nav>

    {{-- ── Mobile drawer ─────────────────────────────────── --}}
    <div class="an-mobile" id="an-mobile">
        <div class="an-mobile-section">Điều hướng</div>

        @if($isAdmin)
            <a href="{{ route('admin.panel') }}"
               class="an-mobile-link {{ request()->routeIs('admin.panel') ? 'active' : '' }}">
                Dashboard
            </a>
            <a href="{{ route('admin.users.index') }}"
               class="an-mobile-link {{ request()->routeIs('admin.users.*') ? 'active' : '' }}">
                Người dùng
            </a>
            <a href="{{ route('admin.books.index') }}"
               class="an-mobile-link {{ request()->routeIs('admin.books.*') ? 'active' : '' }}">
                Sách
            </a>
            <a href="{{ route('admin.orders.index') }}"
               class="an-mobile-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}">
                Đơn hàng
            </a>
            <a href="{{ route('admin.revenue.index') }}"
               class="an-mobile-link {{ request()->routeIs('admin.revenue.*') ? 'active' : '' }}">
                Doanh thu
            </a>
        @elseif($isStaff)
            <a href="{{ route('staff.panel') }}"
               class="an-mobile-link {{ request()->routeIs('staff.panel') ? 'active' : '' }}">
                Dashboard
            </a>
            <a href="{{ route('staff.books.index') }}"
               class="an-mobile-link {{ request()->routeIs('staff.books.*') ? 'active' : '' }}">
                Sách
            </a>
            <a href="{{ route('staff.categories.index') }}"
               class="an-mobile-link {{ request()->routeIs('staff.categories.*') ? 'active' : '' }}">
                Danh mục
            </a>
            <a href="{{ route('staff.orders.index') }}"
               class="an-mobile-link {{ request()->routeIs('staff.orders.*') ? 'active' : '' }}">
                Đơn hàng
            </a>
        @endif

        <div class="an-mobile-section">Tài khoản</div>
        <a href="{{ route('home') }}" class="an-mobile-link">Xem trang khách</a>
        <a href="{{ route('account.show') }}" class="an-mobile-link">Trang cá nhân</a>
        <a href="{{ route('orders.index') }}" class="an-mobile-link">Đơn hàng cá nhân</a>
    </div>
</header>

<script>
document.addEventListener('DOMContentLoaded', function () {
    /* ── Hamburger ── */
    const burger = document.getElementById('an-burger');
    const mobile = document.getElementById('an-mobile');
    if (burger && mobile) {
        burger.addEventListener('click', function () {
            const open = mobile.classList.toggle('open');
            burger.classList.toggle('open', open);
            burger.setAttribute('aria-expanded', String(open));
        });
    }

    /* ── User dropdown ── */
    const userBtn  = document.getElementById('an-user-btn');
    const userWrap = document.getElementById('an-user-wrap');
    if (userBtn && userWrap) {
        userBtn.addEventListener('click', function (e) {
            e.stopPropagation();
            const open = userWrap.classList.toggle('open');
            userBtn.setAttribute('aria-expanded', String(open));
        });
    }

    /* ── Close on outside click ── */
    document.addEventListener('click', function (e) {
        if (userWrap && !userWrap.contains(e.target)) {
            userWrap.classList.remove('open');
            userBtn?.setAttribute('aria-expanded', 'false');
        }
        if (burger && mobile && !burger.contains(e.target) && !mobile.contains(e.target)) {
            mobile.classList.remove('open');
            burger.classList.remove('open');
            burger.setAttribute('aria-expanded', 'false');
        }
    });

    /* ── ESC key ── */
    document.addEventListener('keydown', function (e) {
        if (e.key !== 'Escape') return;
        userWrap?.classList.remove('open');
        userBtn?.setAttribute('aria-expanded', 'false');
        mobile?.classList.remove('open');
        if (burger) { burger.classList.remove('open'); burger.setAttribute('aria-expanded', 'false'); }
    });
});
</script>