@php
    $orderStatusLabels = [
        'pending'   => 'Chờ xử lý',
        'confirmed' => 'Đã xác nhận',
        'shipping'  => 'Đang giao',
        'completed'  => 'Hoàn tất',
        'cancelled'  => 'Đã huỷ',
    ];
    $paymentStatusLabels = [
        'unpaid'   => 'Chưa thanh toán',
        'paid'     => 'Đã thanh toán',
        'refunded' => 'Đã hoàn tiền',
    ];
@endphp

<div class="or-preview-card">
    <div class="or-preview-header">
        <div>
            <h3 class="or-preview-title">
                Chi tiết đơn hàng
                <span class="or-code-pill">#{{ $order->order_code }}</span>
            </h3>
            @if($order->created_at)
                <div class="or-preview-date">Tạo lúc: {{ $order->created_at->format('H:i — d/m/Y') }}</div>
            @endif
        </div>
        <div style="display:flex; gap:8px; align-items:center;">
            <span class="or-order-badge or-ob-{{ $order->order_status }}">
                <span class="or-order-badge-dot"></span>
                {{ $orderStatusLabels[$order->order_status] ?? $order->order_status }}
            </span>
            <span class="or-pay-badge or-pb-{{ $order->payment_status }}">
                {{ $paymentStatusLabels[$order->payment_status] ?? $order->payment_status }}
            </span>
        </div>
    </div>

    <div class="or-preview-body">
        <div class="or-info-box">
            <div class="or-info-title">👤 Thông tin giao hàng</div>
            <div class="or-info-row">
                <span class="lbl">Người đặt:</span>
                <span class="val">{{ $order->user?->full_name ?? 'Khách vãng lai' }}</span>
            </div>
            <div class="or-info-row">
                <span class="lbl">Người nhận:</span>
                <span class="val">{{ $order->recipient_name }} ({{ $order->recipient_phone }})</span>
            </div>
            <div class="or-info-row">
                <span class="lbl">Địa chỉ:</span>
                <span class="val">{{ $order->shipping_address }}</span>
            </div>
            @if(!empty($order->note))
                <div class="or-info-row">
                    <span class="lbl">Ghi chú:</span>
                    <span class="val" style="color:#d97706; font-style:italic;">{{ $order->note }}</span>
                </div>
            @endif
        </div>

        <div class="or-info-box or-summary-box">
            <div class="or-info-title" style="color:var(--cb-accent-dark)">💵 Tổng quan thanh toán</div>

            <div class="or-sum-row">
                <span>Phí vận chuyển</span>
                <strong>{{ number_format($order->shipping_fee ?? 0, 0, ',', '.') }}đ</strong>
            </div>

            <div class="or-sum-total">
                <span>Tổng cộng</span>
                <span>{{ number_format($order->total_amount, 0, ',', '.') }}đ</span>
            </div>
        </div>
    </div>

    <div class="or-info-title">📦 Danh sách sản phẩm</div>
    <table class="or-items-table">
        <thead>
            <tr>
                <th>Sách</th>
                <th style="width:90px; text-align:center;">Số lượng</th>
                <th style="width:130px; text-align:right;">Đơn giá</th>
                <th style="width:140px; text-align:right;">Thành tiền</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->items as $item)
                <tr>
                    <td>
                        <div class="or-book-title">{{ $item->book?->title ?? '—' }}</div>
                        @if($item->book && $item->book->authors->isNotEmpty())
                            <div class="or-book-author">Tác giả: {{ $item->book->authors->pluck('name')->join(', ') }}</div>
                        @endif
                    </td>
                    <td style="text-align:center; font-weight:600;">{{ $item->quantity }}</td>
                    <td style="text-align:right;">{{ number_format($item->unit_price, 0, ',', '.') }}đ</td>
                    <td style="text-align:right; font-weight:700; color:var(--cb-text);">
                        {{ number_format($item->quantity * $item->unit_price, 0, ',', '.') }}đ
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
