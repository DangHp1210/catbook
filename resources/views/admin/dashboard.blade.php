@extends('admin.layout', ['title' => 'Dashboard Admin'])

@section('content')
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <h1 class="text-2xl font-bold text-slate-900">Bảng điều khiển Admin</h1>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        <a href="{{ route('admin.users.index') }}" class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-orange-300 hover:shadow-md">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-400">Chức năng</p>
                    <h2 class="mt-1 text-lg font-bold text-slate-900">Quản lý người dùng</h2>
                    <p class="mt-2 text-sm text-slate-500">Phân quyền và kiểm soát trạng thái tài khoản.</p>
                </div>
                <span class="rounded-xl bg-orange-50 px-3 py-1 text-sm font-semibold text-orange-700">{{ number_format($stats['users']) }}</span>
            </div>
            <p class="mt-4 text-sm font-semibold text-orange-600">Mở chức năng</p>
        </a>

        <a href="{{ route('admin.books.index') }}" class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-orange-300 hover:shadow-md">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-400">Chức năng</p>
                    <h2 class="mt-1 text-lg font-bold text-slate-900">Quản lý sách</h2>
                    <p class="mt-2 text-sm text-slate-500">Cập nhật giá, tồn kho và trạng thái hiển thị.</p>
                </div>
                <span class="rounded-xl bg-orange-50 px-3 py-1 text-sm font-semibold text-orange-700">{{ number_format($stats['books']) }}</span>
            </div>
            <p class="mt-4 text-sm font-semibold text-orange-600">Mở chức năng</p>
        </a>

        <a href="{{ route('admin.authors.index') }}" class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-orange-300 hover:shadow-md">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-400">Chức năng</p>
                    <h2 class="mt-1 text-lg font-bold text-slate-900">Quản lý tác giả</h2>
                    <p class="mt-2 text-sm text-slate-500">Cập nhật hồ sơ tác giả và kiểm soát dữ liệu liên quan.</p>
                </div>
                <span class="rounded-xl bg-orange-50 px-3 py-1 text-sm font-semibold text-orange-700">{{ number_format($stats['authors']) }}</span>
            </div>
            <p class="mt-4 text-sm font-semibold text-orange-600">Mở chức năng</p>
        </a>

        <a href="{{ route('admin.categories.index') }}" class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-orange-300 hover:shadow-md">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-400">Chức năng</p>
                    <h2 class="mt-1 text-lg font-bold text-slate-900">Quản lý danh mục</h2>
                    <p class="mt-2 text-sm text-slate-500">Tổ chức cây danh mục và phân loại sản phẩm.</p>
                </div>
                <span class="rounded-xl bg-orange-50 px-3 py-1 text-sm font-semibold text-orange-700">{{ number_format($stats['categories']) }}</span>
            </div>
            <p class="mt-4 text-sm font-semibold text-orange-600">Mở chức năng</p>
        </a>

        <a href="{{ route('admin.publishers.index') }}" class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-orange-300 hover:shadow-md">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-400">Chức năng</p>
                    <h2 class="mt-1 text-lg font-bold text-slate-900">Quản lý nhà xuất bản</h2>
                    <p class="mt-2 text-sm text-slate-500">Quản lý thông tin liên hệ và hệ thống đối tác.</p>
                </div>
                <span class="rounded-xl bg-orange-50 px-3 py-1 text-sm font-semibold text-orange-700">{{ number_format($stats['publishers']) }}</span>
            </div>
            <p class="mt-4 text-sm font-semibold text-orange-600">Mở chức năng</p>
        </a>

        <a href="{{ route('admin.orders.index') }}" class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-orange-300 hover:shadow-md">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-400">Chức năng</p>
                    <h2 class="mt-1 text-lg font-bold text-slate-900">Quản lý đơn hàng</h2>
                    <p class="mt-2 text-sm text-slate-500">Theo dõi tiến độ xử lý và trạng thái thanh toán.</p>
                </div>
                <span class="rounded-xl bg-orange-50 px-3 py-1 text-sm font-semibold text-orange-700">{{ number_format($stats['orders']) }}</span>
            </div>
            <p class="mt-4 text-sm font-semibold text-orange-600">Mở chức năng</p>
        </a>

        <a href="{{ route('admin.revenue.index') }}" class="group rounded-2xl border border-slate-200 bg-white p-5 shadow-sm transition hover:-translate-y-0.5 hover:border-orange-300 hover:shadow-md">
            <div class="flex items-start justify-between gap-3">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.14em] text-slate-400">Chức năng</p>
                    <h2 class="mt-1 text-lg font-bold text-slate-900">Thống kê doanh thu</h2>
                    <p class="mt-2 text-sm text-slate-500">Xem báo cáo doanh thu theo năm, tháng và phương thức.</p>
                </div>
                <span class="rounded-xl bg-emerald-50 px-3 py-1 text-sm font-semibold text-emerald-700">{{ number_format($stats['revenue'], 0, ',', '.') }} đ</span>
            </div>
            <p class="mt-4 text-sm font-semibold text-orange-600">Mở chức năng</p>
        </a>
    </div>

@endsection
