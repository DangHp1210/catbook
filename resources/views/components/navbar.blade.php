@props(['dark' => false])

@php
    $isDark = $dark;
    $baseClasses = $isDark
        ? 'border-white/10 bg-slate-900/75 text-white backdrop-blur'
        : 'border-slate-200/70 bg-white/80 text-slate-900 backdrop-blur';
    $linkClasses = $isDark
        ? 'text-slate-200 hover:text-white hover:bg-white/10'
        : 'text-slate-700 hover:text-slate-950 hover:bg-slate-100';
    $cartCount = 0;

    if (auth()->check()) {
        $cart = auth()->user()->cart()->with('items')->first();
        $cartCount = $cart ? (int) $cart->items->sum('quantity') : 0;
    }
@endphp

<header class="sticky top-4 z-50 mx-auto w-full max-w-6xl px-4 sm:px-6 lg:px-8">
    <div class="space-y-3 rounded-3xl border {{ $baseClasses }} px-4 py-3 shadow-xl shadow-black/10">
        <div class="flex items-center justify-between gap-3">
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-amber-400 via-orange-400 to-rose-500 text-lg font-black text-slate-950 shadow-lg shadow-amber-500/20">C</span>
                <div>
                    <p class="text-sm font-semibold tracking-[0.22em] text-amber-400 uppercase">Catbook</p>
                    <p class="text-xs {{ $isDark ? 'text-slate-400' : 'text-slate-500' }}">Website bán sách tích hợp AI</p>
                </div>
            </a>

            <nav class="flex items-center gap-2 sm:gap-3">
                @auth
                    <a href="{{ route('orders.index') }}" class="rounded-2xl px-4 py-2 text-sm font-medium transition {{ $linkClasses }}">
                        Don hang
                    </a>
                    <a href="{{ route('cart.index') }}" class="rounded-2xl px-4 py-2 text-sm font-medium transition {{ $linkClasses }}">
                        Gio hang{{ $cartCount > 0 ? ' ('.$cartCount.')' : '' }}
                    </a>
                    <a href="{{ route('account.show') }}" class="hidden rounded-full px-3 py-2 text-sm font-medium transition sm:inline-flex {{ $isDark ? 'bg-emerald-500/10 text-emerald-300 hover:bg-emerald-500/15' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}">
                        {{ auth()->user()->full_name }}
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="rounded-2xl px-4 py-2 text-sm font-semibold transition {{ $isDark ? 'bg-rose-500 text-white hover:bg-rose-400' : 'bg-rose-600 text-white hover:bg-rose-500' }}">
                            Đăng xuất
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="rounded-2xl px-4 py-2 text-sm font-medium transition {{ $linkClasses }}">Đăng nhập</a>
                    <a href="{{ route('register') }}" class="rounded-2xl px-4 py-2 text-sm font-semibold transition {{ $isDark ? 'bg-amber-400 text-slate-950 hover:bg-amber-300' : 'bg-slate-900 text-white hover:bg-slate-800' }}">Đăng ký</a>
                @endauth
            </nav>
        </div>

        <form method="GET" action="{{ route('catalog.categories') }}" class="grid gap-2 sm:grid-cols-[1fr_auto]">
            <label for="header-search" class="sr-only">Tim sach</label>
            <input
                id="header-search"
                name="q"
                type="text"
                value="{{ request('q') }}"
                placeholder="Tim theo ten sach, tac gia hoac ISBN..."
                class="w-full rounded-xl border px-4 py-2.5 text-sm outline-none transition {{ $isDark ? 'border-white/15 bg-slate-800 text-white placeholder:text-slate-400 focus:border-amber-300' : 'border-slate-300 bg-white text-slate-900 placeholder:text-slate-400 focus:border-orange-400' }}"
            >
            <button type="submit" class="rounded-xl px-5 py-2.5 text-sm font-semibold transition {{ $isDark ? 'bg-amber-400 text-slate-950 hover:bg-amber-300' : 'bg-orange-500 text-white hover:bg-orange-600' }}">
                Tim kiem
            </button>
        </form>
    </div>
</header>
