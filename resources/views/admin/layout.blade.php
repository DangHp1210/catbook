<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Admin Dashboard' }} - Catbook</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="cb-site text-slate-900">
    <x-navbar />

    <main class="cb-admin-shell">
        <aside class="cb-admin-menu">
            <h2 class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Admin Menu</h2>
            <nav class="mt-2.5 space-y-1">
                <a href="{{ route('admin.panel') }}" class="cb-admin-link {{ request()->routeIs('admin.panel') ? 'cb-admin-link-active' : '' }}">Tổng quan</a>
                <a href="{{ route('admin.users.index') }}" class="cb-admin-link {{ request()->routeIs('admin.users.*') ? 'cb-admin-link-active' : '' }}">Quản lý người dùng</a>
                <a href="{{ route('admin.books.index') }}" class="cb-admin-link {{ request()->routeIs('admin.books.*') ? 'cb-admin-link-active' : '' }}">Quản lý sách</a>
                <a href="{{ route('admin.authors.index') }}" class="cb-admin-link {{ request()->routeIs('admin.authors.*') ? 'cb-admin-link-active' : '' }}">Quản lý tác giả</a>
                <a href="{{ route('admin.categories.index') }}" class="cb-admin-link {{ request()->routeIs('admin.categories.*') ? 'cb-admin-link-active' : '' }}">Quản lý danh mục</a>
                <a href="{{ route('admin.publishers.index') }}" class="cb-admin-link {{ request()->routeIs('admin.publishers.*') ? 'cb-admin-link-active' : '' }}">Quản lý nhà xuất bản</a>
                <a href="{{ route('admin.orders.index') }}" class="cb-admin-link {{ request()->routeIs('admin.orders.*') ? 'cb-admin-link-active' : '' }}">Quản lý đơn hàng</a>
                <a href="{{ route('admin.revenue.index') }}" class="cb-admin-link {{ request()->routeIs('admin.revenue.*') ? 'cb-admin-link-active' : '' }}">Thống kê doanh thu</a>
            </nav>
        </aside>

        <section class="min-w-0 flex-1 space-y-3.5">
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
