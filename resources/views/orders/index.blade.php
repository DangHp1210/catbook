<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Catbook | Lịch sử đơn hàng</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="cb-site text-slate-800">
    <x-navbar />

    <main class="cb-page">
        <div class="mb-6 flex items-center justify-between">
            <div>
                <p class="text-xs uppercase tracking-[0.16em] text-slate-500">Đơn hàng</p>
                <h1 class="mt-1 text-3xl font-black text-slate-900">Lịch sử đơn hàng của bạn</h1>
            </div>
            <a href="{{ route('catalog.categories') }}" class="text-sm font-semibold text-orange-600 hover:text-orange-700">Tiếp tục mua sách</a>
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

        @if ($orders->isEmpty())
            <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-8 text-center text-slate-500">
                <p class="text-lg font-semibold text-slate-700">Bạn chưa có đơn hàng nào.</p>
                <p class="mt-2 text-sm">Hãy chọn vài cuốn sách và thanh toán để tạo đơn đầu tiên.</p>
                <a href="{{ route('catalog.categories') }}" class="mt-5 inline-flex rounded-xl bg-orange-500 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-orange-600">
                    Mua sách ngay
                </a>
            </div>
        @else
            <section class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-slate-200 text-sm">
                        <thead class="bg-slate-50">
                            <tr>
                                <th class="px-4 py-3 text-left font-semibold text-slate-600">Mã đơn</th>
                                <th class="px-4 py-3 text-left font-semibold text-slate-600">Ngày đặt</th>
                                <th class="px-4 py-3 text-left font-semibold text-slate-600">Sản phẩm</th>
                                <th class="px-4 py-3 text-left font-semibold text-slate-600">Thanh toán</th>
                                <th class="px-4 py-3 text-left font-semibold text-slate-600">Trạng thái</th>
                                <th class="px-4 py-3 text-right font-semibold text-slate-600">Tổng tiền</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($orders as $order)
                                <tr class="hover:bg-slate-50/80">
                                    <td class="px-4 py-3 font-semibold text-slate-900">
                                        <a href="{{ route('orders.show', $order) }}" class="hover:text-orange-700 hover:underline">
                                            {{ $order->order_code }}
                                        </a>
                                    </td>
                                    <td class="px-4 py-3 text-slate-600">{{ $order->created_at?->format('d/m/Y H:i') }}</td>
                                    <td class="px-4 py-3 text-slate-600">{{ $order->items_count }} sản phẩm</td>
                                    <td class="px-4 py-3 text-slate-600">{{ $order->payment_method }}</td>
                                    <td class="px-4 py-3">
                                        <span class="rounded-full px-2.5 py-1 text-xs font-semibold
                                            {{ $order->order_status === 'completed' ? 'bg-emerald-100 text-emerald-700' : '' }}
                                            {{ $order->order_status === 'pending' ? 'bg-amber-100 text-amber-700' : '' }}
                                            {{ $order->order_status === 'shipping' ? 'bg-sky-100 text-sky-700' : '' }}
                                            {{ in_array($order->order_status, ['cancelled', 'refunded'], true) ? 'bg-rose-100 text-rose-700' : '' }}
                                            {{ $order->order_status === 'confirmed' ? 'bg-indigo-100 text-indigo-700' : '' }}
                                        ">
                                            {{ $order->order_status }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-right font-bold text-orange-600">{{ number_format((float) $order->total_amount, 0, ',', '.') }}đ</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>

                <div class="border-t border-slate-200 px-4 py-3">
                    {{ $orders->links() }}
                </div>
            </section>
        @endif
    </main>
</body>
</html>
