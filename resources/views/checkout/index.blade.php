<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Catbook | Checkout</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="cb-site text-slate-800">
    <x-navbar />

    <main class="cb-page">
        <div class="mb-6">
            <p class="text-xs uppercase tracking-[0.16em] text-slate-500">Thanh toán</p>
            <h1 class="mt-1 text-3xl font-black text-slate-900">Xác nhận đơn hàng</h1>
        </div>

        @if ($errors->any())
            <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                {{ $errors->first() }}
            </div>
        @endif

        <section class="grid gap-6 lg:grid-cols-[1fr_360px]">
            <form method="POST" action="{{ route('checkout.store') }}" class="space-y-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm sm:p-6">
                @csrf

                <div>
                    <h2 class="text-lg font-black text-slate-900">Thông tin nhận hàng</h2>
                    <div class="mt-4 grid gap-4 sm:grid-cols-2">
                        <div>
                            <label for="recipient_name" class="mb-1 block text-sm font-semibold text-slate-700">Người nhận</label>
                            <input id="recipient_name" name="recipient_name" type="text" required value="{{ old('recipient_name', $defaultAddress?->receiver_name ?? auth()->user()->full_name) }}" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none transition focus:border-orange-400">
                        </div>
                        <div>
                            <label for="recipient_phone" class="mb-1 block text-sm font-semibold text-slate-700">Số điện thoại</label>
                            <input id="recipient_phone" name="recipient_phone" type="text" required value="{{ old('recipient_phone', $defaultAddress?->receiver_phone ?? auth()->user()->phone) }}" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none transition focus:border-orange-400">
                        </div>
                    </div>

                    <div class="mt-4">
                        <label for="shipping_address" class="mb-1 block text-sm font-semibold text-slate-700">Địa chỉ giao hàng</label>
                        @php
                            $fallbackAddress = $defaultAddress
                                ? collect([$defaultAddress->address_line, $defaultAddress->ward, $defaultAddress->district, $defaultAddress->province])->filter()->implode(', ')
                                : null;
                        @endphp
                        <textarea id="shipping_address" name="shipping_address" rows="3" required class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none transition focus:border-orange-400">{{ old('shipping_address', $fallbackAddress) }}</textarea>
                    </div>
                </div>

                <div>
                    <h2 class="text-lg font-black text-slate-900">Phương thức thanh toán</h2>
                    <div class="mt-3 grid gap-3 sm:grid-cols-2">
                        @php
                            $methods = [
                                'cod' => 'Thanh toán khi nhận hàng (COD)',
                                'bank_transfer' => 'Chuyển khoản ngân hàng',
                                'momo' => 'Ví MoMo',
                                'vnpay' => 'VNPay',
                            ];
                            $selectedMethod = old('payment_method', 'cod');
                        @endphp
                        @foreach ($methods as $value => $label)
                            <label class="flex cursor-pointer items-center gap-2 rounded-xl border px-3 py-2.5 text-sm transition {{ $selectedMethod === $value ? 'border-orange-300 bg-orange-50 text-orange-700' : 'border-slate-200 bg-white text-slate-700 hover:border-orange-300' }}">
                                <input type="radio" name="payment_method" value="{{ $value }}" class="h-4 w-4" {{ $selectedMethod === $value ? 'checked' : '' }}>
                                <span>{{ $label }}</span>
                            </label>
                        @endforeach
                    </div>
                </div>

                <div>
                    <label for="note" class="mb-1 block text-sm font-semibold text-slate-700">Ghi chú đơn hàng (không bắt buộc)</label>
                    <textarea id="note" name="note" rows="3" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none transition focus:border-orange-400">{{ old('note') }}</textarea>
                </div>

                <div class="flex flex-wrap gap-3">
                    <button type="submit" class="rounded-xl bg-orange-500 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-orange-600">
                        Đặt hàng ngay
                    </button>
                    <a href="{{ route('cart.index') }}" class="rounded-xl border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 transition hover:border-orange-300 hover:text-orange-700">
                        Quay lại giỏ hàng
                    </a>
                </div>
            </form>

            <aside class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <h2 class="text-lg font-black text-slate-900">Đơn hàng của bạn</h2>
                <div class="mt-4 space-y-3">
                    @foreach ($items as $item)
                        <div class="rounded-xl border border-slate-200 px-3 py-3">
                            <p class="line-clamp-2 text-sm font-semibold text-slate-900">{{ $item->book->title }}</p>
                            <div class="mt-1 flex items-center justify-between text-xs text-slate-500">
                                <span>SL: {{ $item->quantity }}</span>
                                <span>{{ number_format((float) $item->unit_price * $item->quantity, 0, ',', '.') }}đ</span>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="mt-5 space-y-2 border-t border-slate-200 pt-4 text-sm">
                    <div class="flex items-center justify-between">
                        <span class="text-slate-600">Tạm tính</span>
                        <span class="font-semibold text-slate-900">{{ number_format($subtotal, 0, ',', '.') }}đ</span>
                    </div>
                    <div class="flex items-center justify-between">
                        <span class="text-slate-600">Phí vận chuyển</span>
                        <span class="font-semibold text-slate-900">{{ number_format($shippingFee, 0, ',', '.') }}đ</span>
                    </div>
                    <div class="flex items-center justify-between border-t border-slate-200 pt-3 text-base">
                        <span class="font-bold text-slate-900">Tổng thanh toán</span>
                        <span class="text-xl font-black text-orange-600">{{ number_format($total, 0, ',', '.') }}đ</span>
                    </div>
                </div>
            </aside>
        </section>
    </main>
</body>
</html>
