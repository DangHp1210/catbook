@extends('layouts.app')

@section('title','Dashboard')

@section('content')
        <div class="mb-8 mt-6 rounded-3xl border border-slate-200 bg-white px-6 py-5 shadow-sm">
            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-500">Catbook</p>
            <h1 class="mt-2 text-2xl font-bold text-slate-900">Dashboard</h1>
        </div>

        <section class="grid gap-6 lg:grid-cols-3">
            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm lg:col-span-2">
                <p class="text-sm text-slate-500">Xin chào</p>
                <h2 class="mt-2 text-3xl font-bold text-slate-900">{{ auth()->user()->full_name }}</h2>
                <p class="mt-3 text-slate-600">Email: {{ auth()->user()->email }}</p>
                <p class="mt-2 text-slate-600">Vai trò: {{ auth()->user()->role }}</p>
                <p class="mt-2 text-slate-600">Trạng thái: {{ auth()->user()->status }}</p>
            </div>

            <div class="rounded-3xl border border-slate-200 bg-white p-6 shadow-sm">
                <p class="text-sm text-slate-500">Các bước tiếp theo</p>
                <ul class="mt-4 space-y-3 text-sm text-slate-600">
                    <li>1. Kết nối trang quản trị sách.</li>
                    <li>2. Áp middleware `role` cho toàn bộ CRUD theo vai trò.</li>
                    <li>3. Hoàn thiện chức năng riêng cho Admin / Staff / Customer.</li>
                </ul>
            </div>
        </section>
@endsection
