@extends('layouts.app')

@section('title','Checkout')

@section('content')
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
                    <div class="rounded-2xl border border-orange-100 bg-gradient-to-br from-orange-50 via-white to-amber-50 p-4">
                        <div class="flex items-start gap-3">
                            <div class="flex h-11 w-11 shrink-0 items-center justify-center rounded-2xl bg-orange-500 text-sm font-black text-white shadow-sm">ON</div>
                            <div>
                                <h2 class="text-lg font-black text-slate-900">Phương thức thanh toán online</h2>
                                <p class="mt-1 text-sm text-slate-600">Chọn chuyển khoản ngân hàng, MoMo hoặc VNPay để thanh toán nhanh và thuận tiện.</p>
                            </div>
                        </div>

                        @php
                            $selectedMethod = old('payment_method', 'cod');
                            $onlineMethods = [
                                'bank_transfer' => [
                                    'title' => 'Chuyển khoản ngân hàng',
                                    'desc' => 'Quét QR hoặc chuyển khoản trực tiếp theo thông tin đơn hàng.',
                                    'tag' => 'Phổ biến',
                                    'badge' => 'BK',
                                    'accent' => 'orange',
                                ],
                                'momo' => [
                                    'title' => 'Ví MoMo',
                                    'desc' => 'Thanh toán bằng ví MoMo trên điện thoại, xác nhận trong vài giây.',
                                    'tag' => 'Nhanh',
                                    'badge' => 'M',
                                    'accent' => 'pink',
                                ],
                                'vnpay' => [
                                    'title' => 'VNPay',
                                    'desc' => 'Thanh toán qua cổng VNPay bằng ngân hàng, thẻ ATM hoặc thẻ quốc tế.',
                                    'tag' => 'An toàn',
                                    'badge' => 'V',
                                    'accent' => 'sky',
                                ],
                            ];
                        @endphp

                        <div class="mt-4 grid gap-3 lg:grid-cols-3">
                            @foreach ($onlineMethods as $value => $method)
                                @php
                                    $isSelected = $selectedMethod === $value;
                                    $accentClasses = [
                                        'orange' => 'bg-orange-100 text-orange-700 ring-orange-200',
                                        'pink' => 'bg-pink-100 text-pink-700 ring-pink-200',
                                        'sky' => 'bg-sky-100 text-sky-700 ring-sky-200',
                                    ];
                                    $selectedCardClass = $isSelected ? 'border-orange-300 bg-white shadow-[0_10px_30px_rgba(249,115,22,0.10)] ring-1 ring-orange-200' : 'border-slate-200 bg-white hover:border-orange-200 hover:shadow-sm';
                                    $badgeClass = $accentClasses[$method['accent']] ?? 'bg-slate-100 text-slate-700 ring-slate-200';
                                @endphp
                                <label class="group relative cursor-pointer rounded-2xl border p-4 transition {{ $selectedCardClass }}">
                                    <input type="radio" name="payment_method" value="{{ $value }}" class="peer sr-only" {{ $isSelected ? 'checked' : '' }}>

                                    <div class="flex items-start justify-between gap-3">
                                        <div class="flex items-center gap-3">
                                            <div class="flex h-11 w-11 items-center justify-center rounded-2xl bg-slate-900 text-sm font-black text-white transition group-hover:scale-[1.02]">
                                                {{ $method['badge'] }}
                                            </div>
                                            <div>
                                                <p class="text-sm font-bold text-slate-900">{{ $method['title'] }}</p>
                                                <p class="mt-0.5 text-xs text-slate-500">Thanh toán online</p>
                                            </div>
                                        </div>

                                        <span class="inline-flex rounded-full px-2.5 py-1 text-[11px] font-semibold ring-1 {{ $badgeClass }}">
                                            {{ $method['tag'] }}
                                        </span>
                                    </div>


                                    <p class="mt-4 text-sm leading-6 text-slate-600">{{ $method['desc'] }}</p>

                                    <div class="mt-4 rounded-xl bg-slate-50 px-3 py-2 text-xs font-medium text-slate-500">
                                        Nhấn để chọn phương thức này
                                    </div>

                                    @if($value === 'bank_transfer')
                                        <div class="bank-qr mt-3 hidden">
                                            <p class="text-xs text-slate-600 mb-2">Quét mã QR để chuyển khoản nhanh:</p>
                                            <img src="{{ asset('images/QRCode.jpg') }}" alt="QR chuyển khoản" class="w-full max-w-xs rounded-md border border-slate-200">
                                            <p class="mt-2 text-xs text-slate-500">Hoặc chuyển khoản theo thông tin trên hoá đơn sau khi hoàn tất đặt hàng.</p>
                                        </div>
                                    @endif

                                    @if ($isSelected)
                                        <div class="absolute right-4 top-4 flex h-8 w-8 items-center justify-center rounded-full bg-orange-500 text-white shadow-sm">✓</div>
                                    @endif
                                </label>
                            @endforeach
                        </div>

                        <div class="mt-4 rounded-2xl border border-slate-200 bg-white p-4">
                            <label class="flex cursor-pointer items-start gap-3">
                                <input type="radio" name="payment_method" value="cod" class="mt-1 h-4 w-4" {{ $selectedMethod === 'cod' ? 'checked' : '' }}>
                                <div>
                                    <p class="text-sm font-bold text-slate-900">Thanh toán khi nhận hàng (COD)</p>
                                    <p class="mt-1 text-sm text-slate-600">Dành cho khách muốn thanh toán trực tiếp cho nhân viên giao hàng.</p>
                                </div>
                            </label>
                        </div>

                        <div class="mt-4 rounded-2xl border border-dashed border-orange-200 bg-orange-50/60 p-4 text-sm text-slate-700">
                            <p class="font-semibold text-slate-900">Gợi ý thanh toán online</p>
                            <ul class="mt-2 space-y-1.5">
                                <li>• Chuyển khoản ngân hàng phù hợp khi bạn muốn đối soát nhanh bằng QR hoặc số tài khoản.</li>
                                <li>• MoMo tiện cho thanh toán trên điện thoại, đặc biệt khi đặt hàng thường xuyên.</li>
                                <li>• VNPay hỗ trợ nhiều ngân hàng và tạo cảm giác quen thuộc, an toàn khi thanh toán.</li>
                            </ul>
                        </div>
                    </div>
                </div>

                <div>
                    <label for="note" class="mb-1 block text-sm font-semibold text-slate-700">Ghi chú đơn hàng (không bắt buộc)</label>
                    <textarea id="note" name="note" rows="3" class="w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm outline-none transition focus:border-orange-400">{{ old('note') }}</textarea>
                </div>

                <div class="flex flex-wrap gap-3">
                    <button type="submit" class="rounded-xl bg-orange-500 px-5 py-2.5 text-sm font-semibold text-green transition hover:bg-orange-600">
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

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const qrBlock = document.querySelector('.bank-qr');
                const paymentInputs = document.querySelectorAll('input[name="payment_method"]');

                function updateQrVisibility() {
                    const selected = document.querySelector('input[name="payment_method"]:checked')?.value;
                    if (qrBlock) {
                        if (selected === 'bank_transfer') {
                            qrBlock.classList.remove('hidden');
                        } else {
                            qrBlock.classList.add('hidden');
                        }
                    }
                }

                paymentInputs.forEach(i => i.addEventListener('change', updateQrVisibility));
                // init on load
                updateQrVisibility();
            });
        </script>
@endsection
