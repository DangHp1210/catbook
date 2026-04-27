@props(['dark' => false])

@php
    $isDark = (bool) $dark;
    $headerClasses = $isDark
        ? 'border-slate-700 bg-slate-900 text-slate-100'
        : 'border-slate-200 bg-white text-slate-900';
    $softText = $isDark ? 'text-slate-300' : 'text-slate-500';
    $linkClasses = $isDark
        ? 'text-slate-100 hover:bg-slate-800'
        : 'text-slate-700 hover:bg-orange-50 hover:text-orange-700';
    $cartCount = 0;

    if (auth()->check()) {
        $cart = auth()->user()->cart()->with('items')->first();
        $cartCount = $cart ? (int) $cart->items->sum('quantity') : 0;
    }
@endphp

<header class="sticky top-0 z-50">
    <div class="{{ $isDark ? 'bg-slate-950' : 'cb-top-strip' }}">
        <div class="{{ $isDark ? 'mx-auto flex w-full max-w-[1120px] items-center justify-between gap-2 px-3 py-1 text-[11px] font-semibold text-slate-300 sm:px-4' : 'cb-top-strip-inner' }}">
            <p>{{ $isDark ? 'Catbook workspace' : 'Freeship đơn từ 299.000đ' }}</p>
            <p>{{ $isDark ? 'Kho sách + AI gợi ý' : 'Ưu đãi NEWBOOK10 cho khách hàng mới' }}</p>
        </div>
    </div>

    <div class="border-b {{ $headerClasses }} shadow-sm">
        <div class="mx-auto flex w-full max-w-[1120px] items-center justify-between gap-2 px-3 py-2 sm:px-4">
            <a href="{{ route('home') }}" class="flex items-center gap-2.5">
                <span class="flex h-8 w-8 items-center justify-center rounded-lg bg-gradient-to-br from-orange-500 to-amber-400 text-sm font-black text-white shadow-sm">C</span>
                <div>
                    <p class="text-[11px] font-bold uppercase tracking-[0.2em] text-orange-500">Catbook</p>
                    <p class="text-[11px] {{ $softText }}">Nha sach truc tuyen</p>
                </div>
            </a>

            <nav class="flex items-center gap-1">
                @auth
                    @if (auth()->user()->role === 'admin')
                        <div class="relative group">
                            <button class="rounded-lg px-2.5 py-1.5 text-xs font-medium transition {{ $linkClasses }} flex items-center gap-1.5">
                                <span>Admin</span>
                                <svg class="h-3.5 w-3.5 transition group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
                                </svg>
                            </button>
                            
                            <!-- Admin Dropdown Menu -->
                            <div class="absolute right-0 mt-0 w-48 rounded-lg shadow-lg {{ $isDark ? 'bg-slate-800 border border-slate-700' : 'bg-white border border-slate-200' }} opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50">
                                <a href="{{ route('admin.panel') }}" class="block px-4 py-2.5 text-xs {{ $linkClasses }} hover:rounded-t-lg first-of-type:rounded-t-lg">
                                    📊 Tổng quan
                                </a>
                                <a href="{{ route('admin.users.index') }}" class="block px-4 py-2.5 text-xs {{ $linkClasses }}">
                                    👥 Quản lý người dùng
                                </a>
                                <a href="{{ route('admin.books.index') }}" class="block px-4 py-2.5 text-xs {{ $linkClasses }}">
                                    📚 Quản lý sách
                                </a>
                                <a href="{{ route('admin.authors.index') }}" class="block px-4 py-2.5 text-xs {{ $linkClasses }}">
                                    ✍️ Quản lý tác giả
                                </a>
                                <a href="{{ route('admin.categories.index') }}" class="block px-4 py-2.5 text-xs {{ $linkClasses }}">
                                    🏷️ Quản lý danh mục
                                </a>
                                <a href="{{ route('admin.publishers.index') }}" class="block px-4 py-2.5 text-xs {{ $linkClasses }}">
                                    🏢 Quản lý nhà xuất bản
                                </a>
                                <a href="{{ route('admin.orders.index') }}" class="block px-4 py-2.5 text-xs {{ $linkClasses }}">
                                    📦 Quản lý đơn hàng
                                </a>
                                <a href="{{ route('admin.revenue.index') }}" class="block px-4 py-2.5 text-xs {{ $linkClasses }} hover:rounded-b-lg last-of-type:rounded-b-lg">
                                    💰 Báo cáo doanh thu
                                </a>
                            </div>
                        </div>
                    @elseif (in_array(auth()->user()->role, ['staff', 'admin'], true))
                        <a href="{{ route('staff.panel') }}" class="rounded-lg px-2.5 py-1.5 text-xs font-medium transition {{ $linkClasses }}">
                            Staff
                        </a>
                    @endif
                    <a href="{{ route('orders.index') }}" class="rounded-lg px-2.5 py-1.5 text-xs font-medium transition {{ $linkClasses }}">
                        Don hang
                    </a>
                    <a href="{{ route('cart.index') }}" class="rounded-lg px-2.5 py-1.5 text-xs font-medium transition {{ $linkClasses }}">
                        Gio hang{{ $cartCount > 0 ? ' ('.$cartCount.')' : '' }}
                    </a>
                    <a href="{{ route('account.show') }}" class="hidden rounded-lg px-2.5 py-1.5 text-xs font-semibold transition sm:inline-flex {{ $isDark ? 'bg-slate-800 text-slate-100 hover:bg-slate-700' : 'bg-orange-50 text-orange-700 hover:bg-orange-100' }}">
                        {{ auth()->user()->full_name }}
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="rounded-lg px-2.5 py-1.5 text-xs font-semibold transition {{ $isDark ? 'bg-rose-600 text-white hover:bg-rose-500' : 'bg-slate-900 text-white hover:bg-slate-700' }}">
                            Đăng xuất
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="rounded-lg px-2.5 py-1.5 text-xs font-medium transition {{ $linkClasses }}">Đăng nhập</a>
                    <a href="{{ route('register') }}" class="rounded-lg px-2.5 py-1.5 text-xs font-semibold transition {{ $isDark ? 'bg-orange-500 text-white hover:bg-orange-400' : 'bg-orange-500 text-white hover:bg-orange-600' }}">Đăng ký</a>
                @endauth
            </nav>
        </div>
    </div>
</header>
