@extends('layouts.admin')

@section('title','Bảng điều khiển Nhân viên')

@section('content')
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
@endsection
