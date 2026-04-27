<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Admin Dashboard' }} - Catbook</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-100 text-slate-900">
    <x-navbar />

    <main class="mx-auto flex w-full max-w-[1200px] gap-4 px-3 pb-8 pt-5 sm:px-4 lg:px-5">
        <aside class="hidden w-56 shrink-0 rounded-xl border border-slate-200 bg-white p-3 shadow-sm xl:block">
            <h2 class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Admin Menu</h2>
            <nav class="mt-3 space-y-1.5">
                <a href="{{ route('admin.panel') }}" class="block rounded-lg px-2.5 py-2 text-sm font-medium transition {{ request()->routeIs('admin.panel') ? 'bg-orange-500 text-white' : 'text-slate-700 hover:bg-slate-100' }}">Tổng quan</a>
                <a href="{{ route('admin.users.index') }}" class="block rounded-lg px-2.5 py-2 text-sm font-medium transition {{ request()->routeIs('admin.users.*') ? 'bg-orange-500 text-white' : 'text-slate-700 hover:bg-slate-100' }}">Quản lý người dùng</a>
                <a href="{{ route('admin.books.index') }}" class="block rounded-lg px-2.5 py-2 text-sm font-medium transition {{ request()->routeIs('admin.books.*') ? 'bg-orange-500 text-white' : 'text-slate-700 hover:bg-slate-100' }}">Quản lý sách</a>
                <a href="{{ route('admin.categories.index') }}" class="block rounded-lg px-2.5 py-2 text-sm font-medium transition {{ request()->routeIs('admin.categories.*') ? 'bg-orange-500 text-white' : 'text-slate-700 hover:bg-slate-100' }}">Quản lý danh mục</a>
                <a href="{{ route('admin.publishers.index') }}" class="block rounded-lg px-2.5 py-2 text-sm font-medium transition {{ request()->routeIs('admin.publishers.*') ? 'bg-orange-500 text-white' : 'text-slate-700 hover:bg-slate-100' }}">Quản lý nhà xuất bản</a>
                <a href="{{ route('admin.orders.index') }}" class="block rounded-lg px-2.5 py-2 text-sm font-medium transition {{ request()->routeIs('admin.orders.*') ? 'bg-orange-500 text-white' : 'text-slate-700 hover:bg-slate-100' }}">Quản lý đơn hàng</a>
                <a href="{{ route('admin.revenue.index') }}" class="block rounded-lg px-2.5 py-2 text-sm font-medium transition {{ request()->routeIs('admin.revenue.*') ? 'bg-orange-500 text-white' : 'text-slate-700 hover:bg-slate-100' }}">Thống kê doanh thu</a>
            </nav>
        </aside>

        <section class="min-w-0 flex-1 space-y-4">
            @if(session('success'))
                <div class="rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
                    {{ session('error') }}
                </div>
            @endif

            @if ($errors->any())
                <div class="rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
                    <p class="font-semibold">Có lỗi dữ liệu:</p>
                    <ul class="mt-2 list-disc pl-5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </section>
    </main>
</body>
</html>
