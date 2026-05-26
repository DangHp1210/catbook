@extends('layouts.app')

@section('title','Đăng ký | CatBook')

@section('content')
<x-layouts.guest>
    <div class="w-full max-w-md rounded-3xl border border-slate-200 bg-white p-8 shadow-[0_20px_60px_rgba(13,27,16,0.08)] sm:p-10">
        <div class="mb-8 text-center">
            <p class="text-sm font-semibold uppercase tracking-[0.25em] text-emerald-700">Bắt đầu ngay</p>
            <h2 class="mt-3 text-3xl font-bold text-slate-900">Đăng ký CatBook</h2>
        </div>

        @if ($errors->any())
            <div class="mb-5 rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-700">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('register.store') }}" class="space-y-5">
            @csrf
            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700" for="full_name">Họ và tên</label>
                <input id="full_name" name="full_name" type="text" value="{{ old('full_name') }}" required autofocus class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10" placeholder="Nguyễn Văn A">
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700" for="email">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10" placeholder="you@example.com">
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700" for="phone">Số điện thoại</label>
                <input id="phone" name="phone" type="text" value="{{ old('phone') }}" class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10" placeholder="09xxxxxxxx">
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700" for="password">Mật khẩu</label>
                <input id="password" name="password" type="password" required class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10" placeholder="Tối thiểu 8 ký tự">
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700" for="password_confirmation">Xác nhận mật khẩu</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10" placeholder="Nhập lại mật khẩu">
            </div>

            <button type="submit" class="w-full rounded-full bg-slate-900 px-4 py-3 font-semibold text-white transition hover:bg-emerald-700">Tạo tài khoản</button>
        </form>

        <p class="mt-6 text-center text-sm text-slate-600">
            Đã có tài khoản?
            <a href="{{ route('login') }}" class="font-semibold text-emerald-700 hover:text-emerald-800">Đăng nhập</a>
        </p>
    </div>
</x-layouts.guest>
@endsection
