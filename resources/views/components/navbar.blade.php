@props(['dark' => false])

@php
    $isDark = $dark;
    $baseClasses = $isDark
        ? 'border-white/10 bg-slate-900/75 text-white backdrop-blur'
        : 'border-slate-200/70 bg-white/80 text-slate-900 backdrop-blur';
    $linkClasses = $isDark
        ? 'text-slate-200 hover:text-white hover:bg-white/10'
        : 'text-slate-700 hover:text-slate-950 hover:bg-slate-100';
@endphp

<header class="sticky top-4 z-50 mx-auto w-full max-w-6xl px-4 sm:px-6 lg:px-8">
    <div class="flex items-center justify-between rounded-3xl border {{ $baseClasses }} px-4 py-3 shadow-xl shadow-black/10">
        <a href="{{ route('dashboard') }}" class="flex items-center gap-3">
            <span class="flex h-11 w-11 items-center justify-center rounded-2xl bg-gradient-to-br from-amber-400 via-orange-400 to-rose-500 text-lg font-black text-slate-950 shadow-lg shadow-amber-500/20">C</span>
            <div>
                <p class="text-sm font-semibold tracking-[0.22em] text-amber-400 uppercase">Catbook</p>
                <p class="text-xs {{ $isDark ? 'text-slate-400' : 'text-slate-500' }}">Website bán sách tích hợp AI</p>
            </div>
        </a>

        <nav class="flex items-center gap-2 sm:gap-3">
            <a href="{{ route('dashboard') }}" class="rounded-2xl px-4 py-2 text-sm font-medium transition {{ $linkClasses }}">Dashboard</a>

            @auth
                @if (auth()->user()->role === 'admin')
                    <a href="{{ route('admin.panel') }}" class="rounded-2xl px-4 py-2 text-sm font-medium transition {{ $linkClasses }}">Admin</a>
                @endif

                @if (in_array(auth()->user()->role, ['admin', 'staff'], true))
                    <a href="{{ route('staff.panel') }}" class="rounded-2xl px-4 py-2 text-sm font-medium transition {{ $linkClasses }}">Staff</a>
                @endif

                @if (auth()->user()->role === 'customer')
                    <a href="{{ route('customer.panel') }}" class="rounded-2xl px-4 py-2 text-sm font-medium transition {{ $linkClasses }}">Customer</a>
                @endif

                <span class="hidden rounded-full px-3 py-2 text-sm font-medium sm:inline-flex {{ $isDark ? 'bg-emerald-500/10 text-emerald-300' : 'bg-emerald-50 text-emerald-700' }}">
                    {{ auth()->user()->full_name }} ({{ auth()->user()->role }})
                </span>
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
</header>
