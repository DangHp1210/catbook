@extends('layouts.app')

@section('title','Đăng nhập | CatBook')

@section('content')
<x-layouts.guest>
    <div class="w-full max-w-md rounded-3xl border border-slate-200 bg-white p-8 shadow-[0_20px_60px_rgba(13,27,16,0.08)] sm:p-10">
        <div class="mb-8 text-center">
            <p class="text-sm font-semibold uppercase tracking-[0.25em] text-emerald-700">Chào mừng trở lại</p>
            <h2 class="mt-3 text-3xl font-bold text-slate-900">Đăng nhập CatBook</h2>
        </div>

        @if ($errors->any())
            <div class="mb-5 rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-700">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.store') }}" class="space-y-5">
            @csrf
            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700" for="email">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10">
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700" for="password">Mật khẩu</label>
                <input id="password" name="password" type="password" required class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10">
            </div>

            {{-- remember me removed --}}

            <button type="submit" class="w-full rounded-full bg-slate-900 px-4 py-3 font-semibold text-white transition hover:bg-emerald-700">Đăng nhập</button>
        </form>

        <div class="my-6 flex items-center gap-3">
            <div class="h-px flex-1 bg-slate-200"></div>
            <span class="text-xs font-medium uppercase tracking-[0.18em] text-slate-400">Hoặc</span>
            <div class="h-px flex-1 bg-slate-200"></div>
        </div>

        <div class="grid gap-3">
            <a href="{{ route('login.provider', 'google') }}" class="inline-flex items-center justify-center gap-3 rounded-2xl border border-slate-200 bg-white px-4 py-3 text-sm font-semibold text-slate-700 transition hover:border-slate-300 hover:bg-slate-50">
                <svg width="18" height="18" viewBox="0 0 48 48" aria-hidden="true">
                    <path fill="#FFC107" d="M43.611 20.083H42V20H24v8h11.303C33.655 32.657 29.271 36 24 36c-6.627 0-12-5.373-12-12s5.373-12 12-12c3.059 0 5.842 1.154 7.961 3.038l5.657-5.657C34.029 6.053 29.268 4 24 4 12.955 4 4 12.955 4 24s8.955 20 20 20 20-8.955 20-20c0-1.341-.138-2.651-.389-3.917z"/>
                    <path fill="#FF3D00" d="M6.306 14.691l6.571 4.819C14.655 15.108 18.979 12 24 12c3.059 0 5.842 1.154 7.961 3.038l5.657-5.657C34.029 6.053 29.268 4 24 4c-7.688 0-14.411 4.337-17.694 10.691z"/>
                    <path fill="#4CAF50" d="M24 44c5.163 0 9.86-1.977 13.409-5.192l-6.19-5.238C29.089 35.091 26.715 36 24 36c-5.251 0-9.619-3.317-11.287-7.946l-6.522 5.025C9.44 39.556 16.186 44 24 44z"/>
                    <path fill="#1976D2" d="M43.611 20.083H42V20H24v8h11.303c-1.137 3.157-3.32 5.72-6.084 7.569l.002-.001 6.19 5.238C34.97 39.238 44 33 44 24c0-1.341-.138-2.651-.389-3.917z"/>
                </svg>
                Đăng nhập bằng Google
            </a>
        </div>

        <p class="mt-6 text-center text-sm text-slate-600">
            Chưa có tài khoản?
            <a href="{{ route('register') }}" class="font-semibold text-emerald-700 hover:text-emerald-800">Đăng ký ngay</a>
        </p>
    </div>
</x-layouts.guest>
@endsection
