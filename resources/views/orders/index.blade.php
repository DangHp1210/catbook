@extends('layouts.app')

@section('title', 'Lịch sử đơn hàng')

@section('content')

<style>
/* ─── Design tokens ───────────────────────────────────── */
:root {
    --cb-bg:           #f8f6f1;
    --cb-border:       #e8e3d8;
    --cb-text:         #1a1a1a;
    --cb-muted:        #777;
    --cb-white:        #ffffff;
    --cb-accent:       #2d6a4f;
    --cb-accent-dark:  #1b4332;
    --cb-accent-light: #d8f3dc;
    --cb-serif:        'Playfair Display', Georgia, serif;
    --cb-sans:         'DM Sans', system-ui, sans-serif;
}
body {
    background: var(--cb-bg);
    color: var(--cb-text);
    margin: 0;
}
/* ─── Page header ─────────────────────────────────────── */
.oi-header {
    display: flex; align-items: flex-end; justify-content: space-between;
    gap: 16px; flex-wrap: wrap; margin-bottom: 28px;
    max-width: 1300px;
    margin: 0 auto 16px;
}
.oi-heading {
    font-family: var(--cb-serif);
    font-size: 32px; font-weight: 900; color: #0d1b10;
    letter-spacing: -.8px; line-height: 1.08; margin: 0 0 4px;
}
.oi-sub { font-family: var(--cb-sans); font-size: 13px; color: var(--cb-muted); }
.oi-cta-link {
    font-family: var(--cb-sans); font-size: 13px; font-weight: 500;
    color: var(--cb-accent); text-decoration: none;
    display: inline-flex; align-items: center; gap: 6px;
    border-bottom: 1px solid var(--cb-accent); padding-bottom: 1px;
    transition: opacity .18s; flex-shrink: 0;
}
.oi-cta-link:hover { opacity: .65; }

/* ─── Flash messages ──────────────────────────────────── */
.oi-flash {
    display: flex; align-items: flex-start; gap: 10px;
    padding: 13px 18px; border-radius: 12px; border: 1px solid;
    font-family: var(--cb-sans); font-size: 13px; margin-bottom: 20px;
}
.oi-flash svg { flex-shrink: 0; margin-top: 1px; }
.oi-flash-ok  { background: #f0fdf4; border-color: #bbf7d0; color: #166534; }
.oi-flash-err { background: #fff1f2; border-color: #fecdd3; color: #9f1239; }

/* ─── Empty state ─────────────────────────────────────── */
.oi-empty {
    background: var(--cb-white); border: 2px dashed var(--cb-border);
    border-radius: 20px; padding: 72px 32px; text-align: center;
    max-width: 1300px;
    margin: 0 auto 16px;
}
.oi-empty h2 {
    font-family: var(--cb-serif); font-size: 24px; font-weight: 700;
    color: var(--cb-text); margin: 0 0 8px;
}
.oi-empty p { font-family: var(--cb-sans); font-size: 14px; color: var(--cb-muted); margin: 0 0 24px; }
.oi-empty-btn {
    display: inline-flex; align-items: center; gap: 7px;
    font-family: var(--cb-sans); font-size: 14px; font-weight: 600;
    padding: 12px 28px; border-radius: 10px; border: none;
    background: var(--cb-text); color: #fff; text-decoration: none;
    transition: background .2s;
}
.oi-empty-btn:hover { background: var(--cb-accent); }

/* ─── Section group ───────────────────────────────────── */
.oi-group { margin-bottom: 28px; }
.oi-group:last-child { margin-bottom: 0; }

.oi-group-label {
    display: flex; align-items: center; gap: 10px;
    font-family: var(--cb-sans); font-size: 11px; font-weight: 700;
    letter-spacing: 1.6px; text-transform: uppercase;
    color: #b0a898; margin-bottom: 12px;
    max-width: 1300px;
    margin: 0 auto 16px;
}
.oi-group-label::after {
    content: ''; flex: 1; height: 1px; background: var(--cb-border);
}
.oi-group-dot {
    width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0;
}

/* ─── Table card ──────────────────────────────────────── */
.oi-table-card {
    background: var(--cb-white); border: 1px solid var(--cb-border);
    border-radius: 18px; overflow: hidden;
    max-width: 1300px;
    margin: 0 auto 16px;
}
.oi-table { width: 100%; border-collapse: collapse; font-family: var(--cb-sans); }
.oi-table thead tr { border-bottom: 1px solid var(--cb-border); }
.oi-table th {
    padding: 11px 18px; font-size: 11px; font-weight: 700;
    letter-spacing: 1.1px; text-transform: uppercase; color: #b0a898; text-align: left;
    white-space: nowrap;
}
.oi-table th:last-child { text-align: right; }
.oi-table tbody tr { border-bottom: 1px solid var(--cb-border); transition: background .15s; }
.oi-table tbody tr:last-child { border-bottom: none; }
.oi-table tbody tr:hover { background: #fdfcfa; }
.oi-table td { padding: 14px 18px; vertical-align: middle; font-size: 13px; }

/* Order code link */
.oi-code-link { text-decoration: none; }
.oi-code {
    font-family: monospace; font-size: 13px; font-weight: 700;
    color: var(--cb-text); letter-spacing: .5px;
    background: var(--cb-bg); border: 1px solid var(--cb-border);
    padding: 3px 10px; border-radius: 6px;
    display: inline-block; transition: transform .1s, border-color .15s;
}
.oi-code-link:hover .oi-code {
    border-color: var(--cb-accent); color: var(--cb-accent);
    transform: translateY(-1px);
}

/* Date */
.oi-date { color: var(--cb-muted); white-space: nowrap; }

/* Products count */
.oi-count {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: 12px; font-weight: 600;
    padding: 3px 10px; border-radius: 999px;
    background: var(--cb-bg); border: 1px solid var(--cb-border); color: var(--cb-muted);
}

/* Payment method */
.oi-pay-method {
    font-size: 12px; color: var(--cb-muted); white-space: nowrap;
}

/* Status badges */
.oi-badge {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: 11px; font-weight: 600; padding: 4px 10px;
    border-radius: 999px; border: 1px solid; white-space: nowrap;
}
.oi-badge-dot { width: 5px; height: 5px; border-radius: 50%; flex-shrink: 0; }

/* active group */
.oi-badge-pending   { background: #fffbeb; color: #d97706; border-color: #fde68a; }
.oi-badge-confirmed { background: #e0eaff; color: #1e3a8a; border-color: #bfdbfe; }
.oi-badge-shipping  { background: #e0f2fe; color: #0284c7; border-color: #bae6fd; }
/* completed */
.oi-badge-completed { background: var(--cb-accent-light); color: var(--cb-accent); border-color: #86efac; }
/* cancelled */
.oi-badge-cancelled { background: #fff1f2; color: #dc2626; border-color: #fecdd3; }
.oi-badge-refunded  { background: #f5f3ff; color: #7c3aed; border-color: #ddd6fe; }

/* Dot colors for badges */
.oi-badge-pending .oi-badge-dot   { background: #d97706; }
.oi-badge-confirmed .oi-badge-dot { background: #1e3a8a; }
.oi-badge-shipping .oi-badge-dot  { background: #0284c7; }
.oi-badge-completed .oi-badge-dot { background: var(--cb-accent); }
.oi-badge-cancelled .oi-badge-dot { background: #dc2626; }
.oi-badge-refunded .oi-badge-dot  { background: #7c3aed; }

/* Total */
.oi-total {
    font-family: var(--cb-serif); font-size: 16px; font-weight: 700;
    color: var(--cb-accent); text-align: right; white-space: nowrap;
}

.oi-page-gap {
    margin-bottom: 72px;
}
</style>

@php
    $statusLabels = [
        'pending'   => 'Đang chờ',
        'confirmed' => 'Đã xác nhận',
        'shipping'  => 'Đang giao',
        'completed' => 'Hoàn thành',
        'cancelled' => 'Đã huỷ',
        'refunded'  => 'Đã hoàn trả',
    ];
    $paymentLabels = [
        'bank_transfer' => 'Chuyển khoản',
        'momo'          => 'MoMo',
        'vnpay'         => 'VNPay',
        'cod'           => 'COD',
        'card'          => 'Thẻ tín dụng',
        'paypal'        => 'PayPal',
    ];
@endphp

<div class="oi-page-gap">

{{-- ── Page header ──────────────────────────────────────── --}}
<div class="oi-header">
    <div>
        <h1 class="oi-heading">Lịch sử đơn hàng</h1>
        <p class="oi-sub">Theo dõi và quản lý tất cả đơn hàng của bạn.</p>
    </div>
    <a href="{{ route('catalog.categories') }}" class="oi-cta-link">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M5 12h14M12 5l7 7-7 7"/>
        </svg>
        Tiếp tục mua sách
    </a>
</div>

{{-- ── Flash messages ───────────────────────────────────── --}}
@if(session('success'))
    <div class="oi-flash oi-flash-ok">
        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
            <polyline points="20 6 9 17 4 12"/>
        </svg>
        {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div class="oi-flash oi-flash-err">
        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>
        </svg>
        {{ session('error') }}
    </div>
@endif

{{-- ════════════════════════════════════════════════════════
     EMPTY STATE
════════════════════════════════════════════════════════ --}}
@if(($activeOrders->isEmpty() ?? true) && ($completedOrders->isEmpty() ?? true) && ($cancelledOrders->isEmpty() ?? true))

    <div class="oi-empty">
        <svg width="56" height="56" fill="none" stroke="var(--cb-border)" stroke-width="1.4" viewBox="0 0 24 24" style="margin:0 auto 18px">
            <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/>
            <rect x="9" y="3" width="6" height="4" rx="1"/>
            <line x1="9" y1="12" x2="15" y2="12"/><line x1="9" y1="16" x2="13" y2="16"/>
        </svg>
        <h2>Bạn chưa có đơn hàng nào</h2>
        <p>Hãy chọn vài cuốn sách và thanh toán để tạo đơn đầu tiên.</p>
        <a href="{{ route('catalog.categories') }}" class="oi-empty-btn">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M12 5v14M5 12l7-7 7 7"/>
            </svg>
            Mua sách ngay
        </a>
    </div>

@else

{{-- ════════════════════════════════════════════════════════
     ORDER TABLE MACRO (dùng inline closure để tránh lặp)
════════════════════════════════════════════════════════ --}}

@php
    /* Render helper: trả HTML badge cho từng trạng thái */
    $badgeClass = fn(string $s): string => match($s) {
        'pending'   => 'oi-badge-pending',
        'confirmed' => 'oi-badge-confirmed',
        'shipping'  => 'oi-badge-shipping',
        'completed' => 'oi-badge-completed',
        'cancelled' => 'oi-badge-cancelled',
        'refunded'  => 'oi-badge-refunded',
        default     => 'oi-badge-pending',
    };
    $dotColor = fn(string $s): string => match($s) {
        'pending'   => '#d97706',
        'confirmed' => '#1e3a8a',
        'shipping'  => '#0284c7',
        'completed' => 'var(--cb-accent)',
        'cancelled' => '#dc2626',
        'refunded'  => '#7c3aed',
        default     => '#999',
    };
@endphp

    {{-- ── 1. Đang xử lý ────────────────────────────────── --}}
    @if(!($activeOrders->isEmpty() ?? true))
        <div class="oi-group">
            <div class="oi-group-label">
                <span class="oi-group-dot" style="background:#f59e0b"></span>
                Đang xử lý
            </div>
            <div class="oi-table-card">
                <div style="overflow-x:auto">
                    <table class="oi-table" style="min-width:720px">
                        <thead>
                            <tr>
                                <th>Mã đơn</th>
                                <th>Ngày đặt</th>
                                <th>Sản phẩm</th>
                                <th>Thanh toán</th>
                                <th>Trạng thái</th>
                                <th>Tổng tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($activeOrders as $order)
                                <tr>
                                    <td>
                                        <a href="{{ route('orders.show', $order) }}" class="oi-code-link">
                                            <span class="oi-code">{{ $order->order_code }}</span>
                                        </a>
                                    </td>
                                    <td class="oi-date">{{ $order->created_at?->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <span class="oi-count">
                                            <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/>
                                                <line x1="3" y1="6" x2="21" y2="6"/>
                                            </svg>
                                            {{ $order->items_count }} sản phẩm
                                        </span>
                                    </td>
                                    <td class="oi-pay-method">{{ $paymentLabels[$order->payment_method] ?? $order->payment_method }}</td>
                                    <td>
                                            <span class="oi-badge {{ $badgeClass($order->order_status) }}">
                                            <span class="oi-badge-dot"></span>
                                            {{ $statusLabels[$order->order_status] ?? $order->order_status }}
                                        </span>
                                    </td>
                                    <td class="oi-total">{{ number_format((float)$order->total_amount, 0, ',', '.') }}đ</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    {{-- ── 2. Đã hoàn thành ─────────────────────────────── --}}
    @if(!($completedOrders->isEmpty() ?? true))
        <div class="oi-group">
            <div class="oi-group-label">
                <span class="oi-group-dot" style="background:var(--cb-accent)"></span>
                Đã hoàn thành
            </div>
            <div class="oi-table-card">
                <div style="overflow-x:auto">
                    <table class="oi-table" style="min-width:720px">
                        <thead>
                            <tr>
                                <th>Mã đơn</th>
                                <th>Ngày đặt</th>
                                <th>Sản phẩm</th>
                                <th>Thanh toán</th>
                                <th>Trạng thái</th>
                                <th>Tổng tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($completedOrders as $order)
                                <tr>
                                    <td>
                                        <a href="{{ route('orders.show', $order) }}" class="oi-code-link">
                                            <span class="oi-code">{{ $order->order_code }}</span>
                                        </a>
                                    </td>
                                    <td class="oi-date">{{ $order->created_at?->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <span class="oi-count">
                                            <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/>
                                                <line x1="3" y1="6" x2="21" y2="6"/>
                                            </svg>
                                            {{ $order->items_count }} sản phẩm
                                        </span>
                                    </td>
                                    <td class="oi-pay-method">{{ $paymentLabels[$order->payment_method] ?? $order->payment_method }}</td>
                                    <td>
                                            <span class="oi-badge {{ $badgeClass($order->order_status) }}">
                                            <span class="oi-badge-dot"></span>
                                            {{ $statusLabels[$order->order_status] ?? $order->order_status }}
                                        </span>
                                    </td>
                                    <td class="oi-total">{{ number_format((float)$order->total_amount, 0, ',', '.') }}đ</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

    {{-- ── 3. Đã huỷ ────────────────────────────────────── --}}
    @if(!($cancelledOrders->isEmpty() ?? true))
        <div class="oi-group">
            <div class="oi-group-label">
                <span class="oi-group-dot" style="background:#dc2626"></span>
                Đã huỷ
            </div>
            <div class="oi-table-card">
                <div style="overflow-x:auto">
                    <table class="oi-table" style="min-width:720px">
                        <thead>
                            <tr>
                                <th>Mã đơn</th>
                                <th>Ngày đặt</th>
                                <th>Sản phẩm</th>
                                <th>Thanh toán</th>
                                <th>Trạng thái</th>
                                <th>Tổng tiền</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($cancelledOrders as $order)
                                <tr>
                                    <td>
                                        <a href="{{ route('orders.show', $order) }}" class="oi-code-link">
                                            <span class="oi-code">{{ $order->order_code }}</span>
                                        </a>
                                    </td>
                                    <td class="oi-date">{{ $order->created_at?->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <span class="oi-count">
                                            <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                                <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/>
                                                <line x1="3" y1="6" x2="21" y2="6"/>
                                            </svg>
                                            {{ $order->items_count }} sản phẩm
                                        </span>
                                    </td>
                                    <td class="oi-pay-method">{{ $paymentLabels[$order->payment_method] ?? $order->payment_method }}</td>
                                    <td>
                                            <span class="oi-badge {{ $badgeClass($order->order_status) }}">
                                            <span class="oi-badge-dot"></span>
                                            {{ $statusLabels[$order->order_status] ?? $order->order_status }}
                                        </span>
                                    </td>
                                    <td class="oi-total" style="color:#dc2626">{{ number_format((float)$order->total_amount, 0, ',', '.') }}đ</td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    @endif

@endif {{-- end else --}}

</div>

@endsection