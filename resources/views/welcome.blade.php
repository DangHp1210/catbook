<!DOCTYPE html>
<html lang="vi">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>Catbook</title>
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-slate-950 text-slate-100">
        <div class="relative isolate min-h-screen overflow-hidden">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,_rgba(251,191,36,0.22),_transparent_32%),radial-gradient(circle_at_right,_rgba(14,165,233,0.18),_transparent_28%),linear-gradient(180deg,#0f172a_0%,#020617_100%)]"></div>
            <x-navbar :dark="true" />

            <main class="relative mx-auto flex min-h-[calc(100vh-6rem)] max-w-6xl items-center px-4 py-10 sm:px-6 lg:px-8">
                <section class="max-w-3xl">
                    <p class="text-sm font-semibold uppercase tracking-[0.35em] text-amber-300/90">Catbook AI</p>
                    <h1 class="mt-5 text-5xl font-black leading-tight sm:text-6xl">Website bán sách trực tuyến với AI tư vấn thông minh.</h1>
                    <p class="mt-6 max-w-2xl text-lg leading-8 text-slate-300">Đăng nhập để mua sách, quản lý giỏ hàng, theo dõi đơn hàng và trò chuyện với trợ lý AI gợi ý sách theo nhu cầu.</p>

                    <div class="mt-8 flex flex-wrap gap-4">
                        @auth
                            <a href="{{ route('dashboard') }}" class="rounded-2xl bg-amber-400 px-6 py-3 font-semibold text-slate-950 transition hover:bg-amber-300">Vào Dashboard</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="rounded-2xl border border-white/10 bg-white/5 px-6 py-3 font-semibold text-white transition hover:bg-white/10">Đăng xuất</button>
                            </form>
                        @else
                            <a href="{{ route('login') }}" class="rounded-2xl bg-amber-400 px-6 py-3 font-semibold text-slate-950 transition hover:bg-amber-300">Đăng nhập</a>
                            <a href="{{ route('register') }}" class="rounded-2xl border border-white/10 bg-white/5 px-6 py-3 font-semibold text-white transition hover:bg-white/10">Đăng ký</a>
                        @endauth
                    </div>
                </section>
            </main>
        </div>
    </body>
</html>
