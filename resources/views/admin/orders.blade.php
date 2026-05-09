@extends('layouts.admin', ['title' => 'Quản lý đơn hàng'])

@section('content')

@php
    $orderStatusLabels = [
        'pending'   => 'Chờ xử lý',
        'confirmed' => 'Đã xác nhận',
        'shipping'  => 'Đang giao',
        'completed' => 'Hoàn tất',
        'cancelled' => 'Đã huỷ',
    ];
    $paymentStatusLabels = [
        'unpaid'   => 'Chưa thanh toán',
        'paid'     => 'Đã thanh toán',
        'refunded' => 'Đã hoàn tiền',
    ];
    $paymentMethodLabels = [
        'cod'           => 'COD',
        'bank_transfer' => 'Chuyển khoản',
        'momo'          => 'MoMo',
        'vnpay'         => 'VNPay',
    ];
@endphp

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
    font-family: var(--cb-sans, 'DM Sans', system-ui, sans-serif);
    background: var(--cb-bg);
    color: var(--cb-text);
    margin: 0;
}

/* ─── Page header ─────────────────────────────────────── */
.or-header {
    background: var(--cb-white); border: 1px solid var(--cb-border);
    border-radius: 18px; padding: 20px 26px;
    display: flex; align-items: flex-end; justify-content: space-between;
    gap: 20px; flex-wrap: wrap; margin-bottom: 16px;
    position: relative; overflow: hidden;
    max-width: 1300px;
    margin: 0 auto 16px;
}
.or-header::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
    background: linear-gradient(90deg, #f59e0b, #ef4444);
}
.or-header-title {
    font-family: var(--cb-serif); font-size: 22px; font-weight: 900;
    color: #0d1b10; letter-spacing: -.5px; margin: 0 0 3px;
}
.or-header-sub { font-family: var(--cb-sans); font-size: 13px; color: var(--cb-muted); }

/* Search */
.or-search-wrap {
    display: flex; border: 1.5px solid var(--cb-border); border-radius: 10px;
    overflow: hidden; background: var(--cb-white); transition: border-color .2s;
    min-width: 280px;
}
.or-search-wrap:focus-within { border-color: var(--cb-accent); }
.or-search-icon { padding: 0 10px 0 12px; display: flex; align-items: center; color: var(--cb-muted); }
.or-search-input {
    font-family: var(--cb-sans); font-size: 13px; border: none; outline: none;
    background: transparent; color: var(--cb-text); padding: 10px 14px 10px 0; flex: 1;
}
.or-search-input::placeholder { color: #c0b8b0; }

/* ─── Table card ──────────────────────────────────────── */
.or-table-card {
    background: var(--cb-white); border: 1px solid var(--cb-border);
    border-radius: 18px; overflow: hidden; margin-bottom: 16px;
    max-width: 1300px;
    margin: 0 auto 16px;
}
.or-table { width: 100%; border-collapse: collapse; font-family: var(--cb-sans); }
.or-table thead tr { border-bottom: 1px solid var(--cb-border); }
.or-table th {
    padding: 11px 18px; font-size: 11px; font-weight: 700;
    letter-spacing: 1.2px; text-transform: uppercase; color: #b0a898; text-align: left;
    white-space: nowrap;
}
.or-table tbody tr { border-bottom: 1px solid var(--cb-border); transition: background .15s; }
.or-table tbody tr:last-child { border-bottom: none; }
.or-table tbody tr:hover { background: #fdfcfa; }
.or-table td { padding: 14px 18px; vertical-align: top; }

/* ─── Order code ──────────────────────────────────────── */
.or-code {
    font-family: monospace; font-size: 13px; font-weight: 700;
    color: var(--cb-text); letter-spacing: .5px;
    background: var(--cb-bg); border: 1px solid var(--cb-border);
    padding: 3px 10px; border-radius: 6px; white-space: nowrap;
    display: inline-block;
}
.or-date { font-size: 11px; color: var(--cb-muted); margin-top: 5px; }

.or-code-link { text-decoration: none; }
.or-code-link .or-code { transition: transform .08s; }
.or-code-link:hover .or-code { transform: translateY(-1px); }

/* ─── Customer info ───────────────────────────────────── */
.or-user-name    { font-size: 13px; font-weight: 600; color: var(--cb-text); margin-bottom: 3px; }
.or-recipient    { font-size: 12px; color: var(--cb-muted); margin-bottom: 2px; }
.or-items-count  {
    display: inline-flex; align-items: center; gap: 4px;
    font-size: 11px; font-weight: 600; padding: 2px 8px; border-radius: 999px;
    background: var(--cb-bg); border: 1px solid var(--cb-border); color: var(--cb-muted);
    margin-top: 4px;
}
.or-preview-card {
    background: var(--cb-bg); /* Nền xám kem đồng bộ hệ thống */
    border: 1px solid var(--cb-border);
    border-radius: 16px;
    padding: 24px;
    margin: 12px 0;
    box-shadow: inset 0 2px 10px rgba(0,0,0,0.02);
}

.or-preview-header {
    display: flex; justify-content: space-between; align-items: flex-start;
    border-bottom: 1px solid var(--cb-border);
    padding-bottom: 16px; margin-bottom: 20px;
}
.or-preview-title {
    font-family: var(--cb-serif); font-size: 18px; font-weight: 700;
    color: var(--cb-text); margin: 0 0 6px 0;
    display: flex; align-items: center; gap: 10px;
}
.or-code-pill {
    font-family: var(--cb-sans); font-size: 13px; font-weight: 600;
    background: var(--cb-white); border: 1.5px dashed #d0c8be;
    padding: 2px 10px; border-radius: 6px; color: var(--cb-text);
}
.or-preview-date { font-size: 13px; color: var(--cb-muted); }

/* Bố cục chia 2 cột */
.or-preview-body {
    display: grid; grid-template-columns: 1fr 280px; gap: 20px; margin-bottom: 24px;
}
@media (max-width: 768px) { .or-preview-body { grid-template-columns: 1fr; } }

/* Hộp thông tin (Khách hàng, Thanh toán) */
.or-info-box {
    background: var(--cb-white); border: 1px solid var(--cb-border);
    border-radius: 12px; padding: 18px;
}
.or-info-title {
    font-size: 11px; font-weight: 700; text-transform: uppercase;
    letter-spacing: 1px; color: #b0a898; margin-bottom: 14px;
    display: flex; align-items: center; gap: 6px;
}
.or-info-row {
    font-size: 14px; margin-bottom: 10px; line-height: 1.5;
    display: flex; gap: 12px;
}
.or-info-row:last-child { margin-bottom: 0; }
.or-info-row .lbl { color: var(--cb-muted); width: 85px; flex-shrink: 0; }
.or-info-row .val { color: var(--cb-text); font-weight: 500; }

/* Hộp tổng tiền nổi bật */
.or-summary-box {
    background: var(--cb-accent-light); border: 1px solid rgba(45,106,79,0.15);
}
.or-sum-row {
    display: flex; justify-content: space-between; font-size: 14px;
    color: var(--cb-accent-dark); margin-bottom: 10px;
}
.or-sum-total {
    display: flex; justify-content: space-between; align-items: center;
    border-top: 1px dashed rgba(45,106,79,0.3);
    margin-top: 12px; padding-top: 14px;
    font-weight: 900; font-size: 20px; color: var(--cb-accent);
    font-family: var(--cb-serif, Georgia, serif);
}

/* Bảng sản phẩm */
.or-items-table {
    width: 100%; border-collapse: collapse;
    background: var(--cb-white); border: 1px solid var(--cb-border);
    border-radius: 12px; overflow: hidden;
}
.or-items-table th {
    background: #fdfcf9; font-size: 12px; font-weight: 600;
    color: var(--cb-muted); text-align: left; padding: 12px 16px;
    border-bottom: 1px solid var(--cb-border); text-transform: uppercase;
}
.or-items-table td {
    padding: 14px 16px; font-size: 14px; color: var(--cb-text);
    border-bottom: 1px solid var(--cb-border);
}
.or-items-table tr:last-child td { border-bottom: none; }
.or-book-title { font-weight: 600; margin-bottom: 4px; color: var(--cb-accent-dark); }
.or-book-author { font-size: 12px; color: var(--cb-muted); }
/* ─── Total amount ────────────────────────────────────── */
.or-total {
    font-family: var(--cb-serif); font-size: 16px; font-weight: 700;
    color: var(--cb-accent); white-space: nowrap;
}

/* ─── Order status badges ─────────────────────────────── */
.or-order-badge {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: 11px; font-weight: 600; padding: 4px 10px;
    border-radius: 999px; border: 1px solid; white-space: nowrap;
}
.or-order-badge-dot { width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0; }

.or-ob-pending   { background: #fffbeb; color: #d97706; border-color: #fde68a; }
.or-ob-confirmed { background: #e0eaff; color: #1e3a8a; border-color: #bfdbfe; }
.or-ob-shipping  { background: #e0f2fe; color: #0284c7; border-color: #bae6fd; }
.or-ob-completed { background: var(--cb-accent-light); color: var(--cb-accent); border-color: #86efac; }
.or-ob-cancelled { background: #fff1f2; color: #dc2626; border-color: #fecdd3; }

/* Dot colors for order badges (avoid inline style with Blade expressions) */
.or-ob-pending .or-order-badge-dot   { background: #d97706; }
.or-ob-confirmed .or-order-badge-dot { background: #1e3a8a; }
.or-ob-shipping .or-order-badge-dot  { background: #0284c7; }
.or-ob-completed .or-order-badge-dot { background: var(--cb-accent); }
.or-ob-cancelled .or-order-badge-dot { background: #dc2626; }

/* ─── Payment status badges ───────────────────────────── */
.or-pay-badge {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: 11px; font-weight: 600; padding: 3px 10px;
    border-radius: 999px; white-space: nowrap; margin-bottom: 4px;
}
.or-pb-unpaid   { background: #fff7ed; color: #c2410c; }
.or-pb-paid     { background: var(--cb-accent-light); color: var(--cb-accent); }
.or-pb-refunded { background: #f5f3ff; color: #7c3aed; }

.or-method-tag {
    display: inline-flex; align-items: center;
    font-size: 10px; font-weight: 700; padding: 2px 8px;
    border-radius: 999px; background: var(--cb-bg);
    border: 1px solid var(--cb-border); color: var(--cb-muted);
    letter-spacing: .4px;
}

/* ─── Inline edit form ────────────────────────────────── */
.or-edit-form { display: flex; flex-direction: column; gap: 8px; min-width: 170px; }

.or-select {
    font-family: var(--cb-sans); font-size: 12px;
    padding: 7px 10px; border: 1.5px solid var(--cb-border);
    border-radius: 8px; background: var(--cb-white); color: var(--cb-text);
    outline: none; cursor: pointer; width: 100%;
    transition: border-color .18s; appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='none' viewBox='0 0 24 24'%3E%3Cpolyline points='6 9 12 15 18 9' stroke='%23999' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
    background-repeat: no-repeat; background-position: right 8px center;
    padding-right: 28px;
}
.or-select:focus { border-color: var(--cb-accent); }

.or-save-btn {
    font-family: var(--cb-sans); font-size: 12px; font-weight: 600;
    padding: 8px 14px; border-radius: 8px; border: none;
    background: var(--cb-text); color: #fff; cursor: pointer;
    display: inline-flex; align-items: center; justify-content: center; gap: 5px;
    transition: background .2s; width: 100%;
}
.or-save-btn:hover { background: var(--cb-accent); }

/* ─── Empty state ─────────────────────────────────────── */
.or-empty { padding: 56px 32px; text-align: center; }
.or-empty h3 {
    font-family: var(--cb-serif); font-size: 20px; font-weight: 700;
    color: var(--cb-text); margin-bottom: 6px;
}
.or-empty p { font-family: var(--cb-sans); font-size: 13px; color: var(--cb-muted); }
</style>

{{-- ── Page header ──────────────────────────────────────── --}}
<div class="or-header">
    <div>
        <h1 class="or-header-title">Quản lý đơn hàng</h1>
        <p class="or-header-sub">Theo dõi tiến độ xử lý và cập nhật trạng thái đơn hàng.</p>
    </div>
    <form method="GET">
        <div class="or-search-wrap">
            <span class="or-search-icon">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
            </span>
            <input name="q" value="{{ $q }}"
                   placeholder="Tìm mã đơn, người nhận, SĐT..."
                   class="or-search-input">
        </div>
    </form>
</div>

{{-- ── Orders table ──────────────────────────────────────── --}}
<div class="or-table-card">
    <div style="overflow-x:auto">
        <table class="or-table" style="min-width:900px">
            <thead>
                <tr>
                    <th>Mã đơn</th>
                    <th>Khách hàng</th>
                    <th>Tổng tiền</th>
                    <th>Trạng thái đơn</th>
                    <th>Thanh toán</th>
                    <th>Cập nhật</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                    @php
                        $oStatus = $order->order_status;
                        $pStatus = $order->payment_status;
                        $oClass  = 'or-ob-'.($oStatus ?: 'pending');
                        $pClass  = 'or-pb-'.($pStatus ?: 'unpaid');
                        $oDotColor = match($oStatus) {
                            'pending'   => '#d97706',
                            'confirmed' => '#1e3a8a',
                            'shipping'  => '#0284c7',
                            'completed' => 'var(--cb-accent)',
                            'cancelled' => '#dc2626',
                            default     => '#999',
                        };
                    @endphp
                    <tr>
                        {{-- Order code --}}
                        <td>
                            <a href="#" class="or-code-link" data-order-id="{{ $order->id }}" data-preview-url="{{ route('admin.orders.preview', $order) }}">
                                <span class="or-code">{{ $order->order_code }}</span>
                            </a>
                            @if($order->created_at)
                                <p class="or-date">{{ $order->created_at->format('d/m/Y H:i') }}</p>
                            @endif
                        </td>

                        {{-- Customer --}}
                        <td>
                            <p class="or-user-name">{{ $order->user?->full_name ?? 'N/A' }}</p>
                            <p class="or-recipient">
                                {{ $order->recipient_name }}
                                &nbsp;·&nbsp;
                                {{ $order->recipient_phone }}
                            </p>
                            <span class="or-items-count">
                                <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/>
                                    <line x1="3" y1="6" x2="21" y2="6"/>
                                </svg>
                                {{ $order->items_count }} sản phẩm
                            </span>
                        </td>

                        {{-- Total --}}
                        <td>
                            <p class="or-total">{{ number_format($order->total_amount, 0, ',', '.') }}đ</p>
                        </td>

                        {{-- Order status --}}
                        <td>
                            <span class="or-order-badge {{ $oClass }}">
                                <span class="or-order-badge-dot"></span>
                                {{ $orderStatusLabels[$oStatus] ?? $oStatus }}
                            </span>
                        </td>

                        {{-- Payment --}}
                        <td>
                            <div style="display:flex;flex-direction:column;gap:5px;align-items:flex-start">
                                <span class="or-pay-badge {{ $pClass }}">
                                    {{ $paymentStatusLabels[$pStatus] ?? $pStatus }}
                                </span>
                                <span class="or-method-tag">
                                    {{ $paymentMethodLabels[$order->payment_method] ?? $order->payment_method }}
                                </span>
                            </div>
                        </td>

                        {{-- Inline edit --}}
                        <td>
                            <form method="POST"
                                  action="{{ route('admin.orders.update', $order) }}"
                                  class="or-edit-form">
                                @csrf
                                @method('PATCH')

                                <select name="order_status" class="or-select">
                                    @foreach(['pending','confirmed','shipping','completed','cancelled'] as $status)
                                        <option value="{{ $status }}" @selected($order->order_status === $status)>
                                            {{ $orderStatusLabels[$status] ?? $status }}
                                        </option>
                                    @endforeach
                                </select>

                                <select name="payment_status" class="or-select">
                                    @foreach(['unpaid','paid','refunded'] as $status)
                                        <option value="{{ $status }}" @selected($order->payment_status === $status)>
                                            {{ $paymentStatusLabels[$status] ?? $status }}
                                        </option>
                                    @endforeach
                                </select>

                                <button type="submit" class="or-save-btn">
                                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                        <polyline points="20 6 9 17 4 12"/>
                                    </svg>
                                    Lưu thay đổi
                                </button>
                            </form>
                        </td>
                    </tr>
                    {{-- Hidden inline preview template for this order (merged from partial) --}}
                        <tr style="display:none">
                            <td colspan="6" style="padding: 0;">
                                <div class="order-detail-template" data-order-id="{{ $order->id }}" style="display:none; padding: 0 16px;">
                                    
                                    <div class="or-preview-card">
                                        
                                        {{-- 1. Header (Mã đơn & Trạng thái) --}}
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

                                        {{-- 2. Thông tin Khách hàng & Thanh toán --}}
                                        <div class="or-preview-body">
                                            
                                            {{-- Cột trái: Thông tin người nhận --}}
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

                                            {{-- Cột phải: Tóm tắt chi phí --}}
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

                                        {{-- 3. Danh sách Sản phẩm --}}
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
                                    
                                </div>
                            </td>
                        </tr>
                @empty
                    <tr>
                        <td colspan="6">
                            <div class="or-empty">
                                <svg width="48" height="48" fill="none" stroke="var(--cb-border)" stroke-width="1.4" viewBox="0 0 24 24" style="margin:0 auto 14px">
                                    <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/>
                                    <rect x="9" y="3" width="6" height="4" rx="1"/>
                                    <line x1="9" y1="12" x2="15" y2="12"/>
                                    <line x1="9" y1="16" x2="13" y2="16"/>
                                </svg>
                                <h3>Không có đơn hàng</h3>
                                <p>Thử thay đổi từ khoá tìm kiếm.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Pagination --}}
<div style="display:flex;justify-content:center">
    {{ $orders->links() }}
</div>

{{-- Order preview modal (AJAX) --}}
<div id="orderPreviewModal" class="order-preview-modal" style="display:none;position:fixed;inset:0;z-index:80;align-items:center;justify-content:center;padding:28px;background:rgba(10,10,10,0.45)">
    <div style="max-width:900px;width:100%;max-height:90vh;overflow:auto;background:var(--cb-white);border-radius:14px;padding:18px;position:relative;border:1px solid var(--cb-border)">
        <button id="orderPreviewClose" style="position:absolute;right:12px;top:12px;border:none;background:transparent;font-size:18px;cursor:pointer">✕</button>
        <div id="orderPreviewContent">Loading…</div>
    </div>
</div>

<script>
document.addEventListener('click', function(e){
    const link = e.target.closest('.or-code-link');
    if (!link) return;
    e.preventDefault();
    const orderId = link.dataset.orderId;
    const url = link.dataset.previewUrl;
    const modal = document.getElementById('orderPreviewModal');
    const content = document.getElementById('orderPreviewContent');
    if (!modal || !content) return;
    content.innerHTML = 'Đang tải...';
    modal.style.display = 'flex';

    // Try using inline pre-rendered template first
    if (orderId) {
        const tpl = document.querySelector('.order-detail-template[data-order-id="' + orderId + '"]');
        if (tpl) {
            content.innerHTML = tpl.innerHTML;
            return;
        }
    }

    // Fallback to AJAX preview route
    if (!url) {
        content.innerHTML = '<p style="color:#c00">Không có đường dẫn xem trước.</p>';
        return;
    }
    fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(resp => {
            if (!resp.ok) throw new Error('Network error');
            return resp.text();
        })
        .then(html => { content.innerHTML = html; })
        .catch(err => { content.innerHTML = '<p style="color:#c00">Không tải được chi tiết đơn hàng.</p>'; console.error(err); });
});
document.getElementById('orderPreviewClose')?.addEventListener('click', function(){
    document.getElementById('orderPreviewModal').style.display = 'none';
});
document.getElementById('orderPreviewModal')?.addEventListener('click', function(e){ if (e.target === this) this.style.display = 'none'; });
</script>

@endsection