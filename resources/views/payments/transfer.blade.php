@extends('layouts.app')

@section('title', 'Chuyển khoản ngân hàng — ' . $order->order_code)

@section('content')
<style>
:root {
    --tp-bg: #f7f5ef;
    --tp-surface: rgba(255, 255, 255, 0.94);
    --tp-border: rgba(15, 23, 42, 0.10);
    --tp-text: #0f172a;
    --tp-muted: #64748b;
    --tp-accent: #2d6a4f;
    --tp-accent-dark: #1b4332;
    --tp-shadow: 0 24px 80px rgba(15, 23, 42, 0.14);
}

body {
    background:
        radial-gradient(circle at top left, rgba(45, 106, 79, 0.14), transparent 32%),
        radial-gradient(circle at right center, rgba(14, 165, 233, 0.08), transparent 28%),
        var(--tp-bg);
}

.tp-wrap {
    max-width: 1180px;
    margin: 0 auto;
    padding: 32px 16px 48px;
}

.tp-grid {
    display: grid;
    gap: 24px;
    grid-template-columns: 1.05fr 0.95fr;
    align-items: start;
}

.tp-panel,
.tp-card {
    background: var(--tp-surface);
    border: 1px solid var(--tp-border);
    border-radius: 28px;
    box-shadow: var(--tp-shadow);
    backdrop-filter: blur(18px);
}

.tp-panel {
    padding: 32px;
}

.tp-kicker {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 8px 14px;
    border-radius: 999px;
    background: rgba(45, 106, 79, 0.10);
    color: var(--tp-accent-dark);
    font-size: 12px;
    font-weight: 800;
    letter-spacing: .08em;
    text-transform: uppercase;
}

.tp-title {
    margin: 18px 0 12px;
    font-size: clamp(30px, 4vw, 54px);
    line-height: 1.02;
    letter-spacing: -0.05em;
    color: var(--tp-text);
    font-weight: 900;
}

.tp-desc {
    margin: 0;
    max-width: 62ch;
    color: var(--tp-muted);
    line-height: 1.8;
    font-size: 15px;
}

.tp-meta {
    display: grid;
    gap: 12px;
    grid-template-columns: repeat(3, minmax(0, 1fr));
    margin-top: 28px;
}

.tp-meta-item {
    padding: 14px 16px;
    border-radius: 18px;
    background: rgba(15, 23, 42, 0.03);
    border: 1px solid rgba(15, 23, 42, 0.06);
}

.tp-meta-label {
    display: block;
    color: var(--tp-muted);
    font-size: 12px;
    margin-bottom: 6px;
}

.tp-meta-value {
    color: var(--tp-text);
    font-size: 15px;
    font-weight: 800;
}

.tp-card {
    padding: 28px;
    display: flex;
    flex-direction: column;
    gap: 16px;
    align-items: center;
    text-align: center;
}

.tp-qr-shell {
    width: min(100%, 340px);
    padding: 18px;
    border-radius: 28px;
    background: #fff;
    border: 1px solid rgba(15, 23, 42, 0.08);
}

.tp-qr-shell img {
    display: block;
    width: 100%;
    height: auto;
    border-radius: 18px;
}

.tp-countdown {
    display: inline-flex;
    align-items: center;
    gap: 8px;
    padding: 10px 14px;
    border-radius: 999px;
    background: #fff7ed;
    color: #c2410c;
    border: 1px solid #fed7aa;
    font-size: 12px;
    font-weight: 700;
}

.tp-countdown-dot {
    width: 7px;
    height: 7px;
    border-radius: 50%;
    background: #f97316;
    animation: tpPulse 1.2s ease-in-out infinite;
}

@keyframes tpPulse {
    0%, 100% { transform: scale(1); opacity: 1; }
    50% { transform: scale(1.4); opacity: .5; }
}

.tp-bankbox {
    width: 100%;
    text-align: left;
    padding: 16px;
    border-radius: 18px;
    background: rgba(45, 106, 79, 0.08);
    border: 1px solid rgba(45, 106, 79, 0.12);
}

.tp-bankbox-title {
    margin: 0 0 12px;
    font-size: 13px;
    font-weight: 800;
    color: var(--tp-accent-dark);
    text-transform: uppercase;
    letter-spacing: .08em;
}

.tp-bankrow {
    display: flex;
    justify-content: space-between;
    gap: 12px;
    padding: 9px 0;
    border-top: 1px dashed rgba(45, 106, 79, 0.16);
    font-size: 13px;
}

.tp-bankrow:first-of-type {
    border-top: 0;
    padding-top: 0;
}

.tp-banklabel {
    color: var(--tp-muted);
}

.tp-bankvalue {
    color: var(--tp-text);
    font-weight: 800;
    text-align: right;
}

.tp-alert {
    width: 100%;
    padding: 14px 16px;
    border-radius: 18px;
    background: rgba(45, 106, 79, 0.08);
    color: var(--tp-accent-dark);
    border: 1px solid rgba(45, 106, 79, 0.12);
    font-size: 13px;
    line-height: 1.7;
}

.tp-btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    gap: 10px;
    width: 100%;
    padding: 13px 20px;
    border-radius: 999px;
    font-weight: 800;
    text-decoration: none;
    transition: transform .18s ease, background .18s ease, border-color .18s ease;
}

.tp-btn-primary {
    background: var(--tp-accent);
    color: #fff;
}

.tp-btn-primary:hover {
    background: var(--tp-accent-dark);
    transform: translateY(-1px);
}

.tp-btn-secondary {
    background: #fff;
    color: var(--tp-text);
    border: 1px solid rgba(15, 23, 42, 0.12);
}

.tp-btn-secondary:hover {
    border-color: rgba(45, 106, 79, 0.35);
    transform: translateY(-1px);
}

.tp-btn-group {
    display: flex;
    flex-direction: column;
    gap: 10px;
    width: 100%;
}

.tp-note {
    margin: 0;
    font-size: 12px;
    line-height: 1.65;
    color: var(--tp-muted);
}

@media (max-width: 900px) {
    .tp-grid {
        grid-template-columns: 1fr;
    }

    .tp-meta {
        grid-template-columns: 1fr;
    }
}
</style>

<div class="tp-wrap">
    <div class="tp-grid">
        <section class="tp-panel">
            <span class="tp-kicker">Chuyển khoản ngân hàng</span>
            <h1 class="tp-title">Thanh toán đơn hàng bằng chuyển khoản</h1>
            <p class="tp-desc">
                Quét mã hoặc chuyển khoản theo đúng nội dung đơn hàng để hệ thống dễ dàng đối soát. Sau khi chuyển khoản thành công, đơn hàng sẽ được ghi nhận và chờ xác nhận.
            </p>

            <div class="tp-meta">
                <div class="tp-meta-item">
                    <span class="tp-meta-label">Mã đơn</span>
                    <span class="tp-meta-value">{{ $order->order_code }}</span>
                </div>
                <div class="tp-meta-item">
                    <span class="tp-meta-label">Số tiền</span>
                    <span class="tp-meta-value">{{ number_format($amount, 0, ',', '.') }}đ</span>
                </div>
                <div class="tp-meta-item">
                    <span class="tp-meta-label">Người nhận</span>
                    <span class="tp-meta-value">{{ $order->recipient_name }}</span>
                </div>
            </div>

            <div style="margin-top:24px" class="tp-alert">
                Nội dung chuyển khoản cần đúng mã đơn để shop đối soát nhanh. Nếu hệ thống chưa xác nhận ngay, vui lòng chờ kiểm tra giao dịch.
            </div>
        </section>

        <aside class="tp-card">
            <div class="tp-countdown">
                <span class="tp-countdown-dot"></span>
                Còn lại <span id="tpTimer">15:00</span>
            </div>

            <div class="tp-qr-shell">
                <img src="{{ $qrImageUrl }}" alt="QR chuyển khoản cho đơn {{ $order->order_code }}">
            </div>

            <div class="tp-bankbox">
                <div class="tp-bankbox-title">Thông tin chuyển khoản</div>
                <div class="tp-bankrow">
                    <span class="tp-banklabel">Ngân hàng</span>
                    <span class="tp-bankvalue">{{ $bankName }}</span>
                </div>
                <div class="tp-bankrow">
                    <span class="tp-banklabel">Số tài khoản</span>
                    <span class="tp-bankvalue">{{ $accountNumber }}</span>
                </div>
                <div class="tp-bankrow">
                    <span class="tp-banklabel">Chủ tài khoản</span>
                    <span class="tp-bankvalue">{{ $accountName }}</span>
                </div>
                <div class="tp-bankrow">
                    <span class="tp-banklabel">Nội dung</span>
                    <span class="tp-bankvalue">{{ $transferContent }}</span>
                </div>
            </div>

            <div class="tp-btn-group">
                <a href="{{ route('orders.show', $order) }}" class="tp-btn tp-btn-primary">Tôi đã thanh toán</a>
                <a href="{{ route('orders.show', $order) }}" class="tp-btn tp-btn-secondary">Xem đơn hàng</a>
            </div>

            <p class="tp-note">
                Trang này sẽ tự động chuyển về đơn hàng sau 15 phút. Nếu bạn đã chuyển khoản, hãy giữ lại biên lai để đối soát khi cần.
            </p>
        </aside>
    </div>
</div>

<script>
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