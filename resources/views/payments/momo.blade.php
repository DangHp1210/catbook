@extends('layouts.app')

@section('title', 'Thanh toán MoMo — ' . $order->order_code)

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

    /* MoMo brand */
    --mm-pink:         #a50064;
    --mm-pink-dark:    #7f004c;
    --mm-pink-light:   #fce7f3;
    --mm-pink-border:  #fbcfe8;
}
html, body {
    background: var(--cb-bg);
    margin: 0;
}
/* ─── Page wrapper ────────────────────────────────────── */
.mm-wrap {
    max-width: 960px;
    margin: 0 auto;
    padding: 40px 24px 64px;
}
.momo-page-gap { margin-bottom: 72px; }

/* ─── Breadcrumb ──────────────────────────────────────── */
.mm-crumb {
    display: flex; align-items: center; gap: 6px; flex-wrap: wrap;
    font-family: var(--cb-sans); font-size: 12px; color: var(--cb-muted);
    margin-bottom: 28px;
}
.mm-crumb a { color: var(--cb-muted); text-decoration: none; transition: color .15s; }
.mm-crumb a:hover { color: var(--cb-accent); }
.mm-crumb-sep { opacity: .4; }

/* ─── Page header ─────────────────────────────────────── */
.mm-header { margin-bottom: 28px; }
.mm-eyebrow {
    display: inline-flex; align-items: center; gap: 7px;
    font-family: var(--cb-sans); font-size: 11px; font-weight: 700;
    letter-spacing: 1.8px; text-transform: uppercase;
    color: var(--mm-pink-dark); background: var(--mm-pink-light);
    padding: 4px 13px; border-radius: 999px; margin-bottom: 12px;
    border: 1px solid var(--mm-pink-border);
}
.mm-eyebrow-dot {
    width: 6px; height: 6px; border-radius: 50%;
    background: var(--mm-pink);
    animation: pulse-mm 2s ease-in-out infinite;
}
@keyframes pulse-mm {
    0%,100%{ opacity:1; transform:scale(1); }
    50%{ opacity:.5; transform:scale(1.5); }
}
.mm-heading {
    font-family: var(--cb-serif);
    font-size: 36px; font-weight: 900; color: #0d1b10;
    letter-spacing: -1px; line-height: 1.08; margin: 0;
}

/* ─── 2-col grid ──────────────────────────────────────── */
.mm-grid {
    display: grid;
    grid-template-columns: 1fr 340px;
    gap: 24px; align-items: start;
}
@media (max-width: 860px) { .mm-grid { grid-template-columns: 1fr; } }

/* ─── Left info card ──────────────────────────────────── */
.mm-info-card {
    background: var(--cb-white); border: 1px solid var(--cb-border);
    border-radius: 18px; overflow: hidden;
}
.mm-info-head {
    padding: 18px 24px; border-bottom: 1px solid var(--cb-border);
    position: relative;
}
.mm-info-head::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
    background: linear-gradient(90deg, var(--mm-pink), #f472b6);
}
.mm-info-title {
    font-family: var(--cb-serif); font-size: 18px; font-weight: 700;
    color: var(--cb-text); margin: 0;
}
.mm-info-body { padding: 18px 24px; display: flex; flex-direction: column; gap: 12px; }

/* Meta rows */
.mm-meta-row {
    display: flex; align-items: center; justify-content: space-between;
    gap: 12px; font-family: var(--cb-sans); font-size: 13px;
    padding: 10px 14px; border-radius: 10px;
    background: var(--cb-bg); border: 1px solid var(--cb-border);
}
.mm-meta-lbl { color: var(--cb-muted); font-weight: 500; }
.mm-meta-val { color: var(--cb-text); font-weight: 700; }
.mm-meta-val.big {
    font-family: var(--cb-serif); font-size: 22px;
    color: var(--mm-pink); letter-spacing: -.3px;
}

/* Alert */
.mm-alert {
    display: flex; align-items: flex-start; gap: 10px;
    padding: 13px 15px; border-radius: 10px;
    background: var(--mm-pink-light); color: var(--mm-pink-dark);
    border: 1px solid var(--mm-pink-border);
    font-family: var(--cb-sans); font-size: 13px; line-height: 1.6;
}
.mm-alert svg { flex-shrink: 0; margin-top: 1px; }

/* Steps */
.mm-steps { padding: 18px 24px; border-top: 1px solid var(--cb-border); }
.mm-steps-title {
    font-family: var(--cb-sans); font-size: 11px; font-weight: 700;
    letter-spacing: 1.2px; text-transform: uppercase; color: #b0a898; margin-bottom: 14px;
}
.mm-step-list { display: flex; flex-direction: column; gap: 10px; }
.mm-step {
    display: flex; align-items: flex-start; gap: 12px;
    font-family: var(--cb-sans); font-size: 13px; color: var(--cb-muted); line-height: 1.5;
}
.mm-step-num {
    width: 22px; height: 22px; border-radius: 50%; flex-shrink: 0;
    background: var(--mm-pink-light); color: var(--mm-pink-dark);
    font-size: 11px; font-weight: 700;
    display: flex; align-items: center; justify-content: center; margin-top: 1px;
}

/* ─── Right action card ───────────────────────────────── */
.mm-action-card {
    background: var(--cb-white); border: 1px solid var(--cb-border);
    border-radius: 18px; overflow: hidden;
    position: sticky; top: 84px;
}
.mm-action-head {
    padding: 16px 22px 12px; border-bottom: 1px solid var(--cb-border);
    text-align: center; position: relative;
}
.mm-action-head::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
    background: linear-gradient(90deg, var(--mm-pink), #f472b6);
}
.mm-action-head-title {
    font-family: var(--cb-sans); font-size: 13px; font-weight: 600; color: var(--cb-text);
}
.mm-action-head-sub {
    font-family: var(--cb-sans); font-size: 11px; color: var(--cb-muted); margin-top: 2px;
}
.mm-action-body {
    padding: 20px 20px; display: flex; flex-direction: column;
    align-items: center; gap: 16px;
}

/* MoMo logo area */
.mm-logo-wrap {
    width: 80px; height: 80px; border-radius: 22px;
    background: var(--mm-pink-light); border: 2px solid var(--mm-pink-border);
    display: flex; align-items: center; justify-content: center;
    flex-shrink: 0;
}
.mm-logo-text {
    font-family: var(--cb-serif); font-size: 22px; font-weight: 900;
    color: var(--mm-pink); letter-spacing: -1px;
}

/* Amount display */
.mm-amount {
    text-align: center;
}
.mm-amount-val {
    font-family: var(--cb-serif); font-size: 32px; font-weight: 900;
    color: var(--mm-pink); letter-spacing: -.5px; display: block;
}
.mm-amount-lbl {
    font-family: var(--cb-sans); font-size: 12px; color: var(--cb-muted); margin-top: 2px;
}

/* Countdown */
.mm-countdown {
    display: flex; align-items: center; gap: 7px;
    font-family: var(--cb-sans); font-size: 13px; font-weight: 700;
    padding: 9px 18px; border-radius: 999px;
    background: #fff7ed; color: #c2410c; border: 1px solid #fed7aa;
}
.mm-countdown-dot {
    width: 6px; height: 6px; border-radius: 50%; background: #f97316;
    animation: pulse-mm 1.2s ease-in-out infinite;
}
.mm-countdown-time { font-variant-numeric: tabular-nums; letter-spacing: .5px; }

/* Action alert */
.mm-action-alert {
    width: 100%; display: flex; align-items: flex-start; gap: 8px;
    padding: 11px 13px; border-radius: 10px;
    background: var(--mm-pink-light); color: var(--mm-pink-dark);
    border: 1px solid var(--mm-pink-border);
    font-family: var(--cb-sans); font-size: 12px; line-height: 1.6;
    text-align: left;
}
.mm-action-alert svg { flex-shrink: 0; margin-top: 1px; }

/* Buttons */
.mm-btn-group { display: flex; flex-direction: column; gap: 8px; width: 100%; }

.mm-btn-primary {
    display: flex; align-items: center; justify-content: center; gap: 8px;
    width: 100%; font-family: var(--cb-sans); font-size: 14px; font-weight: 700;
    padding: 13px 20px; border-radius: 12px; border: none;
    background: var(--mm-pink); color: #ffffff; text-decoration: none;
    cursor: pointer; transition: background .2s, transform .15s;
}
.mm-btn-primary:hover { background: var(--mm-pink-dark); transform: translateY(-1px); }

.mm-btn-secondary {
    display: flex; align-items: center; justify-content: center; gap: 8px;
    width: 100%; font-family: var(--cb-sans); font-size: 13px; font-weight: 500;
    padding: 11px 20px; border-radius: 12px;
    border: 1.5px solid var(--cb-border); background: var(--cb-white);
    color: var(--cb-muted); text-decoration: none;
    transition: border-color .2s, color .2s;
}
.mm-btn-secondary:hover { border-color: var(--mm-pink); color: var(--mm-pink); }

.mm-btn-outline {
    display: flex; align-items: center; justify-content: center; gap: 8px;
    width: 100%; font-family: var(--cb-sans); font-size: 13px; font-weight: 500;
    padding: 11px 20px; border-radius: 12px;
    border: 1.5px solid var(--cb-border); background: var(--cb-white);
    color: var(--cb-muted); text-decoration: none;
    transition: border-color .2s, color .2s;
}
.mm-btn-outline:hover { border-color: var(--cb-accent); color: var(--cb-accent); }

/* Note */
.mm-note {
    font-family: var(--cb-sans); font-size: 11px; color: #b0a898;
    text-align: center; line-height: 1.6;
}

/* Divider in action body */
.mm-body-sep { width: 100%; height: 1px; background: var(--cb-border); }
</style>

<div class="momo-page-gap">
<div class="mm-wrap">

    {{-- ── Breadcrumb ── --}}
    <nav class="mm-crumb">
        <a href="{{ route('home') }}">Trang chủ</a>
        <span class="mm-crumb-sep">/</span>
        <a href="{{ route('orders.index') }}">Đơn hàng</a>
        <span class="mm-crumb-sep">/</span>
        <a href="{{ route('orders.show', $order) }}">{{ $order->order_code }}</a>
        <span class="mm-crumb-sep">/</span>
        <span style="color:var(--cb-text);font-weight:500">Thanh toán MoMo</span>
    </nav>

    {{-- ── Page header ── --}}
    <div class="mm-header">
        <div class="mm-eyebrow">
            <span class="mm-eyebrow-dot"></span>
            MoMo Payment
        </div>
        <h1 class="mm-heading">Mở MoMo để thanh toán</h1>
    </div>

    {{-- ── Main grid ── --}}
    <div class="mm-grid">

        {{-- LEFT: Order info + steps --}}
        <div class="mm-info-card">
            <div class="mm-info-head">
                <h2 class="mm-info-title">Thông tin đơn hàng</h2>
            </div>
            <div class="mm-info-body">
                <div class="mm-meta-row">
                    <span class="mm-meta-lbl">Mã đơn hàng</span>
                    <span class="mm-meta-val" style="font-family:monospace;letter-spacing:.5px;font-size:13px">{{ $order->order_code }}</span>
                </div>
                <div class="mm-meta-row">
                    <span class="mm-meta-lbl">Người nhận</span>
                    <span class="mm-meta-val">{{ $order->recipient_name }}</span>
                </div>
                <div class="mm-meta-row">
                    <span class="mm-meta-lbl">Số tiền cần thanh toán</span>
                    <span class="mm-meta-val big">{{ number_format($amount, 0, ',', '.') }}đ</span>
                </div>

                {{-- Alert --}}
                <div class="mm-alert">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="12" y1="8" x2="12" y2="12"/>
                        <line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    <span>Sau khi thanh toán thành công, hệ thống sẽ tự động cập nhật trạng thái đơn hàng. Bạn không cần thao tác thủ công.</span>
                </div>
            </div>

            {{-- Steps --}}
            <div class="mm-steps">
                <p class="mm-steps-title">Hướng dẫn thanh toán</p>
                <div class="mm-step-list">
                    <div class="mm-step">
                        <span class="mm-step-num">1</span>
                        Dùng ứng dụng MoMo trên điện thoại để quét mã QR.
                    </div>
                    <div class="mm-step">
                        <span class="mm-step-num">2</span>
                        Nếu không quét được, nhấn “ Mở trang MoMo ”.
                    </div>
                    <div class="mm-step">
                        <span class="mm-step-num">3</span>
                        Xác nhận đúng <strong>số tiền</strong> trước khi thanh toán.
                    </div>
                    <div class="mm-step">
                        <span class="mm-step-num">4</span>
                        Hệ thống sẽ tự động cập nhật đơn hàng sau khi thanh toán thành công.
                    </div>
                </div>
            </div>
        </div>

        {{-- RIGHT: Action card --}}
        <aside class="mm-action-card">
            <div class="mm-action-head">
                <p class="mm-action-head-title">Thanh toán qua MoMo</p>
                <p class="mm-action-head-sub">Đơn {{ $order->order_code }}</p>
            </div>

            <div class="mm-action-body">

                {{-- MoMo QR code --}}
                <div style="display:flex;justify-content:center;padding:12px 0">
                    <img src="{{ $qrImageUrl }}" alt="MoMo QR" style="width:220px;height:240px;border-radius:12px;border:1px solid var(--cb-border);background:#fff;padding:8px" />
                </div>

                {{-- Amount --}}
                <div class="mm-amount">
                    <span class="mm-amount-val">{{ number_format($amount, 0, ',', '.') }}đ</span>
                    <p class="mm-amount-lbl">Số tiền thanh toán</p>
                </div>

                {{-- Countdown --}}
                <div class="mm-countdown">
                    <span class="mm-countdown-dot"></span>
                    Còn lại <span id="mqTimer" class="mm-countdown-time">15:00</span>
                </div>

                {{-- Alert --}}
                <div class="mm-action-alert">
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="12" y1="8" x2="12" y2="12"/>
                        <line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    <span>Mỗi giao dịch có một liên kết riêng. Trang sẽ tự chuyển về đơn hàng sau khi hết thời gian.</span>
                </div>

                <div class="mm-body-sep"></div>

                {{-- Buttons --}}
                <div class="mm-btn-group">
                    <a href="{{ $fallbackUrl }}" target="_blank" rel="noopener noreferrer" class="mm-btn-secondary" style="background:var(--mm-pink);color:#fff;border-color:var(--mm-pink)">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"/>
                            <polyline points="15 3 21 3 21 9"/>
                            <line x1="10" y1="14" x2="21" y2="3"/>
                        </svg>
                        Mở trang MoMo
                    </a>
                </div>

                <a href="{{ route('orders.show', $order) }}" class="mm-btn-outline">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/>
                        <rect x="9" y="3" width="6" height="4" rx="1"/>
                    </svg>
                    Xem chi tiết đơn hàng
                </a>

                <p class="mm-note">
                    Nếu app MoMo không mở, hãy dùng nút "Mở trang MoMo Web". Callback từ MoMo sẽ cập nhật trạng thái đơn trước khi hết giờ.
                </p>
            </div>
        </aside>
    </div>
</div>
</div>

{{-- ── Countdown script (logic giữ nguyên) ─────────────── --}}
<script>
(function () {
    const countdownEl = document.getElementById('mqTimer');
    if (!countdownEl) return;

    let remaining = 15 * 60;
    const redirectUrl = "{{ route('orders.show', $order) }}";

    const pad = (value) => String(value).padStart(2, '0');

    const render = () => {
        const minutes = Math.floor(remaining / 60);
        const seconds = remaining % 60;
        countdownEl.textContent = `${pad(minutes)}:${pad(seconds)}`;
    };

    render();

    const timer = window.setInterval(() => {
        remaining -= 1;
        if (remaining <= 0) {
            window.clearInterval(timer);
            window.location.href = redirectUrl;
            return;
        }
        render();
    }, 1000);
})();
</script>

@endsection