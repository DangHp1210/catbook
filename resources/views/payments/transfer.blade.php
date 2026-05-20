@extends('layouts.app')

@section('title', 'Chuyển khoản ngân hàng — ' . $order->order_code)

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
/* ─── Wrapper ─────────────────────────────────────────── */
.tp-wrap { max-width: 960px; margin: 0 auto; padding: 40px 24px 64px; }

/* ─── Breadcrumb ──────────────────────────────────────── */
.tp-crumb {
    display: flex; align-items: center; gap: 6px; flex-wrap: wrap;
    font-family: var(--cb-sans); font-size: 12px; color: var(--cb-muted);
    margin-bottom: 28px;
}
.tp-crumb a { color: var(--cb-muted); text-decoration: none; transition: color .15s; }
.tp-crumb a:hover { color: var(--cb-accent); }
.tp-crumb-sep { opacity: .4; }

/* ─── Page heading ────────────────────────────────────── */
.tp-header { margin-bottom: 28px; }
.tp-eyebrow {
    display: inline-flex; align-items: center; gap: 7px;
    font-family: var(--cb-sans); font-size: 11px; font-weight: 700;
    letter-spacing: 1.8px; text-transform: uppercase;
    color: var(--cb-accent); background: var(--cb-accent-light);
    padding: 4px 13px; border-radius: 999px; margin-bottom: 12px;
}
.tp-eyebrow-dot {
    width: 6px; height: 6px; border-radius: 50%;
    background: var(--cb-accent);
    animation: tp-pulse 2s ease-in-out infinite;
}
@keyframes tp-pulse {
    0%,100%{ opacity:1; transform:scale(1); }
    50%{ opacity:.5; transform:scale(1.5); }
}
.tp-heading {
    font-family: var(--cb-serif); font-size: 36px; font-weight: 900;
    color: #0d1b10; letter-spacing: -1px; line-height: 1.08; margin: 0;
}

/* ─── 2-col grid ──────────────────────────────────────── */
.tp-grid {
    display: grid;
    grid-template-columns: 1fr 340px;
    gap: 24px; align-items: start;
}
@media (max-width: 860px) { .tp-grid { grid-template-columns: 1fr; } }

/* ─── Shared card ─────────────────────────────────────── */
.tp-card {
    background: var(--cb-white); border: 1px solid var(--cb-border);
    border-radius: 18px; overflow: hidden;
}
.tp-card-head {
    padding: 18px 24px; border-bottom: 1px solid var(--cb-border);
    position: relative;
}
.tp-card-head::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
    background: linear-gradient(90deg, var(--cb-accent), #4ade80);
}
.tp-card-title {
    font-family: var(--cb-serif); font-size: 18px; font-weight: 700;
    color: var(--cb-text); margin: 0;
}
.tp-card-sub {
    font-family: var(--cb-sans); font-size: 11px; color: var(--cb-muted); margin-top: 3px;
}

/* ─── Left: info body ─────────────────────────────────── */
.tp-info-body { padding: 18px 24px; display: flex; flex-direction: column; gap: 12px; }

.tp-meta-row {
    display: flex; align-items: center; justify-content: space-between;
    gap: 12px; font-family: var(--cb-sans); font-size: 13px;
    padding: 10px 14px; border-radius: 10px;
    background: var(--cb-bg); border: 1px solid var(--cb-border);
}
.tp-lbl { color: var(--cb-muted); font-weight: 500; }
.tp-val { color: var(--cb-text); font-weight: 700; }
.tp-val.big {
    font-family: var(--cb-serif); font-size: 22px;
    color: var(--cb-accent); letter-spacing: -.3px;
}

.tp-alert {
    display: flex; align-items: flex-start; gap: 10px;
    padding: 12px 14px; border-radius: 10px;
    background: var(--cb-accent-light); color: var(--cb-accent-dark);
    border: 1px solid #86efac;
    font-family: var(--cb-sans); font-size: 13px; line-height: 1.6;
}
.tp-alert svg { flex-shrink: 0; margin-top: 1px; }

/* Steps */
.tp-steps { padding: 18px 24px; border-top: 1px solid var(--cb-border); }
.tp-steps-lbl {
    font-family: var(--cb-sans); font-size: 11px; font-weight: 700;
    letter-spacing: 1.2px; text-transform: uppercase; color: #b0a898; margin-bottom: 14px;
}
.tp-step-list { display: flex; flex-direction: column; gap: 10px; }
.tp-step {
    display: flex; align-items: flex-start; gap: 12px;
    font-family: var(--cb-sans); font-size: 13px; color: var(--cb-muted); line-height: 1.5;
}
.tp-step-num {
    width: 22px; height: 22px; border-radius: 50%; flex-shrink: 0;
    background: var(--cb-accent-light); color: var(--cb-accent);
    font-size: 11px; font-weight: 700;
    display: flex; align-items: center; justify-content: center; margin-top: 1px;
}

/* ─── Right: action card ──────────────────────────────── */
.tp-action { position: sticky; top: 84px; }

.tp-action-body {
    padding: 20px; display: flex; flex-direction: column;
    align-items: center; gap: 16px;
}

/* Countdown */
.tp-countdown {
    display: flex; align-items: center; gap: 7px;
    font-family: var(--cb-sans); font-size: 13px; font-weight: 700;
    padding: 9px 18px; border-radius: 999px;
    background: #fff7ed; color: #c2410c; border: 1px solid #fed7aa;
}
.tp-countdown-dot {
    width: 6px; height: 6px; border-radius: 50%; background: #f97316;
    animation: tp-pulse 1.2s ease-in-out infinite;
}

/* QR */
.tp-qr-shell {
    width: 100%; max-width: 240px;
    padding: 14px; border-radius: 14px;
    background: var(--cb-white); border: 2px solid var(--cb-border);
    box-shadow: 0 4px 18px rgba(0,0,0,.07);
}
.tp-qr-shell img { display: block; width: 100%; height: auto; border-radius: 6px; }

/* Amount */
.tp-amount { text-align: center; }
.tp-amount-val {
    display: block; font-family: var(--cb-serif); font-size: 30px; font-weight: 900;
    color: var(--cb-accent); letter-spacing: -.5px;
}
.tp-amount-lbl {
    font-family: var(--cb-sans); font-size: 12px; color: var(--cb-muted); margin-top: 2px;
}

/* Bank info box */
.tp-bankbox {
    width: 100%; border-radius: 12px;
    border: 1.5px solid var(--cb-border);
    overflow: hidden;
}
.tp-bankbox-head {
    padding: 10px 14px; background: var(--cb-accent-light);
    font-family: var(--cb-sans); font-size: 11px; font-weight: 700;
    letter-spacing: 1.1px; text-transform: uppercase; color: var(--cb-accent);
    border-bottom: 1px solid #86efac;
}
.tp-bankrow {
    display: flex; align-items: flex-start; justify-content: space-between;
    gap: 12px; padding: 10px 14px;
    border-bottom: 1px solid var(--cb-border);
    font-family: var(--cb-sans); font-size: 12px;
}
.tp-bankrow:last-child { border-bottom: none; }
.tp-banklbl { color: var(--cb-muted); font-weight: 500; flex-shrink: 0; }
.tp-bankval {
    color: var(--cb-text); font-weight: 700; text-align: right;
    word-break: break-all;
}
/* Transfer content highlight */
.tp-bankval.content {
    background: #fffbeb; color: #92400e;
    padding: 2px 8px; border-radius: 6px;
    border: 1px solid #fde68a; font-family: monospace;
    font-size: 11px; letter-spacing: .3px;
}

/* Copy button */
.tp-copy-btn {
    font-family: var(--cb-sans); font-size: 11px; font-weight: 600;
    padding: 5px 10px; border-radius: 6px;
    border: 1.5px solid var(--cb-border); background: var(--cb-white);
    color: var(--cb-muted); cursor: pointer; transition: all .18s;
    white-space: nowrap; flex-shrink: 0;
}
.tp-copy-btn:hover { border-color: var(--cb-accent); color: var(--cb-accent); }
.tp-copy-btn.copied { border-color: #86efac; color: var(--cb-accent); background: var(--cb-accent-light); }

/* Action alert */
.tp-action-alert {
    width: 100%; display: flex; align-items: flex-start; gap: 8px;
    padding: 11px 13px; border-radius: 10px;
    background: var(--cb-accent-light); color: var(--cb-accent-dark);
    border: 1px solid #86efac;
    font-family: var(--cb-sans); font-size: 12px; line-height: 1.6; text-align: left;
}
.tp-action-alert svg { flex-shrink: 0; margin-top: 1px; }

/* Separator */
.tp-sep { width: 100%; height: 1px; background: var(--cb-border); }

/* Buttons */
.tp-btn-group { display: flex; flex-direction: column; gap: 8px; width: 100%; }

.tp-btn-primary {
    display: flex; align-items: center; justify-content: center; gap: 8px;
    width: 100%; font-family: var(--cb-sans); font-size: 14px; font-weight: 700;
    padding: 13px 20px; border-radius: 12px; border: none;
    background: var(--cb-accent); color: #fff; text-decoration: none;
    cursor: pointer; transition: background .2s, transform .15s;
}
.tp-btn-primary:hover { background: var(--cb-accent-dark); transform: translateY(-1px); }

.tp-btn-secondary {
    display: flex; align-items: center; justify-content: center; gap: 8px;
    width: 100%; font-family: var(--cb-sans); font-size: 13px; font-weight: 500;
    padding: 11px 20px; border-radius: 12px;
    border: 1.5px solid var(--cb-border); background: var(--cb-white);
    color: var(--cb-muted); text-decoration: none; transition: border-color .2s, color .2s;
}
.tp-btn-secondary:hover { border-color: var(--cb-accent); color: var(--cb-accent); }

/* Note */
.tp-note {
    font-family: var(--cb-sans); font-size: 11px; color: #b0a898;
    text-align: center; line-height: 1.6;
}
</style>

<div class="tp-wrap">

    {{-- Breadcrumb --}}
    <nav class="tp-crumb">
        <a href="{{ route('home') }}">Trang chủ</a>
        <span class="tp-crumb-sep">/</span>
        <a href="{{ route('orders.index') }}">Đơn hàng</a>
        <span class="tp-crumb-sep">/</span>
        <a href="{{ route('orders.show', $order) }}">{{ $order->order_code }}</a>
        <span class="tp-crumb-sep">/</span>
        <span style="color:var(--cb-text);font-weight:500">Chuyển khoản ngân hàng</span>
    </nav>

    {{-- Page header --}}
    <div class="tp-header">
        <div class="tp-eyebrow">
            <span class="tp-eyebrow-dot"></span>
            Chuyển khoản ngân hàng
        </div>
        <h1 class="tp-heading">Thanh toán bằng chuyển khoản</h1>
    </div>

    {{-- Main grid --}}
    <div class="tp-grid">

        {{-- ══ LEFT: Info + Steps ══════════════════════════ --}}
        <div class="tp-card">
            <div class="tp-card-head">
                <h2 class="tp-card-title">Thông tin đơn hàng</h2>
            </div>

            <div class="tp-info-body">
                <div class="tp-meta-row">
                    <span class="tp-lbl">Mã đơn hàng</span>
                    <span class="tp-val" style="font-family:monospace;letter-spacing:.5px;font-size:13px">
                        {{ $order->order_code }}
                    </span>
                </div>
                <div class="tp-meta-row">
                    <span class="tp-lbl">Người nhận</span>
                    <span class="tp-val">{{ $order->recipient_name }}</span>
                </div>
                <div class="tp-meta-row">
                    <span class="tp-lbl">Số tiền cần chuyển</span>
                    <span class="tp-val big">{{ number_format($amount, 0, ',', '.') }}đ</span>
                </div>

                <div class="tp-alert">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="12" y1="8" x2="12" y2="12"/>
                        <line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    <span>Nội dung chuyển khoản cần đúng mã đơn để shop đối soát nhanh. Nếu hệ thống chưa xác nhận ngay, vui lòng chờ kiểm tra giao dịch.</span>
                </div>
            </div>

            <div class="tp-steps">
                <p class="tp-steps-lbl">Hướng dẫn chuyển khoản</p>
                <div class="tp-step-list">
                    <div class="tp-step">
                        <span class="tp-step-num">1</span>
                        <span>Quét mã QR bên phải bằng app ngân hàng, hoặc chuyển khoản theo thông tin tài khoản.</span>
                    </div>
                    <div class="tp-step">
                        <span class="tp-step-num">2</span>
                        <span>Nhập đúng <strong>số tiền</strong> và <strong>nội dung chuyển khoản</strong> hiển thị bên phải.</span>
                    </div>
                    <div class="tp-step">
                        <span class="tp-step-num">3</span>
                        <span>Xác nhận giao dịch. Giữ lại biên lai để đối soát nếu cần.</span>
                    </div>
                    <div class="tp-step">
                        <span class="tp-step-num">4</span>
                        <span>Nhấn <strong>"Tôi đã chuyển khoản"</strong> — đơn hàng sẽ chờ xác nhận từ shop.</span>
                    </div>
                </div>
            </div>
        </div>

        {{-- ══ RIGHT: Action card ═══════════════════════════ --}}
        <aside class="tp-card tp-action">
            <div class="tp-card-head" style="text-align:center">
                <p class="tp-card-title" style="font-size:15px">Thông tin chuyển khoản</p>
                <p class="tp-card-sub">Đơn {{ $order->order_code }}</p>
            </div>

            <div class="tp-action-body">

                {{-- Countdown --}}
                <div class="tp-countdown">
                    <span class="tp-countdown-dot"></span>
                    Còn lại <span id="tpTimer" style="font-variant-numeric:tabular-nums;letter-spacing:.5px">15:00</span>
                </div>

                {{-- QR --}}
                <div class="tp-qr-shell">
                    <img src="{{ $qrImageUrl;  }}"
                         alt="QR chuyển khoản cho đơn {{ $order->order_code }}">
                </div>

                {{-- Amount --}}
                <div class="tp-amount">
                    <span class="tp-amount-val">{{ number_format($amount, 0, ',', '.') }}đ</span>
                    <p class="tp-amount-lbl">Số tiền cần chuyển</p>
                </div>

                {{-- Bank info table --}}
                <div class="tp-bankbox">
                    <div class="tp-bankbox-head">Tài khoản nhận</div>
                    <div class="tp-bankrow">
                        <span class="tp-banklbl">Ngân hàng</span>
                        <span class="tp-bankval">{{ $bankName }}</span>
                    </div>
                    <div class="tp-bankrow">
                        <span class="tp-banklbl">Số tài khoản</span>
                        <div style="display:flex;align-items:center;gap:6px">
                            <span class="tp-bankval" id="tp-acc-num">{{ $accountNumber }}</span>
                            <button type="button" class="tp-copy-btn" onclick="tpCopy('tp-acc-num', this)">Sao chép</button>
                        </div>
                    </div>
                    <div class="tp-bankrow">
                        <span class="tp-banklbl">Chủ tài khoản</span>
                        <span class="tp-bankval">{{ $accountName }}</span>
                    </div>
                    <div class="tp-bankrow">
                        <span class="tp-banklbl">Nội dung</span>
                        <div style="display:flex;align-items:center;gap:6px;flex-wrap:wrap;justify-content:flex-end">
                            <span class="tp-bankval content" id="tp-content">{{ $transferContent }}</span>
                            <button type="button" class="tp-copy-btn" onclick="tpCopy('tp-content', this)">Sao chép</button>
                        </div>
                    </div>
                </div>

                {{-- Alert --}}
                <div class="tp-action-alert">
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="12" cy="12" r="10"/>
                        <line x1="12" y1="8" x2="12" y2="12"/>
                        <line x1="12" y1="16" x2="12.01" y2="16"/>
                    </svg>
                    <span>Nhập đúng nội dung chuyển khoản để shop đối soát tự động. Trang tự chuyển về đơn hàng sau 15 phút.</span>
                </div>

                <div class="tp-sep"></div>

                {{-- Buttons --}}
                <div class="tp-btn-group">
                    <a href="{{ route('orders.show', $order) }}" class="tp-btn-primary">
                        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        Tôi đã thanh toán
                    </a>
                    <a href="{{ route('orders.show', $order) }}" class="tp-btn-secondary">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2"/>
                            <rect x="9" y="3" width="6" height="4" rx="1"/>
                        </svg>
                        Xem đơn hàng
                    </a>
                </div>

                <p class="tp-note">
                    Đơn hàng sẽ được xác nhận sau khi shop kiểm tra.
                </p>

            </div>{{-- /.tp-action-body --}}
        </aside>

    </div>{{-- /.tp-grid --}}
</div>{{-- /.tp-wrap --}}

<script>
/* ── Copy to clipboard ────────────────────────────────── */
function tpCopy(elId, btn) {
    const el = document.getElementById(elId);
    if (!el) return;
    navigator.clipboard.writeText(el.textContent.trim()).then(() => {
        const orig = btn.textContent;
        btn.textContent = '✓ Đã chép';
        btn.classList.add('copied');
        setTimeout(() => { btn.textContent = orig; btn.classList.remove('copied'); }, 2000);
    }).catch(() => {});
}

/* ── Countdown (logic giữ nguyên) ────────────────────── */
(function () {
    const countdownEl = document.getElementById('tpTimer');
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