<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Catbook | Chi tiết đơn hàng {{ $order->order_code }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#f7f8fc] text-slate-800">
    <x-navbar />

    <main class="mx-auto max-w-6xl px-4 pb-12 pt-6 sm:px-6 lg:px-8">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <p class="text-xs uppercase tracking-[0.16em] text-slate-500">Chi tiết đơn hàng</p>
                <h1 class="mt-1 text-3xl font-black text-slate-900">{{ $order->order_code }}</h1>
            </div>
            <a href="{{ route('orders.index') }}" class="text-sm font-semibold text-orange-600 hover:text-orange-700">← Quay lại lịch sử đơn</a>
        </div>

        @if (session('success'))
            <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                {{ session('error') }}
            </div>
        @endif

        <section class="grid gap-6 lg:grid-cols-[1fr_340px]">
            <article class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                <h2 class="text-xl font-black text-slate-900">Sản phẩm trong đơn</h2>
                <div class="mt-4 space-y-3">
                    @foreach ($order->items as $item)
                        <div class="rounded-xl border border-slate-200 px-4 py-3">
                            <p class="font-semibold text-slate-900">{{ $item->book_title_snapshot }}</p>
                            <div class="mt-1 flex items-center justify-between text-sm text-slate-600">
                                <span>Đơn giá: {{ number_format((float) $item->unit_price, 0, ',', '.') }}đ</span>
                                <span>Số lượng: {{ $item->quantity }}</span>
                            </div>
                            <p class="mt-1 text-right text-sm font-bold text-orange-600">{{ number_format((float) $item->total_price, 0, ',', '.') }}đ</p>
                        </div>
                    @endforeach
                </div>

                @if ($order->order_status === 'pending')
                    <form method="POST" action="{{ route('orders.cancel', $order) }}" class="mt-5" onsubmit="return confirm('Bạn chắc chắn muốn hủy đơn hàng này?');">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="rounded-xl bg-rose-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-rose-500">
                            Hủy đơn
                        </button>
                    </form>
                @endif
            </article>

            <aside class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-black text-slate-900">Thông tin đơn hàng</h2>

                <div class="mt-4 space-y-2 text-sm text-slate-600">
                    <div class="flex items-center justify-between">
                        <span>Ngày đặt</span>
                        <span class="font-semibold text-slate-900">{{ $order->created_at?->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Người nhận</span>
                        <span class="font-semibold text-slate-900">{{ $order->recipient_name }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>SĐT</span>
                        <span class="font-semibold text-slate-900">{{ $order->recipient_phone }}</span>
                    </div>
                    <div>
                        <p class="text-slate-500">Địa chỉ giao</p>
                        <p class="font-semibold text-slate-900">{{ $order->shipping_address }}</p>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Thanh toán</span>
                        <span class="font-semibold text-slate-900">{{ $order->payment_method }}</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Trạng thái đơn</span>
                        <span class="rounded-full px-2.5 py-1 text-xs font-semibold
                            {{ $order->order_status === 'completed' ? 'bg-emerald-100 text-emerald-700' : '' }}
                            {{ $order->order_status === 'pending' ? 'bg-amber-100 text-amber-700' : '' }}
                            {{ $order->order_status === 'shipping' ? 'bg-sky-100 text-sky-700' : '' }}
                            {{ $order->order_status === 'cancelled' ? 'bg-rose-100 text-rose-700' : '' }}
                            {{ $order->order_status === 'confirmed' ? 'bg-indigo-100 text-indigo-700' : '' }}
                        ">
                            {{ $order->order_status }}
                        </span>
                    </div>
                    <div class="flex items-center justify-between border-t border-slate-200 pt-2">
                        <span>Tạm tính</span>
                        <span class="font-semibold text-slate-900">{{ number_format((float) $order->subtotal, 0, ',', '.') }}đ</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span>Phí ship</span>
                        <span class="font-semibold text-slate-900">{{ number_format((float) $order->shipping_fee, 0, ',', '.') }}đ</span>
                    </div>
                    <div class="flex items-center justify-between text-base font-bold">
                        <span class="text-slate-900">Tổng thanh toán</span>
                        <span class="text-orange-600">{{ number_format((float) $order->total_amount, 0, ',', '.') }}đ</span>
                    </div>
                </div>
            </aside>
        </section>
    </main>
</body>
</html>
