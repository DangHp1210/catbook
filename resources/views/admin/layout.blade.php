<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? 'Admin Dashboard' }} - Catbook</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .admin-compact {
            font-size: 13px;
            line-height: 1.45;
        }

        .admin-compact .text-2xl {
            font-size: 1.35rem !important;
            line-height: 1.35 !important;
        }

        .admin-compact .text-xl {
            font-size: 1.1rem !important;
            line-height: 1.35 !important;
        }

        .admin-compact .text-lg {
            font-size: 0.98rem !important;
            line-height: 1.35 !important;
        }

        .admin-compact .text-sm {
            font-size: 0.8rem !important;
            line-height: 1.4 !important;
        }

        .admin-compact .p-5 {
            padding: 0.9rem !important;
        }

        .admin-compact .p-4 {
            padding: 0.8rem !important;
        }

        .admin-compact .px-5 {
            padding-left: 0.75rem !important;
            padding-right: 0.75rem !important;
        }

        .admin-compact .py-4 {
            padding-top: 0.6rem !important;
            padding-bottom: 0.6rem !important;
        }

        .admin-compact .py-3 {
            padding-top: 0.5rem !important;
            padding-bottom: 0.5rem !important;
        }

        .admin-compact input,
        .admin-compact select,
        .admin-compact textarea {
            min-height: 34px;
        }

        .admin-compact button,
        .admin-compact a {
            min-height: 32px;
        }

        .admin-compact .max-h-\[90vh\] {
            max-height: 85vh !important;
        }
    </style>
</head>
<body class="min-h-screen bg-slate-100 text-slate-900">
    <x-navbar />

    <main class="mx-auto flex w-full max-w-[1240px] gap-3 px-2.5 pb-7 pt-4 sm:px-3.5 lg:px-4">
        <aside class="hidden w-52 shrink-0 rounded-xl border border-slate-200 bg-white p-2.5 shadow-sm 2xl:block">
            <h2 class="text-xs font-semibold uppercase tracking-[0.16em] text-slate-500">Admin Menu</h2>
            <nav class="mt-2.5 space-y-1">
                <a href="{{ route('admin.panel') }}" class="block rounded-lg px-2 py-1.5 text-xs font-medium transition {{ request()->routeIs('admin.panel') ? 'bg-orange-500 text-white' : 'text-slate-700 hover:bg-slate-100' }}">Tổng quan</a>
                <a href="{{ route('admin.users.index') }}" class="block rounded-lg px-2 py-1.5 text-xs font-medium transition {{ request()->routeIs('admin.users.*') ? 'bg-orange-500 text-white' : 'text-slate-700 hover:bg-slate-100' }}">Quản lý người dùng</a>
                <a href="{{ route('admin.books.index') }}" class="block rounded-lg px-2 py-1.5 text-xs font-medium transition {{ request()->routeIs('admin.books.*') ? 'bg-orange-500 text-white' : 'text-slate-700 hover:bg-slate-100' }}">Quản lý sách</a>
                <a href="{{ route('admin.authors.index') }}" class="block rounded-lg px-2 py-1.5 text-xs font-medium transition {{ request()->routeIs('admin.authors.*') ? 'bg-orange-500 text-white' : 'text-slate-700 hover:bg-slate-100' }}">Quản lý tác giả</a>
                <a href="{{ route('admin.categories.index') }}" class="block rounded-lg px-2 py-1.5 text-xs font-medium transition {{ request()->routeIs('admin.categories.*') ? 'bg-orange-500 text-white' : 'text-slate-700 hover:bg-slate-100' }}">Quản lý danh mục</a>
                <a href="{{ route('admin.publishers.index') }}" class="block rounded-lg px-2 py-1.5 text-xs font-medium transition {{ request()->routeIs('admin.publishers.*') ? 'bg-orange-500 text-white' : 'text-slate-700 hover:bg-slate-100' }}">Quản lý nhà xuất bản</a>
                <a href="{{ route('admin.orders.index') }}" class="block rounded-lg px-2 py-1.5 text-xs font-medium transition {{ request()->routeIs('admin.orders.*') ? 'bg-orange-500 text-white' : 'text-slate-700 hover:bg-slate-100' }}">Quản lý đơn hàng</a>
                <a href="{{ route('admin.revenue.index') }}" class="block rounded-lg px-2 py-1.5 text-xs font-medium transition {{ request()->routeIs('admin.revenue.*') ? 'bg-orange-500 text-white' : 'text-slate-700 hover:bg-slate-100' }}">Thống kê doanh thu</a>
            </nav>
        </aside>

        <section class="admin-compact min-w-0 flex-1 space-y-3.5">
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
