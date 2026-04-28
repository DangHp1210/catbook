<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Catbook | Thông tin tài khoản</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="cb-site text-slate-800">
    <x-navbar />

    <main class="cb-page">
        <section class="grid gap-6 lg:grid-cols-[320px_1fr]">
            <aside class="rounded-2xl border border-slate-200 bg-white p-6 shadow-sm">
                <div class="flex items-center gap-4">
                    <div class="flex h-16 w-16 items-center justify-center rounded-2xl bg-gradient-to-br from-orange-400 to-rose-500 text-2xl font-black text-white">
                        {{ strtoupper(mb_substr($user->full_name, 0, 1)) }}
                    </div>
                    <div>
                        <h1 class="text-2xl font-black text-slate-900">{{ $user->full_name }}</h1>
                        <p class="text-sm text-slate-500">Tài khoản {{ $user->role }}</p>
                    </div>
                </div>

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

                        <div class="space-y-3">
                            @forelse ($user->addresses()->latest()->limit(3)->get() as $address)
                                <a href="{{ route('account.addresses.index') }}" class="block rounded-xl border border-slate-200 px-4 py-3 transition hover:border-emerald-200 hover:bg-emerald-50/50">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <p class="font-semibold text-slate-900">{{ $address->receiver_name }}</p>
                                            <p class="mt-1 text-sm text-slate-600">{{ $address->address_line }}</p>
                                            <p class="text-xs text-slate-500">{{ collect([$address->ward, $address->district, $address->province])->filter()->implode(', ') }}</p>
                                        </div>
                                        @if($address->is_default)
                                            <span class="rounded-full bg-emerald-50 px-2.5 py-1 text-xs font-semibold text-emerald-700">Mặc định</span>
                                        @endif
                                    </div>
                                </a>
                            @empty
                                <a href="{{ route('account.addresses.index') }}" class="block rounded-xl border border-dashed border-slate-200 px-4 py-3 text-sm text-slate-500 transition hover:border-emerald-200 hover:bg-emerald-50/50 hover:text-emerald-700">
                                    Chưa có địa chỉ nào. Nhấn để quản lý địa chỉ.
                                </a>
                            @endforelse
                        </div>
                    </article>
                    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <p class="text-xs uppercase tracking-[0.14em] text-slate-500">Đơn hàng</p>
                        <p class="mt-2 text-3xl font-black text-slate-900">{{ $user->orders_count }}</p>
                    </article>
                    <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                        <p class="text-xs uppercase tracking-[0.14em] text-slate-500">Đánh giá</p>
                        <p class="mt-2 text-3xl font-black text-slate-900">{{ $user->reviews_count }}</p>
                    </article>
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
    </main>
</body>
</html>
