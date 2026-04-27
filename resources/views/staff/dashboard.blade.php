<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard Nhân viên - Catbook</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .staff-compact {
            font-size: 13px;
            line-height: 1.45;
        }

        .staff-compact .text-2xl {
            font-size: 1.3rem !important;
            line-height: 1.35 !important;
        }

        .staff-compact .text-lg {
            font-size: 0.98rem !important;
            line-height: 1.35 !important;
        }

        .staff-compact .text-sm {
            font-size: 0.8rem !important;
            line-height: 1.4 !important;
        }

        .staff-compact .p-5 {
            padding: 0.9rem !important;
        }
    </style>
</head>
<body class="min-h-screen bg-slate-100 text-slate-900">
    <x-navbar />

    <main class="staff-compact mx-auto w-full max-w-6xl space-y-4 px-2.5 pb-7 pt-4 sm:px-3.5 lg:px-4">
        <section class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h1 class="text-2xl font-bold text-slate-900">Bảng điều khiển Nhân viên</h1>
            <p class="mt-2 text-sm text-slate-500">Truy cập nhanh các chức năng vận hành được cấp quyền cho nhân viên.</p>
        </section>

        <section class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
            <a href="{{ route('admin.books.index') }}" class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-cyan-300 hover:shadow-md">
                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-400">Chức năng</p>
                <h2 class="mt-1 text-lg font-bold text-slate-900">Quản lý sách</h2>
                <p class="mt-2 text-sm text-slate-500">Cập nhật thông tin sách, tồn kho và trạng thái hiển thị.</p>
                <p class="mt-4 text-sm font-semibold text-cyan-600">Mở chức năng</p>
            </a>

            <a href="{{ route('admin.authors.index') }}" class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-cyan-300 hover:shadow-md">
                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-400">Chức năng</p>
                <h2 class="mt-1 text-lg font-bold text-slate-900">Quản lý tác giả</h2>
                <p class="mt-2 text-sm text-slate-500">Quản lý hồ sơ tác giả và thông tin mô tả liên quan.</p>
                <p class="mt-4 text-sm font-semibold text-cyan-600">Mở chức năng</p>
            </a>

            <a href="{{ route('admin.categories.index') }}" class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-cyan-300 hover:shadow-md">
                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-400">Chức năng</p>
                <h2 class="mt-1 text-lg font-bold text-slate-900">Quản lý danh mục</h2>
                <p class="mt-2 text-sm text-slate-500">Sắp xếp danh mục cha con và tối ưu phân loại sách.</p>
                <p class="mt-4 text-sm font-semibold text-cyan-600">Mở chức năng</p>
            </a>

            <a href="{{ route('admin.publishers.index') }}" class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-cyan-300 hover:shadow-md">
                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-400">Chức năng</p>
                <h2 class="mt-1 text-lg font-bold text-slate-900">Quản lý nhà xuất bản</h2>
                <p class="mt-2 text-sm text-slate-500">Cập nhật thông tin đối tác phát hành và liên hệ.</p>
                <p class="mt-4 text-sm font-semibold text-cyan-600">Mở chức năng</p>
            </a>

            <a href="{{ route('admin.orders.index') }}" class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-cyan-300 hover:shadow-md">
                <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-400">Chức năng</p>
                <h2 class="mt-1 text-lg font-bold text-slate-900">Quản lý đơn hàng</h2>
                <p class="mt-2 text-sm text-slate-500">Theo dõi và cập nhật trạng thái xử lý đơn hàng.</p>
                <p class="mt-4 text-sm font-semibold text-cyan-600">Mở chức năng</p>
            </a>
        </section>
    </main>
</body>
</html>
