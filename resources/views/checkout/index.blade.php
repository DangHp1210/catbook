@extends('layouts.app')

@section('title', 'Thanh toán — CatBook')

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
        font-family: var(--cb-sans, 'DM Sans', system-ui, sans-serif);
        background: var(--cb-bg);
        color: var(--cb-text);
        margin: 0;
}
/* ─── Page header ─────────────────────────────────────── */
.ck-header { margin-bottom: 28px; }
.ck-eyebrow {
    display: inline-flex; align-items: center; gap: 7px;
    font-family: var(--cb-sans); font-size: 11px; font-weight: 600;
    letter-spacing: 1.8px; text-transform: uppercase;
    color: var(--cb-accent); background: var(--cb-accent-light);
    padding: 4px 13px; border-radius: 999px; margin-bottom: 10px;
}
.ck-heading {
    font-family: var(--cb-serif);
    font-size: 36px; font-weight: 900; color: #0d1b10;
    letter-spacing: -1px; line-height: 1.08;
}

/* ─── Error flash ─────────────────────────────────────── */
.ck-error-flash {
    display: flex; align-items: flex-start; gap: 10px;
    padding: 13px 18px; border-radius: 12px;
    border: 1px solid #fecdd3; background: #fff1f2;
    font-family: var(--cb-sans); font-size: 13px; color: #9f1239;
    margin-bottom: 22px;
}
.ck-error-flash svg { flex-shrink: 0; margin-top: 1px; }

/* ─── 2-col layout ────────────────────────────────────── */
.ck-layout {
    display: grid;
    grid-template-columns: 1fr 340px;
    gap: 24px; align-items: start;
}
@media (max-width: 960px) { .ck-layout { grid-template-columns: 1fr; } }

/* ─── Shared card ─────────────────────────────────────── */
.ck-card {
    background: var(--cb-white); border: 1px solid var(--cb-border);
    border-radius: 18px; overflow: hidden;
}
.ck-card-head {
    padding: 18px 24px 14px;
    border-bottom: 1px solid var(--cb-border);
}
.ck-card-title {
    font-family: var(--cb-serif);
    font-size: 18px; font-weight: 700; color: var(--cb-text);
}
.ck-card-sub {
    font-family: var(--cb-sans);
    font-size: 12px; color: var(--cb-muted); margin-top: 3px;
}
.ck-card-body { padding: 22px 24px; }

/* ─── Form elements ───────────────────────────────────── */
.ck-grid-2 { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
@media (max-width: 560px) { .ck-grid-2 { grid-template-columns: 1fr; } }

.ck-field { display: flex; flex-direction: column; }
.ck-label {
    font-family: var(--cb-sans); font-size: 12px; font-weight: 600;
    color: var(--cb-text); margin-bottom: 7px; letter-spacing: .1px;
}
.ck-input {
    font-family: var(--cb-sans); font-size: 14px;
    padding: 11px 16px;
    border: 1.5px solid var(--cb-border); border-radius: 10px;
    background: var(--cb-white); color: var(--cb-text);
    outline: none; transition: border-color .2s, box-shadow .2s;
    box-sizing: border-box; width: 100%;
}
.ck-input:focus {
    border-color: var(--cb-accent);
    box-shadow: 0 0 0 3px rgba(45,106,79,.09);
}
.ck-input::placeholder { color: #c0b8b0; }

.ck-textarea {
    font-family: var(--cb-sans); font-size: 14px;
    padding: 11px 16px; resize: vertical;
    border: 1.5px solid var(--cb-border); border-radius: 10px;
    background: var(--cb-white); color: var(--cb-text);
    outline: none; transition: border-color .2s, box-shadow .2s;
    box-sizing: border-box; width: 100%;
}
.ck-textarea:focus {
    border-color: var(--cb-accent);
    box-shadow: 0 0 0 3px rgba(45,106,79,.09);
}
.ck-textarea::placeholder { color: #c0b8b0; }

/* ─── Section divider ─────────────────────────────────── */
.ck-hr { height: 1px; background: var(--cb-border); margin: 6px 0 22px; }

/* ─── Payment methods ─────────────────────────────────── */
.ck-pay-section-title {
    font-family: var(--cb-sans); font-size: 12px; font-weight: 600;
    letter-spacing: 1.3px; text-transform: uppercase;
    color: #b0a898; margin-bottom: 12px;
}

/* Online method cards grid */
.ck-pay-grid { display: grid; grid-template-columns: repeat(3,1fr); gap: 10px; margin-bottom: 10px; }
@media (max-width: 640px) { .ck-pay-grid { grid-template-columns: 1fr; } }

.ck-pay-card {
    position: relative; cursor: pointer;
    border: 1.5px solid var(--cb-border);
    border-radius: 14px; padding: 16px;
    background: var(--cb-white);
    transition: border-color .2s, box-shadow .2s;
    display: flex; flex-direction: column; gap: 10px;
}
.ck-pay-card:hover { border-color: var(--cb-accent); }
.ck-pay-card.selected {
    border-color: var(--cb-accent);
    box-shadow: 0 0 0 3px rgba(45,106,79,.1);
    background: #f4fdf7;
}

/* Hide real radio */
.ck-pay-card input[type="radio"] { position: absolute; opacity: 0; pointer-events: none; }

/* Check circle */
.ck-pay-check {
    position: absolute; top: 12px; right: 12px;
    width: 20px; height: 20px; border-radius: 50%;
    border: 1.5px solid var(--cb-border);
    background: var(--cb-white);
    display: flex; align-items: center; justify-content: center;
    transition: all .18s;
}
.ck-pay-card.selected .ck-pay-check {
    background: var(--cb-accent); border-color: var(--cb-accent);
}
.ck-pay-check svg { display: none; }
.ck-pay-card.selected .ck-pay-check svg { display: block; }

/* Icon */
.ck-pay-icon {
    width: 40px; height: 40px; border-radius: 10px;
    display: flex; align-items: center; justify-content: center;
    font-family: var(--cb-serif); font-size: 16px; font-weight: 900; color: #fff;
}
.ck-pay-icon-bk { background: linear-gradient(135deg, #0ea5e9, #2563eb); }
.ck-pay-icon-mm { background: linear-gradient(135deg, #ec4899, #be185d); }
.ck-pay-icon-vn { background: linear-gradient(135deg, #f97316, #dc2626); }
.ck-pay-icon-cod { background: linear-gradient(135deg, #6b7280, #374151); }

.ck-pay-name {
    font-family: var(--cb-sans); font-size: 13px; font-weight: 600; color: var(--cb-text);
}
.ck-pay-desc {
    font-family: var(--cb-sans); font-size: 11px; color: var(--cb-muted); line-height: 1.5;
}

.ck-pay-tag {
    display: inline-flex; align-items: center;
    font-family: var(--cb-sans); font-size: 10px; font-weight: 700;
    padding: 2px 8px; border-radius: 999px; letter-spacing: .4px;
    align-self: flex-start;
}
.ck-tag-blue   { background: #dbeafe; color: #1d4ed8; }
.ck-tag-pink   { background: #fce7f3; color: #9d174d; }
.ck-tag-orange { background: #ffedd5; color: #c2410c; }

/* COD row */
.ck-cod-row {
    display: flex; align-items: flex-start; gap: 12px;
    padding: 14px 16px;
    border: 1.5px solid var(--cb-border);
    border-radius: 12px; cursor: pointer;
    background: var(--cb-white);
    transition: border-color .2s, box-shadow .2s;
}
.ck-cod-row.selected {
    border-color: var(--cb-accent);
    box-shadow: 0 0 0 3px rgba(45,106,79,.1);
    background: #f4fdf7;
}
.ck-cod-row input[type="radio"] { accent-color: var(--cb-accent); margin-top: 2px; flex-shrink: 0; }
.ck-cod-name {
    font-family: var(--cb-sans); font-size: 13px; font-weight: 600; color: var(--cb-text);
    margin-bottom: 3px;
}
.ck-cod-desc { font-family: var(--cb-sans); font-size: 12px; color: var(--cb-muted); line-height: 1.5; }

/* QR block */
.ck-qr-wrap {
    margin-top: 12px; padding: 14px; border-radius: 10px;
    background: var(--cb-bg); border: 1px solid var(--cb-border);
}
.ck-qr-wrap img { width: 100%; max-width: 180px; border-radius: 8px; display: block; }
.ck-qr-note {
    font-family: var(--cb-sans); font-size: 12px; color: var(--cb-muted); margin-top: 8px;
}

/* Payment tips */
.ck-tips {
    margin-top: 14px; padding: 14px 16px;
    background: var(--cb-bg); border: 1px solid var(--cb-border);
    border-radius: 12px;
}
.ck-tips-title {
    font-family: var(--cb-sans); font-size: 12px; font-weight: 600;
    color: var(--cb-text); margin-bottom: 8px;
}
.ck-tips ul {
    margin: 0; padding: 0; list-style: none;
    display: flex; flex-direction: column; gap: 5px;
}
.ck-tips li {
    font-family: var(--cb-sans); font-size: 12px; color: var(--cb-muted);
    padding-left: 14px; position: relative; line-height: 1.55;
}
.ck-tips li::before {
    content: '→'; position: absolute; left: 0;
    color: var(--cb-accent); font-size: 11px;
}

/* ─── Action buttons ──────────────────────────────────── */
.ck-actions { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 24px; }
.ck-btn-submit {
    font-family: var(--cb-sans); font-size: 15px; font-weight: 600;
    padding: 13px 32px; border-radius: 12px; border: none;
    background: var(--cb-accent); color: #fff; cursor: pointer;
    display: flex; align-items: center; gap: 8px;
    transition: background .2s, transform .15s;
}
.ck-btn-submit:hover { background: var(--cb-accent-dark); transform: translateY(-1px); }
.ck-btn-back {
    font-family: var(--cb-sans); font-size: 14px; font-weight: 500;
    padding: 13px 22px; border-radius: 12px;
    border: 1.5px solid var(--cb-border); background: transparent;
    color: var(--cb-muted); text-decoration: none; cursor: pointer;
    display: inline-flex; align-items: center; gap: 7px;
    transition: border-color .2s, color .2s;
}
.ck-btn-back:hover { border-color: var(--cb-text); color: var(--cb-text); }

/* ─── Order summary aside ─────────────────────────────── */
.ck-summary { position: sticky; top: 84px; }
.ck-summary-head {
    padding: 18px 22px 14px;
    border-bottom: 1px solid var(--cb-border);
    position: relative;
}
.ck-summary-head::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
    background: linear-gradient(90deg, var(--cb-accent), #4ade80);
}
.ck-summary-title {
    font-family: var(--cb-serif); font-size: 18px; font-weight: 700; color: var(--cb-text);
}

/* Item list */
.ck-item-list { padding: 14px 22px; display: flex; flex-direction: column; gap: 10px; }
.ck-item {
    display: flex; align-items: flex-start; gap: 12px;
    padding: 12px 14px; border-radius: 12px;
    background: var(--cb-bg); border: 1px solid var(--cb-border);
}
.ck-item-thumb {
    width: 42px; height: 56px; border-radius: 7px;
    background: #ede9e1; overflow: hidden; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
}
.ck-item-thumb img { width: 100%; height: 100%; object-fit: cover; }
.ck-item-thumb-ph {
    font-family: var(--cb-serif); font-size: 18px; font-weight: 900; color: #c5bdb0;
}
.ck-item-body { flex: 1; min-width: 0; }
.ck-item-title {
    font-family: var(--cb-sans); font-size: 13px; font-weight: 600;
    color: var(--cb-text); line-height: 1.4; margin-bottom: 5px;
    display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
    line-clamp: 2;
}
.ck-item-meta {
    display: flex; align-items: center; justify-content: space-between;
    font-family: var(--cb-sans); font-size: 12px; color: var(--cb-muted);
}
.ck-item-price { font-weight: 600; color: var(--cb-accent); }

/* Totals */
.ck-totals {
    padding: 14px 22px 20px;
    border-top: 1px solid var(--cb-border);
    display: flex; flex-direction: column; gap: 10px;
}
.ck-total-row {
    display: flex; align-items: center; justify-content: space-between;
    font-family: var(--cb-sans); font-size: 13px; color: var(--cb-muted);
}
.ck-total-row strong { color: var(--cb-text); font-weight: 600; }
.ck-total-divider { height: 1px; background: var(--cb-border); }
.ck-grand-row {
    display: flex; align-items: center; justify-content: space-between;
    font-family: var(--cb-sans);
}
.ck-grand-lbl { font-size: 14px; font-weight: 600; color: var(--cb-text); }
.ck-grand-val {
    font-family: var(--cb-serif); font-size: 28px; font-weight: 900;
    color: var(--cb-accent); letter-spacing: -.5px; line-height: 1;
}

/* ─── Progress steps ──────────────────────────────────── */
.ck-steps {
    display: flex; align-items: center; justify-content: center;
    gap: 0; margin-bottom: 28px;
}
.ck-step {
    display: flex; align-items: center; gap: 8px;
    font-family: var(--cb-sans); font-size: 12px; font-weight: 500;
}
.ck-step-dot {
    width: 26px; height: 26px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 11px; font-weight: 700;
}
.ck-step-dot.done  { background: var(--cb-accent-light); color: var(--cb-accent); }
.ck-step-dot.active { background: var(--cb-accent); color: #fff; }
.ck-step-dot.future { background: var(--cb-bg); border: 1.5px solid var(--cb-border); color: var(--cb-muted); }
.ck-step-lbl.active { color: var(--cb-accent); font-weight: 600; }
.ck-step-lbl.future { color: var(--cb-muted); }
.ck-step-lbl.done   { color: var(--cb-accent); }
.ck-step-line {
    width: 48px; height: 1.5px; background: var(--cb-border); margin: 0 6px;
}
.ck-step-line.done { background: var(--cb-accent); }
</style>

{{-- ── Page header ──────────────────────────────────────── --}}
<div class="ck-header">
    {{-- Progress steps --}}
    <div class="ck-steps">
        <div class="ck-step">
            <div class="ck-step-dot done">✓</div>
            <span class="ck-step-lbl done">Giỏ hàng</span>
        </div>
        <div class="ck-step-line done"></div>
        <div class="ck-step">
            <div class="ck-step-dot active">2</div>
            <span class="ck-step-lbl active">Thanh toán</span>
        </div>
        <div class="ck-step-line"></div>
        <div class="ck-step">
            <div class="ck-step-dot future">3</div>
            <span class="ck-step-lbl future">Hoàn tất</span>
        </div>
    </div>

    <div class="ck-eyebrow">Đặt hàng</div>
    <h1 class="ck-heading">Xác nhận đơn hàng</h1>
</div>

{{-- ── Error flash ───────────────────────────────────────── --}}
@if($errors->any())
    <div class="ck-error-flash">
        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/>
            <line x1="12" y1="16" x2="12.01" y2="16"/>
        </svg>
        {{ $errors->first() }}
    </div>
@endif

{{-- ════════════════════════════════════════════════════════
     LAYOUT
════════════════════════════════════════════════════════ --}}
@php
    $selectedMethod = old('payment_method', 'cod');

    $onlineMethods = [
        'bank_transfer' => [
            'title'   => 'Chuyển khoản',
            'desc'    => 'Quét QR hoặc chuyển khoản trực tiếp theo thông tin đơn hàng.',
            'tag'     => 'Phổ biến',
            'tag_cls' => 'ck-tag-blue',
            'icon'    => 'BK',
            'icon_cls'=> 'ck-pay-icon-bk',
        ],
        'momo' => [
            'title'   => 'Ví MoMo',
            'desc'    => 'Thanh toán bằng ví MoMo, xác nhận trong vài giây.',
            'tag'     => 'Nhanh',
            'tag_cls' => 'ck-tag-pink',
            'icon'    => 'M',
            'icon_cls'=> 'ck-pay-icon-mm',
        ],
        'vnpay' => [
            'title'   => 'VNPay',
            'desc'    => 'Thanh toán qua VNPay bằng ngân hàng, ATM hoặc thẻ quốc tế.',
            'tag'     => 'An toàn',
            'tag_cls' => 'ck-tag-orange',
            'icon'    => 'V',
            'icon_cls'=> 'ck-pay-icon-vn',
        ],
    ];

    $fallbackAddress = $defaultAddress
        ? collect([$defaultAddress->address_line, $defaultAddress->ward, $defaultAddress->district, $defaultAddress->province])->filter()->implode(', ')
        : null;
@endphp

<div class="ck-layout">

    {{-- ── LEFT: Checkout form ───────────────────────────── --}}
    <form method="POST" action="{{ route('checkout.store') }}">
        @csrf

        {{-- 1. Shipping info --}}
        <div class="ck-card" style="margin-bottom:16px">
            <div class="ck-card-head">
                <div class="ck-card-title">Thông tin nhận hàng</div>
                <div class="ck-card-sub">Điền đầy đủ để đảm bảo giao hàng chính xác</div>
            </div>
            <div class="ck-card-body">
                <div class="ck-grid-2" style="margin-bottom:14px">
                    <div class="ck-field">
                        <label for="recipient_name" class="ck-label">Người nhận</label>
                        <input id="recipient_name" name="recipient_name" type="text"
                               required
                               value="{{ old('recipient_name', $defaultAddress?->receiver_name ?? auth()->user()->full_name) }}"
                               class="ck-input"
                               placeholder="Họ và tên người nhận">
                    </div>
                    <div class="ck-field">
                        <label for="recipient_phone" class="ck-label">Số điện thoại</label>
                        <input id="recipient_phone" name="recipient_phone" type="text"
                               required
                               value="{{ old('recipient_phone', $defaultAddress?->receiver_phone ?? auth()->user()->phone) }}"
                               class="ck-input"
                               placeholder="0xxxxxxxxx">
                    </div>
                </div>
                <div class="ck-field">
                    <label for="shipping_address" class="ck-label">Địa chỉ giao hàng</label>
                    <textarea id="shipping_address" name="shipping_address"
                              rows="3" required
                              class="ck-textarea"
                              placeholder="Số nhà, đường, phường/xã, quận/huyện, tỉnh/thành phố">{{ old('shipping_address', $fallbackAddress) }}</textarea>
                </div>
            </div>
        </div>

        {{-- 2. Payment method --}}
        <div class="ck-card" style="margin-bottom:16px">
            <div class="ck-card-head">
                <div class="ck-card-title">Phương thức thanh toán</div>
                <div class="ck-card-sub">Chọn hình thức phù hợp với bạn</div>
            </div>
            <div class="ck-card-body">

                {{-- Online methods --}}
                <p class="ck-pay-section-title">Thanh toán online</p>
                <div class="ck-pay-grid">
                    @foreach($onlineMethods as $value => $method)
                        <label class="ck-pay-card {{ $selectedMethod === $value ? 'selected' : '' }}"
                               data-method="{{ $value }}">
                            <input type="radio" name="payment_method"
                                   value="{{ $value }}"
                                   {{ $selectedMethod === $value ? 'checked' : '' }}>

                            {{-- Check circle --}}
                            <div class="ck-pay-check">
                                <svg width="10" height="10" fill="none" stroke="#fff" stroke-width="2.5" viewBox="0 0 24 24">
                                    <polyline points="20 6 9 17 4 12"/>
                                </svg>
                            </div>

                            <div class="ck-pay-icon {{ $method['icon_cls'] }}">
                                {{ $method['icon'] }}
                            </div>
                            <div class="ck-pay-name">{{ $method['title'] }}</div>
                            <div class="ck-pay-desc">{{ $method['desc'] }}</div>
                            <span class="ck-pay-tag {{ $method['tag_cls'] }}">{{ $method['tag'] }}</span>
                        </label>
                    @endforeach
                </div>

                {{-- QR block (bank_transfer) --}}
                <div class="ck-qr-wrap bank-qr {{ $selectedMethod !== 'bank_transfer' ? 'hidden' : '' }}"
                     style="margin-bottom:12px">
                    <p style="font-family:var(--cb-sans);font-size:12px;color:var(--cb-muted);margin-bottom:8px">
                        Quét mã QR để chuyển khoản nhanh:
                    </p>
                    <img src="{{ asset('images/QRCode.png') }}" alt="QR chuyển khoản">
                    <p class="ck-qr-note">Hoặc chuyển theo thông tin trên hoá đơn sau khi đặt hàng.</p>
                </div>

                {{-- COD --}}
                <p class="ck-pay-section-title" style="margin-top:16px">Thanh toán khi nhận hàng</p>
                <label class="ck-cod-row {{ $selectedMethod === 'cod' ? 'selected' : '' }}"
                       data-method="cod">
                    <input type="radio" name="payment_method" value="cod"
                           {{ $selectedMethod === 'cod' ? 'checked' : '' }}>
                    <div class="ck-pay-icon ck-pay-icon-cod" style="width:36px;height:36px;border-radius:9px;flex-shrink:0">
                        COD
                    </div>
                    <div>
                        <div class="ck-cod-name">Thanh toán khi nhận hàng (COD)</div>
                        <div class="ck-cod-desc">Dành cho khách muốn thanh toán trực tiếp cho nhân viên giao hàng.</div>
                    </div>
                </label>

                {{-- Tips --}}
                <div class="ck-tips">
                    <p class="ck-tips-title">Gợi ý lựa chọn</p>
                    <ul>
                        <li>Chuyển khoản phù hợp khi muốn đối soát nhanh bằng QR hoặc số tài khoản.</li>
                        <li>MoMo tiện cho thanh toán trên điện thoại, đặc biệt khi đặt hàng thường xuyên.</li>
                        <li>VNPay hỗ trợ nhiều ngân hàng và thẻ quốc tế, an toàn và quen thuộc.</li>
                    </ul>
                </div>
            </div>
        </div>

        {{-- 3. Note --}}
        <div class="ck-card" style="margin-bottom:16px">
            <div class="ck-card-head">
                <div class="ck-card-title">Ghi chú đơn hàng</div>
                <div class="ck-card-sub">Không bắt buộc</div>
            </div>
            <div class="ck-card-body">
                <textarea id="note" name="note" rows="3" class="ck-textarea"
                          placeholder="Yêu cầu đặc biệt, thời gian giao hàng mong muốn...">{{ old('note') }}</textarea>
            </div>
        </div>

        {{-- Actions --}}
        <div class="ck-actions">
            <button type="submit" class="ck-btn-submit">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M5 12h14M12 5l7 7-7 7"/>
                </svg>
                Đặt hàng ngay
            </button>
            <a href="{{ route('cart.index') }}" class="ck-btn-back">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M19 12H5M12 19l-7-7 7-7"/>
                </svg>
                Quay lại giỏ hàng
            </a>
        </div>
    </form>

    {{-- ── RIGHT: Order summary ────────────────────────────── --}}
    <aside class="ck-card ck-summary">
        <div class="ck-summary-head">
            <div class="ck-summary-title">Đơn hàng của bạn</div>
        </div>

        {{-- Item list --}}
        <div class="ck-item-list">
            @foreach($items as $item)
                @php
                    $itmCover = null;
                    if (!empty($item->book->cover_image)) {
                        $itmCover = str_starts_with($item->book->cover_image, 'http://') || str_starts_with($item->book->cover_image, 'https://')
                            ? $item->book->cover_image
                            : asset('storage/'.$item->book->cover_image);
                    }
                    $itmInit = mb_strtoupper(mb_substr($item->book->title, 0, 1));
                @endphp
                <div class="ck-item">
                    <div class="ck-item-thumb">
                        @if($itmCover)
                            <img src="{{ $itmCover }}" alt="{{ $item->book->title }}" loading="lazy">
                        @else
                            <span class="ck-item-thumb-ph">{{ $itmInit }}</span>
                        @endif
                    </div>
                    <div class="ck-item-body">
                        <div class="ck-item-title">{{ $item->book->title }}</div>
                        <div class="ck-item-meta">
                            <span>x{{ $item->quantity }}</span>
                            <span class="ck-item-price">
                                {{ number_format((float)$item->unit_price * $item->quantity, 0, ',', '.') }}đ
                            </span>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        {{-- Totals --}}
        <div class="ck-totals">
            <div class="ck-total-row">
                <span>Tạm tính</span>
                <strong>{{ number_format($subtotal, 0, ',', '.') }}đ</strong>
            </div>
            <div class="ck-total-row">
                <span>Phí vận chuyển</span>
                <strong>{{ number_format($shippingFee, 0, ',', '.') }}đ</strong>
            </div>
            <div class="ck-total-divider"></div>
            <div class="ck-grand-row">
                <span class="ck-grand-lbl">Tổng thanh toán</span>
                <span class="ck-grand-val">{{ number_format($total, 0, ',', '.') }}đ</span>
            </div>
        </div>
    </aside>

</div>{{-- /.ck-layout --}}

<script>
document.addEventListener('DOMContentLoaded', function () {
    const cards    = document.querySelectorAll('.ck-pay-card, .ck-cod-row');
    const inputs   = document.querySelectorAll('input[name="payment_method"]');
    const qrBlock  = document.querySelector('.bank-qr');

    /* ── Payment card selection ── */
    function syncSelection() {
        const val = document.querySelector('input[name="payment_method"]:checked')?.value;

        cards.forEach(card => {
            const m = card.dataset.method;
            card.classList.toggle('selected', m === val);
        });

        if (qrBlock) {
            qrBlock.classList.toggle('hidden', val !== 'bank_transfer');
        }
    }

    cards.forEach(card => {
        card.addEventListener('click', function () {
            const inp = this.querySelector('input[type="radio"]');
            if (inp) { inp.checked = true; syncSelection(); }
        });
    });

    inputs.forEach(inp => inp.addEventListener('change', syncSelection));

    syncSelection();
});
</script>

@endsection