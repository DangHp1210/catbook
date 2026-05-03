@extends('layouts.app')

@section('title','Thông tin tài khoản')

@section('content')
        @php
            $avatarPath = trim((string) ($user->avatar_url ?? ''));
            $avatarSrc = null;

            if ($avatarPath !== '') {
                $avatarSrc = \Illuminate\Support\Str::startsWith($avatarPath, ['http://', 'https://', '/'])
                    ? $avatarPath
                    : asset('storage/' . ltrim($avatarPath, '/'));
            }

            $initial = mb_strtoupper(mb_substr($user->full_name ?? '', 0, 1));
        @endphp

        <section class="grid gap-6 lg:grid-cols-[320px_1fr]">
            <aside class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-center gap-4">
                    <div class="flex h-16 w-16 items-center justify-center overflow-hidden rounded-2xl bg-gradient-to-br from-orange-400 to-rose-500 text-2xl font-black text-white">
                        @if($avatarSrc)
                            <img src="{{ $avatarSrc }}" alt="{{ $user->full_name }}" class="h-full w-full object-cover">
                        @else
                            {{ $initial }}
                        @endif
                    </div>
                    <div class="min-w-0">
                        <h1 class="text-2xl font-black text-slate-900">{{ $user->full_name }}</h1>
                </div>


                <form method="POST" action="{{ route('account.avatar.update') }}" enctype="multipart/form-data" class="mt-6 space-y-3">
                    @csrf

                    <div>
                        <label for="avatar_file" class="mb-2 block text-sm font-semibold text-slate-700">Ảnh đại diện</label>
                        <input id="avatar_file" name="avatar_file" type="file" accept="image/*" class="block w-full cursor-pointer rounded-xl border border-slate-300 px-3 py-2 text-sm text-slate-700 file:mr-4 file:rounded-lg file:border-0 file:bg-slate-900 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-white hover:file:bg-slate-700">
                        @error('avatar_file')
                            <p class="mt-2 text-sm text-rose-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <button type="submit" class="inline-flex items-center gap-2 rounded-xl bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-700">
                        Lưu ảnh đại diện
                    </button>
                </form>
                <div class="mt-6 space-y-3 text-sm">
                    <div class="flex items-center justify-between rounded-xl bg-slate-50 px-4 py-3">
                        <span class="text-slate-500">Email</span>
                        <span class="font-semibold text-slate-900">{{ $user->email }}</span>
                    </div>
                    <div class="flex items-center justify-between rounded-xl bg-slate-50 px-4 py-3">
                        <span class="text-slate-500">Số điện thoại</span>
                        <span class="font-semibold text-slate-900">{{ $user->phone ?? 'Chưa cập nhật' }}</span>
                    </div>
                </div>
            </aside>

            <section class="space-y-6">
                <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
                    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:col-span-2 xl:col-span-2">
                        <div class="mb-4 flex items-center justify-between gap-3">
                            <div>
                                <p class="text-xs uppercase tracking-[0.14em] text-slate-500">Địa chỉ</p>
                                <p class="mt-2 text-3xl font-black text-slate-900">{{ $user->addresses_count }}</p>
                            </div>
                            <a href="{{ route('account.addresses.index') }}" class="inline-flex items-center gap-2 rounded-lg border border-slate-200 px-3 py-2 text-sm font-medium text-slate-700 hover:bg-slate-50">Quản lý địa chỉ</a>
                        </div>
                    </article>
                    <a href="{{ route('orders.index') }}" class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm block hover:shadow-md">
                        <p class="text-xs uppercase tracking-[0.14em] text-slate-500">Đơn hàng</p>
                        <p class="mt-2 text-3xl font-black text-slate-900">{{ $user->orders_count }}</p>
                    </a>
                    <!-- Đánh giá card hidden per user request -->
                    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <p class="text-xs uppercase tracking-[0.14em] text-slate-500">Chat</p>
                        <p class="mt-2 text-3xl font-black text-slate-900">{{ $user->chat_sessions_count }}</p>
                    </article>
                </div>

                <div class="grid gap-6 lg:grid-cols-2">
                    {{-- Quick access removed as requested --}}
                </div>
            </section>
        </section>
@endsection
