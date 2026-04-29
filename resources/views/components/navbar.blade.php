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
    {{-- top strip removed per user request: promos hidden --}}

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
                        <a href="{{ route('admin.panel') }}" class="rounded-lg px-2.5 py-1.5 text-xs font-medium transition {{ $linkClasses }}">
                            Admin
                        </a>
                    @elseif (in_array(auth()->user()->role, ['staff', 'admin'], true))
                        <a href="{{ route('staff.panel') }}" class="rounded-lg px-2.5 py-1.5 text-xs font-medium transition {{ $linkClasses }}">
                            Staff
                        </a>
                    @endif
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
