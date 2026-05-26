@extends('layouts.app')

@section('title', 'Thanh toán VNPay — ' . $order->order_code)

@section('content')
<style>
/* ─── Design tokens ───────────────────────────────────── */
:root {
    --cb-bg:           var(--cb-brand-bg);
    --cb-border:       var(--cb-brand-border);
    --cb-text:         var(--cb-brand-text);
    --cb-muted:        var(--cb-brand-muted);
    --cb-white:        var(--cb-brand-white);
    --cb-accent:       var(--cb-brand-accent);
    --cb-accent-dark:  var(--cb-brand-accent-dark);
    --cb-accent-light: var(--cb-brand-accent-light);
    --cb-serif:        'Playfair Display', Georgia, serif;
    --cb-sans:         'DM Sans', system-ui, sans-serif;
}
html, body {
    background: var(--cb-bg);
    margin: 0;
}
/* ─── Page wrapper ────────────────────────────────────── */
.vq-wrap {
    max-width: 960px;
    margin: 0 auto;
    padding: 40px 24px 64px;
}
.vnpay-page-gap { margin-bottom: 72px; }

/* ─── Breadcrumb ──────────────────────────────────────── */
.vq-crumb {
    display: flex; align-items: center; gap: 6px; flex-wrap: wrap;
    font-family: var(--cb-sans); font-size: 12px; color: var(--cb-muted);
    margin-bottom: 28px;
}
.vq-crumb a { color: var(--cb-muted); text-decoration: none; transition: color .15s; }
.vq-crumb a:hover { color: var(--cb-accent); }
.vq-crumb-sep { opacity: .4; }

/* ─── Page header ─────────────────────────────────────── */
.vq-header { margin-bottom: 28px; }
.vq-eyebrow {
    display: inline-flex; align-items: center; gap: 7px;
    font-family: var(--cb-sans); font-size: 11px; font-weight: 600;
    letter-spacing: 1.8px; text-transform: uppercase;
    color: var(--cb-accent); background: var(--cb-accent-light);
    padding: 4px 13px; border-radius: 999px; margin-bottom: 12px;
}
.vq-eyebrow-dot {
    width: 6px; height: 6px; border-radius: 50%;
    background: var(--cb-accent);
    animation: pulse-dot 2s ease-in-out infinite;
}
@keyframes pulse-dot {
    0%,100%{ opacity:1; transform:scale(1); }
    50%{ opacity:.5; transform:scale(1.5); }
}
.vq-heading {
    font-family: var(--cb-serif);
    font-size: 36px; font-weight: 900; color: #0d1b10;
    letter-spacing: -1px; line-height: 1.08; margin: 0;
}

/* ─── 2-col layout ────────────────────────────────────── */
.vq-grid {
    display: grid;
    grid-template-columns: 1fr 340px;
    gap: 24px; align-items: start;
}
@media (max-width: 860px) { .vq-grid { grid-template-columns: 1fr; } }

/* ─── Left card (info) ────────────────────────────────── */
.vq-info-card {
    background: var(--cb-white); border: 1px solid var(--cb-border);
    border-radius: 18px; overflow: hidden;
}
.vq-info-head {
    padding: 18px 24px; border-bottom: 1px solid var(--cb-border);
    position: relative;
}
.vq-info-head::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
    background: linear-gradient(90deg, var(--cb-accent), #4ade80);
}
.vq-info-title {
    font-family: var(--cb-serif); font-size: 18px; font-weight: 700;
    color: var(--cb-text); margin: 0;
}

/* Meta rows */
.vq-meta-list { padding: 18px 24px; display: flex; flex-direction: column; gap: 12px; }
.vq-meta-row {
    display: flex; align-items: center; justify-content: space-between;
    gap: 12px; font-family: var(--cb-sans); font-size: 13px;
    padding: 10px 14px; border-radius: 10px;
    background: var(--cb-bg); border: 1px solid var(--cb-border);
}
.vq-meta-lbl { color: var(--cb-muted); font-weight: 500; }
.vq-meta-val { color: var(--cb-text); font-weight: 700; }
.vq-meta-val.big {
    font-family: var(--cb-serif); font-size: 22px; color: var(--cb-accent);
    letter-spacing: -.3px;
}
.vq-meta-val.mono {
    font-family: monospace; font-size: 12px; letter-spacing: .5px;
    background: var(--cb-white); padding: 3px 8px; border-radius: 6px;
    border: 1px solid var(--cb-border);
}

/* Steps */
.vq-steps { padding: 18px 24px; border-top: 1px solid var(--cb-border); }
.vq-steps-title {
    font-family: var(--cb-sans); font-size: 12px; font-weight: 700;
    letter-spacing: 1.2px; text-transform: uppercase; color: #b0a898;
    margin-bottom: 14px;
}
.vq-step-list { display: flex; flex-direction: column; gap: 10px; }
.vq-step {
    display: flex; align-items: flex-start; gap: 12px;
    font-family: var(--cb-sans); font-size: 13px; color: var(--cb-muted);
    line-height: 1.5;
}
.vq-step-num {
    width: 22px; height: 22px; border-radius: 50%; flex-shrink: 0;
    background: var(--cb-accent-light); color: var(--cb-accent);
    font-size: 11px; font-weight: 700;
    display: flex; align-items: center; justify-content: center;
    margin-top: 1px;
}

/* ─── Right card (QR) ─────────────────────────────────── */
.vq-qr-card {
    background: var(--cb-white); border: 1px solid var(--cb-border);
    border-radius: 18px; overflow: hidden;
    position: sticky; top: 84px;
}
.vq-qr-head {
    padding: 16px 22px 12px; border-bottom: 1px solid var(--cb-border);
    text-align: center; position: relative;
}
.vq-qr-head::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
    background: linear-gradient(90deg, #f59e0b, #dc2626);
}
.vq-qr-head-title {
    font-family: var(--cb-sans); font-size: 13px; font-weight: 600;
    color: var(--cb-text);
}
.vq-qr-head-sub {
    font-family: var(--cb-sans); font-size: 11px; color: var(--cb-muted); margin-top: 2px;
}

/* QR image */
.vq-qr-body { padding: 20px; display: flex; flex-direction: column; align-items: center; gap: 16px; }
.vq-qr-shell {
    width: 100%; max-width: 240px;
    padding: 14px; border-radius: 16px;
    background: var(--cb-white); border: 2px solid var(--cb-border);
    box-shadow: 0 4px 20px rgba(0,0,0,.07);
}
.vq-qr-shell img { display: block; width: 100%; height: auto; border-radius: 8px; }

/* Amount badge on QR card */
.vq-qr-amount {
    font-family: var(--cb-serif); font-size: 26px; font-weight: 900;
    color: var(--cb-accent); text-align: center; letter-spacing: -.5px;
}
.vq-qr-amount-sub {
    font-family: var(--cb-sans); font-size: 11px; color: var(--cb-muted);
    text-align: center; margin-top: -10px;
}

/* Expiry countdown */
.vq-countdown {
    display: flex; align-items: center; gap: 7px;
    font-family: var(--cb-sans); font-size: 12px; font-weight: 600;
    padding: 7px 14px; border-radius: 999px;
    background: #fff7ed; color: #c2410c; border: 1px solid #fed7aa;
}
.vq-countdown-dot {
    width: 6px; height: 6px; border-radius: 50%; background: #f97316;
    animation: pulse-dot 1.2s ease-in-out infinite;
}

/* Alert */
.vq-alert {
    width: 100%; padding: 12px 14px; border-radius: 10px;
    background: var(--cb-accent-light); color: var(--cb-accent-dark);
    font-family: var(--cb-sans); font-size: 12px; line-height: 1.6;
    border: 1px solid #86efac;
    display: flex; align-items: flex-start; gap: 8px;
}
.vq-alert svg { flex-shrink: 0; margin-top: 1px; }

/* Buttons */
.vq-btn-primary {
    display: flex; align-items: center; justify-content: center; gap: 8px;
    width: 100%; font-family: var(--cb-sans); font-size: 14px; font-weight: 600;
    padding: 13px 20px; border-radius: 12px; border: none;
    background: var(--cb-accent); color: #fff; text-decoration: none;
    cursor: pointer; transition: background .2s, transform .15s;
}
.vq-btn-primary:hover { background: var(--cb-accent-dark); transform: translateY(-1px); }

.vq-btn-secondary {
    display: flex; align-items: center; justify-content: center; gap: 8px;
    width: 100%; font-family: var(--cb-sans); font-size: 13px; font-weight: 500;
    padding: 11px 20px; border-radius: 12px;
    border: 1.5px solid var(--cb-border); background: var(--cb-white);
    color: var(--cb-muted); text-decoration: none;
    transition: border-color .2s, color .2s;
}
.vq-btn-secondary:hover { border-color: var(--cb-accent); color: var(--cb-accent); }

.vq-btn-group { display: flex; flex-direction: column; gap: 8px; width: 100%; }

/* Note */
.vq-note {
    font-family: var(--cb-sans); font-size: 11px; color: #b0a898;
    text-align: center; line-height: 1.6;
}
</style>

<div class="vnpay-page-gap">
<div class="vq-wrap">

    {{-- Breadcrumb --}}
    <nav class="vq-crumb">
        <a href="{{ route('home') }}">Trang chủ</a>
        <span class="vq-crumb-sep">/</span>
        <a href="{{ route('orders.index') }}">Đơn hàng</a>
        <span class="vq-crumb-sep">/</span>
        <a href="{{ route('orders.show', $order) }}">{{ $order->order_code }}</a>
        <span class="vq-crumb-sep">/</span>
        <span style="color:var(--cb-text);font-weight:500">Thanh toán VNPay</span>
    </nav>

    {{-- Page header --}}
    <div class="vq-header">
        <div class="vq-eyebrow">
            <span class="vq-eyebrow-dot"></span>
            VNPay QR
        </div>
        <h1 class="vq-heading">Quét mã để thanh toán</h1>
    </div>

    {{-- Main grid --}}
    <div class="vq-grid">

        {{-- ── LEFT: Order info + steps ─────────────────── --}}
        <div class="vq-info-card">
            <div class="vq-info-head">
                <h2 class="vq-info-title">Thông tin đơn hàng</h2>
            </div>

            <div class="vq-meta-list">
                <div class="vq-meta-row">
                    <span class="vq-meta-lbl">Mã đơn hàng</span>
                    <span class="vq-meta-val mono">{{ $order->order_code }}</span>
                </div>
                <div class="vq-meta-row">
                    <span class="vq-meta-lbl">Mã tham chiếu VNPay</span>
                    <span class="vq-meta-val mono">{{ $vnpTxnRef }}</span>
                </div>
                <div class="vq-meta-row">
                    <span class="vq-meta-lbl">Số tiền cần thanh toán</span>
                    <span class="vq-meta-val big">{{ number_format($vnpAmount, 0, ',', '.') }}đ</span>
                </div>
            </div>

            <div class="vq-steps">
                <p class="vq-steps-title">Hướng dẫn thanh toán</p>
                <div class="vq-step-list">
                    <div class="vq-step">
                        <span class="vq-step-num">1</span>
                        Mở ứng dụng ngân hàng hoặc VNPay trên điện thoại của bạn.
                    </div>
                    <div class="vq-step">
                        <span class="vq-step-num">2</span>
                        Chọn chức năng <strong>Quét mã QR</strong> và hướng camera vào mã bên phải.
                    </div>
                    <div class="vq-step">
                        <span class="vq-step-num">3</span>
                        Kiểm tra <strong>số tiền</strong> và xác nhận thanh toán.
                    </div>
                    <div class="vq-step">
                        <span class="vq-step-num">4</span>
                        Hệ thống sẽ tự động cập nhật trạng thái đơn hàng sau khi thanh toán thành công.
                    </div>
                </div>
            </div>
        </div>

        {{-- ── RIGHT: QR card ────────────────────────────── --}}
        <aside class="vq-qr-card">
            <div class="vq-qr-head">
                <p class="vq-qr-head-title">Mã QR thanh toán</p>
            </div>

            <div class="vq-qr-body">

                {{-- QR image --}}
                <div class="vq-qr-shell">
                    <img src="{{ $qrImageUrl }}"
                         alt="Mã QR thanh toán VNPay cho đơn {{ $order->order_code }}">
                </div>

                {{-- Amount --}}
                <div>
                    <p class="vq-qr-amount">{{ number_format($vnpAmount, 0, ',', '.') }}đ</p>
                </div>

                {{-- Countdown --}}
                <div class="vq-countdown">
                    <span class="vq-countdown-dot"></span>
                    Mã QR còn hiệu lực trong 15 phút
                </div>

                {{-- Alert --}}
                <div class="vq-alert">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="flex-shrink:0;margin-top:1px">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="12" y1="8" x2="12" y2="12"/>
                        <line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    <span>Vui lòng xác nhận đúng số tiền trước khi thanh toán. Mã QR chỉ dùng một lần.</span>
                </div>

                {{-- Buttons --}}
                <div class="vq-btn-group">
                    <a href="{{ $paymentUrl }}" class="vq-btn-primary">
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"/>
                            <polyline points="15 3 21 3 21 9"/>
                            <line x1="10" y1="14" x2="21" y2="3"/>
                        </svg>
                        Mở trang VNPay
                    </a>
                    <a href="{{ route('orders.show', $order) }}" class="vq-btn-secondary">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/>
                            <rect x="9" y="3" width="6" height="4" rx="1"/>
                        </svg>
                        Xem chi tiết đơn hàng
                    </a>
                </div>

                <p class="vq-note">
                    Nếu QR không tải được, nhấn nút "Mở trang VNPay" để thanh toán trực tiếp.
                </p>
            </div>
        </aside>
    </div>
</div>
</div>

@endsection