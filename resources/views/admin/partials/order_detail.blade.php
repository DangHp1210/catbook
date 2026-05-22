@php /** Minimal order preview partial — safe and defensive */ @endphp
<div class="admin-order-detail">
    <h4>Đơn hàng: {{ $order->order_code ?? '—' }}</h4>

    <div style="margin-bottom:8px;color:#444">
        <strong>Người đặt:</strong>
        {{ $order->user->full_name ?? $order->recipient_name ?? '—' }}
        @if(!empty($order->user->email)) &middot; {{ $order->user->email }} @endif
    </div>

    <div style="margin-bottom:10px;color:#444">
        <strong>Trạng thái:</strong> {{ $order->order_status ?? '—' }}
        &nbsp;•&nbsp;
        <strong>Thanh toán:</strong> {{ $order->payment_status ?? '—' }}
    </div>

    <div style="margin-bottom:12px;">
        <table style="width:100%;border-collapse:collapse;font-size:13px">
            <thead>
                <tr style="text-align:left;border-bottom:1px solid #eee">
                    <th style="padding:6px">Sách</th>
                    <th style="padding:6px">Số lượng</th>
                    <th style="padding:6px">Đơn giá</th>
                    <th style="padding:6px">Tổng</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->items ?? [] as $item)
                    <tr>
                        <td style="padding:6px">{{ $item->book->title ?? $item->book_title_snapshot ?? '—' }}</td>
                        <td style="padding:6px">{{ $item->quantity ?? 0 }}</td>
                        <td style="padding:6px">{{ number_format((float)($item->unit_price ?? 0), 0, ',', '.') }}đ</td>
                        <td style="padding:6px">{{ number_format((float)($item->total_price ?? ($item->unit_price * $item->quantity ?? 0)), 0, ',', '.') }}đ</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    <div style="border-top:1px solid #f0f0f0;padding-top:8px;color:#333;font-weight:600">
        <div>Tạm tính: {{ number_format((float)($order->subtotal ?? 0), 0, ',', '.') }}đ</div>
        <div>Phí vận chuyển: {{ number_format((float)($order->shipping_fee ?? 0), 0, ',', '.') }}đ</div>
        <div style="margin-top:6px">Tổng: {{ number_format((float)($order->total_amount ?? 0), 0, ',', '.') }}đ</div>
    </div>

    @if(!empty($order->payments) && count($order->payments) > 0)
        <div style="margin-top:10px">
            <strong>Payments</strong>
            <ul style="margin:6px 0 0;padding-left:18px">
                @foreach($order->payments as $p)
                    <li>{{ $p->payment_method ?? '—' }} — {{ $p->status ?? '—' }} @if(!empty($p->transaction_code)) ({{ $p->transaction_code }}) @endif</li>
                @endforeach
            </ul>
        </div>
    @endif
</div>
