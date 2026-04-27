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

<header class="sticky top-2 z-50 mx-auto w-full max-w-6xl px-3 sm:px-4 lg:px-6">
    <div class="rounded-2xl border {{ $baseClasses }} px-3 py-2 shadow-lg shadow-black/10">
        <div class="flex items-center justify-between gap-2">
            <a href="{{ route('home') }}" class="flex items-center gap-2.5">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-gradient-to-br from-amber-400 via-orange-400 to-rose-500 text-base font-black text-slate-950 shadow-md shadow-amber-500/20">C</span>
                <div>
                    <p class="text-xs font-semibold tracking-[0.2em] text-amber-400 uppercase">Catbook</p>
                    <p class="text-[11px] {{ $isDark ? 'text-slate-400' : 'text-slate-500' }}">Website bán sách tích hợp AI</p>
                </div>
            </a>

            <nav class="flex items-center gap-1.5 sm:gap-2">
                @auth
                    @if (auth()->user()->role === 'admin')
                        <a href="{{ route('admin.panel') }}" class="rounded-xl px-3 py-1.5 text-xs sm:text-sm font-medium transition {{ $linkClasses }}">
                            Admin
                        </a>
                    @elseif (in_array(auth()->user()->role, ['staff', 'admin'], true))
                        <a href="{{ route('staff.panel') }}" class="rounded-xl px-3 py-1.5 text-xs sm:text-sm font-medium transition {{ $linkClasses }}">
                            Staff
                        </a>
                    @endif
                    <a href="{{ route('orders.index') }}" class="rounded-xl px-3 py-1.5 text-xs sm:text-sm font-medium transition {{ $linkClasses }}">
                        Don hang
                    </a>
                    <a href="{{ route('cart.index') }}" class="rounded-xl px-3 py-1.5 text-xs sm:text-sm font-medium transition {{ $linkClasses }}">
                        Gio hang{{ $cartCount > 0 ? ' ('.$cartCount.')' : '' }}
                    </a>
                    <a href="{{ route('account.show') }}" class="hidden rounded-full px-2.5 py-1.5 text-xs sm:text-sm font-medium transition sm:inline-flex {{ $isDark ? 'bg-emerald-500/10 text-emerald-300 hover:bg-emerald-500/15' : 'bg-emerald-50 text-emerald-700 hover:bg-emerald-100' }}">
                        {{ auth()->user()->full_name }}
                    </a>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="rounded-xl px-3 py-1.5 text-xs sm:text-sm font-semibold transition {{ $isDark ? 'bg-rose-500 text-white hover:bg-rose-400' : 'bg-rose-600 text-white hover:bg-rose-500' }}">
                            Đăng xuất
                        </button>
                    </form>
                @else
                    <a href="{{ route('login') }}" class="rounded-xl px-3 py-1.5 text-xs sm:text-sm font-medium transition {{ $linkClasses }}">Đăng nhập</a>
                    <a href="{{ route('register') }}" class="rounded-xl px-3 py-1.5 text-xs sm:text-sm font-semibold transition {{ $isDark ? 'bg-amber-400 text-slate-950 hover:bg-amber-300' : 'bg-slate-900 text-white hover:bg-slate-800' }}">Đăng ký</a>
                @endauth
            </nav>
        </div>
    </div>
</header>
