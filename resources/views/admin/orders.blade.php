@extends('admin.layout', ['title' => 'Quản lý đơn hàng'])

@section('content')
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h1 class="text-xl font-bold text-slate-900">Quản lý đơn hàng</h1>
            <form method="GET" class="w-full max-w-sm">
                <input name="q" value="{{ $q }}" placeholder="Tìm mã đơn, người nhận, SĐT..." class="w-full rounded-xl border border-slate-300 px-4 py-2 text-sm" />
            </form>
        </div>
    </div>

    <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-sm">
        <table class="min-w-full text-left text-sm">
            <thead>
                <tr class="border-b border-slate-200 text-slate-500">
                    <th class="px-3 py-2">Mã đơn</th>
                    <th class="px-3 py-2">Khách hàng</th>
                    <th class="px-3 py-2">Tổng tiền</th>
                    <th class="px-3 py-2">Trạng thái</th>
                    <th class="px-3 py-2">Thanh toán</th>
                    <th class="px-3 py-2">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($orders as $order)
                    <tr class="border-b border-slate-100 align-top">
                        <td class="px-3 py-3 font-semibold text-slate-900">{{ $order->order_code }}</td>
                        <td class="px-3 py-3 text-slate-700">
                            <p>{{ $order->user?->full_name ?? 'N/A' }}</p>
                            <p class="text-slate-500">{{ $order->recipient_name }} | {{ $order->recipient_phone }}</p>
                            <p class="text-slate-400">SP: {{ $order->items_count }}</p>
                        </td>
                        <td class="px-3 py-3 text-slate-700">{{ number_format($order->total_amount, 0, ',', '.') }} đ</td>
                        <td class="px-3 py-3 text-slate-700">{{ $order->order_status }}</td>
                        <td class="px-3 py-3 text-slate-700">{{ $order->payment_status }} ({{ $order->payment_method }})</td>
                        <td class="px-3 py-3">
                            <form method="POST" action="{{ route('admin.orders.update', $order) }}" class="space-y-2">
                                @csrf
                                @method('PATCH')
                                <select name="order_status" class="w-full rounded-lg border border-slate-300 px-2 py-1 text-sm">
                                    @foreach (['pending', 'confirmed', 'shipping', 'completed', 'cancelled'] as $status)
                                        <option value="{{ $status }}" @selected($order->order_status === $status)>{{ $status }}</option>
                                    @endforeach
                                </select>
                                <select name="payment_status" class="w-full rounded-lg border border-slate-300 px-2 py-1 text-sm">
                                    @foreach (['unpaid', 'paid', 'refunded'] as $status)
                                        <option value="{{ $status }}" @selected($order->payment_status === $status)>{{ $status }}</option>
                                    @endforeach
                                </select>
                                <button class="rounded-lg bg-slate-900 px-3 py-1.5 text-xs font-semibold text-white">Lưu</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" class="px-3 py-4 text-center text-slate-500">Không có đơn hàng.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>
        {{ $orders->links() }}
    </div>
@endsection
