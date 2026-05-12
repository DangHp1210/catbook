@extends('layouts.app')

@section('title', 'Chi tiết đơn hàng ' . $order->order_code)

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
.os-header {
    display: flex; align-items: flex-end; justify-content: space-between;
    gap: 16px; flex-wrap: wrap; margin-bottom: 28px;
    max-width: 1300px;
    margin-left: auto;
    margin-right: auto;
}
.os-header-eyebrow {
    font-family: var(--cb-sans); font-size: 11px; font-weight: 600;
    letter-spacing: 1.8px; text-transform: uppercase; color: var(--cb-muted);
    margin-bottom: 6px;
}
.os-heading {
    font-family: var(--cb-serif);
    font-size: 32px; font-weight: 900; color: #0d1b10;
    letter-spacing: -.8px; line-height: 1.08; margin: 0;
}
.os-back-link {
    font-family: var(--cb-sans); font-size: 13px; font-weight: 500;
    color: var(--cb-accent); text-decoration: none;
    display: inline-flex; align-items: center; gap: 6px;
    border-bottom: 1px solid var(--cb-accent); padding-bottom: 1px;
    transition: opacity .18s; flex-shrink: 0;
}
.os-back-link:hover { opacity: .65; }

/* ─── Flash messages ──────────────────────────────────── */
.os-flash {
    display: flex; align-items: flex-start; gap: 10px;
    padding: 13px 18px; border-radius: 12px; border: 1px solid;
    font-family: var(--cb-sans); font-size: 13px; margin-bottom: 20px;
}
.os-flash svg { flex-shrink: 0; margin-top: 1px; }
.os-flash-ok  { background: #f0fdf4; border-color: #bbf7d0; color: #166534; }
.os-flash-err { background: #fff1f2; border-color: #fecdd3; color: #9f1239; }

/* ─── Main layout ─────────────────────────────────────── */
.os-layout {
    display: grid;
    grid-template-columns: 1fr 320px;
    gap: 24px; align-items: start;
    max-width: 1300px;
    margin: 0 auto 16px;
}
@media (max-width: 900px) { .os-layout { grid-template-columns: 1fr; } }

/* ─── Shared card ─────────────────────────────────────── */
.os-card {
    background: var(--cb-white); border: 1px solid var(--cb-border);
    border-radius: 18px; overflow: hidden;
}
.os-card-head {
    padding: 18px 22px 14px; border-bottom: 1px solid var(--cb-border);
    position: relative;
}
.os-card-head::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
    background: linear-gradient(90deg, var(--cb-accent), #4ade80);
}
.os-card-title {
    font-family: var(--cb-serif); font-size: 18px; font-weight: 700;
    color: var(--cb-text); margin: 0;
}
.os-card-body { padding: 18px 22px; }

/* ─── Order item card ─────────────────────────────────── */
.os-items { display: flex; flex-direction: column; gap: 12px; }

.os-item {
    background: var(--cb-bg); border: 1px solid var(--cb-border);
    border-radius: 14px; padding: 16px 18px;
}
.os-item-top {
    display: flex; align-items: flex-start; justify-content: space-between;
    gap: 16px; flex-wrap: wrap; margin-bottom: 12px;
}
.os-item-title {
    font-family: var(--cb-sans); font-size: 14px; font-weight: 600;
    color: var(--cb-text); line-height: 1.4; flex: 1;
}
.os-item-book-link {
    font-family: var(--cb-sans); font-size: 12px; font-weight: 500;
    color: var(--cb-accent); text-decoration: none; display: inline-flex;
    align-items: center; gap: 4px; margin-top: 5px;
    border-bottom: 1px solid transparent; transition: border-color .15s;
}
.os-item-book-link:hover { border-color: var(--cb-accent); }

.os-can-review-badge {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: 11px; font-weight: 600; padding: 3px 10px; border-radius: 999px;
    background: var(--cb-accent-light); color: var(--cb-accent); white-space: nowrap;
    flex-shrink: 0;
}

.os-item-prices {
    display: flex; align-items: center; justify-content: space-between;
    font-family: var(--cb-sans); font-size: 13px; color: var(--cb-muted);
    padding-top: 10px; border-top: 1px solid var(--cb-border);
}
.os-item-total { font-size: 15px; font-weight: 700; color: var(--cb-accent); }

/* Review section inside item */
.os-review-section {
    margin-top: 12px; padding: 14px 16px;
    background: var(--cb-white); border: 1px solid var(--cb-border);
    border-radius: 10px;
}
.os-reviewed-badge {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: 11px; font-weight: 600; padding: 3px 10px; border-radius: 999px;
    background: #fef9ee; color: #92400e; border: 1px solid #fde68a;
    margin-bottom: 10px;
}
.os-btn-review {
    font-family: var(--cb-sans); font-size: 13px; font-weight: 600;
    padding: 9px 20px; border-radius: 9px; border: none;
    background: var(--cb-text); color: #fff; cursor: pointer;
    display: inline-flex; align-items: center; gap: 7px;
    transition: background .2s;
}
.os-btn-review:hover { background: var(--cb-accent); }

/* Cancel order button */
.os-cancel-wrap { padding: 18px 22px; border-top: 1px solid var(--cb-border); }
.os-btn-cancel {
    font-family: var(--cb-sans); font-size: 13px; font-weight: 600;
    padding: 10px 22px; border-radius: 10px; border: none;
    background: #fff1f2; color: #dc2626;
    border: 1.5px solid #fecdd3;
    cursor: pointer; display: inline-flex; align-items: center; gap: 7px;
    transition: background .2s;
}
.os-btn-cancel:hover { background: #fee2e2; }

/* ─── Summary aside ───────────────────────────────────── */
.os-summary { position: sticky; top: 84px; }

.os-summary-head {
    padding: 18px 22px 14px; border-bottom: 1px solid var(--cb-border);
    position: relative;
}
.os-summary-head::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
    background: linear-gradient(90deg, #f59e0b, var(--cb-accent));
}
.os-summary-title {
    font-family: var(--cb-serif); font-size: 18px; font-weight: 700; color: var(--cb-text);
}

.os-info-rows { padding: 16px 22px; display: flex; flex-direction: column; gap: 10px; }
.os-info-row {
    display: flex; align-items: flex-start; justify-content: space-between;
    gap: 12px; font-family: var(--cb-sans); font-size: 13px;
}
.os-info-label { color: var(--cb-muted); flex-shrink: 0; }
.os-info-val   { color: var(--cb-text); font-weight: 600; text-align: right; }
.os-info-divider { height: 1px; background: var(--cb-border); margin: 4px 0; }

/* Order status badge inside summary */
.os-status-badge {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: 11px; font-weight: 700; padding: 4px 12px; border-radius: 999px; border: 1px solid;
}
.os-sb-pending   { background: #fffbeb; color: #d97706; border-color: #fde68a; }
.os-sb-confirmed { background: #e0eaff; color: #1e3a8a; border-color: #bfdbfe; }
.os-sb-shipping  { background: #e0f2fe; color: #0284c7; border-color: #bae6fd; }
.os-sb-completed { background: var(--cb-accent-light); color: var(--cb-accent); border-color: #86efac; }
.os-sb-cancelled { background: #fff1f2; color: #dc2626; border-color: #fecdd3; }

/* Total rows */
.os-totals { padding: 14px 22px 20px; border-top: 1px solid var(--cb-border); display: flex; flex-direction: column; gap: 8px; }
.os-total-row {
    display: flex; align-items: center; justify-content: space-between;
    font-family: var(--cb-sans); font-size: 13px; color: var(--cb-muted);
}
.os-total-row strong { color: var(--cb-text); font-weight: 600; }
.os-grand-row {
    display: flex; align-items: center; justify-content: space-between;
    font-family: var(--cb-sans); padding-top: 10px; border-top: 1px solid var(--cb-border);
    margin-top: 4px;
}
.os-grand-lbl { font-size: 14px; font-weight: 600; color: var(--cb-text); }
.os-grand-val {
    font-family: var(--cb-serif); font-size: 26px; font-weight: 900;
    color: var(--cb-accent); letter-spacing: -.5px; line-height: 1;
}

/* Payment method tag */
.os-pay-method {
    display: inline-flex; align-items: center;
    font-size: 11px; font-weight: 700; padding: 2px 9px; border-radius: 999px;
    background: var(--cb-bg); border: 1px solid var(--cb-border); color: var(--cb-muted);
    letter-spacing: .4px;
}

/* ─── Review modal ────────────────────────────────────── */
.os-modal-wrap {
    position: fixed; inset: 0; z-index: 50;
    display: none;
    align-items: center; justify-content: center; padding: 16px;
    background: rgba(13,27,16,.52);
    backdrop-filter: blur(3px);
}
.os-modal-wrap.is-open { display: flex; }

.os-modal {
    background: var(--cb-white); border-radius: 20px;
    width: 100%; max-width: 480px;
    box-shadow: 0 24px 60px rgba(0,0,0,.16);
    overflow: hidden; position: relative;
}
.os-modal-head {
    padding: 20px 26px 16px;
    border-bottom: 1px solid var(--cb-border);
    display: flex; align-items: flex-start; justify-content: space-between; gap: 12px;
    position: relative;
}
.os-modal-head::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
    background: linear-gradient(90deg, #f59e0b, var(--cb-accent));
}
.os-modal-title { font-family: var(--cb-serif); font-size: 20px; font-weight: 700; color: var(--cb-text); margin: 0; }
.os-modal-close {
    width: 30px; height: 30px; border-radius: 8px;
    border: 1.5px solid var(--cb-border); background: transparent;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer; color: var(--cb-muted); transition: all .18s; flex-shrink: 0;
}
.os-modal-close:hover { border-color: var(--cb-text); color: var(--cb-text); }
.os-modal-body { padding: 20px 26px; display: flex; flex-direction: column; gap: 16px; }
.os-modal-foot {
    padding: 0 26px 22px;
    display: flex; justify-content: flex-end; gap: 10px;
}

/* Star picker */
.os-star-picker { display: flex; gap: 8px; }
.os-star-pick {
    font-size: 28px; cursor: pointer; color: #e0dbd0;
    transition: color .15s; line-height: 1;
}
.os-star-pick.on { color: #f59e0b; }

.os-modal-field { display: flex; flex-direction: column; gap: 6px; }
.os-modal-label {
    font-family: var(--cb-sans); font-size: 12px; font-weight: 600; color: var(--cb-text);
}
.os-modal-select,
.os-modal-textarea {
    font-family: var(--cb-sans); font-size: 13px;
    padding: 10px 14px; border: 1.5px solid var(--cb-border);
    border-radius: 9px; background: var(--cb-white); color: var(--cb-text);
    outline: none; transition: border-color .2s, box-shadow .2s;
    width: 100%; box-sizing: border-box;
}
.os-modal-select:focus,
.os-modal-textarea:focus {
    border-color: var(--cb-accent);
    box-shadow: 0 0 0 3px rgba(45,106,79,.09);
}
.os-modal-textarea::placeholder { color: #c0b8b0; }
.os-modal-textarea { resize: vertical; min-height: 100px; }

.os-modal-submit {
    font-family: var(--cb-sans); font-size: 13px; font-weight: 600;
    padding: 10px 22px; border-radius: 9px; border: none;
    background: var(--cb-text); color: #fff; cursor: pointer;
    transition: background .2s; display: inline-flex; align-items: center; gap: 7px;
}
.os-modal-submit:hover { background: var(--cb-accent); }
.os-modal-cancel {
    font-family: var(--cb-sans); font-size: 13px; font-weight: 500;
    padding: 10px 18px; border-radius: 9px;
    border: 1.5px solid var(--cb-border); background: transparent;
    color: var(--cb-muted); cursor: pointer; transition: all .18s;
}
.os-modal-cancel:hover { border-color: var(--cb-text); color: var(--cb-text); }
</style>

{{-- ── Flash messages ───────────────────────────────────── --}}
@if(session('success'))
    <div class="os-flash os-flash-ok">
        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
            <polyline points="20 6 9 17 4 12"/>
        </svg>
        {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div class="os-flash os-flash-err">
        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>
        </svg>
        {{ session('error') }}
    </div>
@endif

{{-- ── Page header ──────────────────────────────────────── --}}
<div class="os-header">
    <div>
        <p class="os-header-eyebrow">Chi tiết đơn hàng</p>
        <h1 class="os-heading">{{ $order->order_code }}</h1>
    </div>
    <a href="{{ route('orders.index') }}" class="os-back-link">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M19 12H5M12 19l-7-7 7-7"/>
        </svg>
        Lịch sử đơn hàng
    </a>
</div>

{{-- ── Main layout ───────────────────────────────────────── --}}
<div class="os-layout">

    {{-- LEFT: Products ──────────────────────────────────── --}}
    <div class="os-card">
        <div class="os-card-head">
            <h2 class="os-card-title">Sản phẩm trong đơn</h2>
        </div>
        <div class="os-card-body">
            <div class="os-items">
                @foreach($order->items as $item)
                    @php
                        $book          = $item->book;
                        $existingReview = $reviewsByBookId[$item->book_id] ?? null;
                    @endphp
                    <div class="os-item">
                        <div class="os-item-top">
                            <div>
                                <p class="os-item-title">{{ $item->book_title_snapshot }}</p>
                                @if($book)
                                    <a href="{{ route('catalog.book', $book->slug) }}" class="os-item-book-link">
                                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path d="M18 13v6a2 2 0 01-2 2H5a2 2 0 01-2-2V8a2 2 0 012-2h6"/>
                                            <polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/>
                                        </svg>
                                        Xem sách
                                    </a>
                                @endif
                            </div>
                            @if($order->order_status === 'completed')
                                <span class="os-can-review-badge">
                                    <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                                    Có thể đánh giá
                                </span>
                            @endif
                        </div>

                        <div class="os-item-prices">
                            <div style="display:flex;gap:20px">
                                <span>
                                    Đơn giá: <strong style="color:var(--cb-text)">{{ number_format((float)$item->unit_price, 0, ',', '.') }}đ</strong>
                                </span>
                                <span>
                                    Số lượng: <strong style="color:var(--cb-text)">{{ $item->quantity }}</strong>
                                </span>
                            </div>
                            <span class="os-item-total">{{ number_format((float)$item->total_price, 0, ',', '.') }}đ</span>
                        </div>

                        {{-- Review section --}}
                        @if($order->order_status === 'completed' && $book)
                            <div class="os-review-section">
                                @if($existingReview)
                                    <div>
                                        <span class="os-reviewed-badge">
                                            ★ Đã đánh giá {{ $existingReview->rating }}/5 sao
                                        </span>
                                    </div>
                                @endif
                                <button type="button"
                                        class="os-btn-review open-review-modal"
                                        data-book-slug="{{ $book->slug }}"
                                        data-book-title="{{ $item->book_title_snapshot }}"
                                        data-existing-rating="{{ $existingReview->rating ?? '' }}"
                                        data-existing-comment="{{ e($existingReview->comment ?? '') }}">
                                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>
                                    </svg>
                                    {{ $existingReview ? 'Chỉnh sửa đánh giá' : 'Viết đánh giá' }}
                                </button>
                            </div>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        {{-- Cancel order --}}
        @if($order->order_status === 'pending')
            <div class="os-cancel-wrap">
                <form method="POST"
                      action="{{ route('orders.cancel', $order) }}"
                      onsubmit="return confirm('Bạn chắc chắn muốn huỷ đơn hàng này?')">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="os-btn-cancel">
                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10"/>
                            <line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/>
                        </svg>
                        Huỷ đơn hàng
                    </button>
                </form>
            </div>
        @endif
    </div>

    {{-- RIGHT: Summary aside ────────────────────────────── --}}
    <aside class="os-card os-summary">
        <div class="os-summary-head">
            <p class="os-summary-title">Thông tin đơn hàng</p>
        </div>

        <div class="os-info-rows">
            <div class="os-info-row">
                <span class="os-info-label">Ngày đặt</span>
                <span class="os-info-val">{{ $order->created_at?->format('d/m/Y H:i') }}</span>
            </div>
            <div class="os-info-row">
                <span class="os-info-label">Người nhận</span>
                <span class="os-info-val">{{ $order->recipient_name }}</span>
            </div>
            <div class="os-info-row">
                <span class="os-info-label">Số điện thoại</span>
                <span class="os-info-val">{{ $order->recipient_phone }}</span>
            </div>
            <div class="os-info-row">
                <span class="os-info-label">Địa chỉ giao</span>
                <span class="os-info-val" style="max-width:180px">{{ $order->shipping_address }}</span>
            </div>
            <div class="os-info-row">
                <span class="os-info-label">Thanh toán</span>
                <span class="os-pay-method">{{ $order->payment_method }}</span>
            </div>
            <div class="os-info-divider"></div>
            <div class="os-info-row">
                <span class="os-info-label">Trạng thái</span>
                <span class="os-status-badge os-sb-{{ $order->order_status }}">
                    {{ match($order->order_status) {
                        'pending'   => 'Chờ xử lý',
                        'confirmed' => 'Đã xác nhận',
                        'shipping'  => 'Đang giao',
                        'completed' => 'Hoàn tất',
                        'cancelled' => 'Đã huỷ',
                        default     => $order->order_status,
                    } }}
                </span>
            </div>
        </div>

        <div class="os-totals">
            <div class="os-total-row">
                <span>Tạm tính</span>
                <strong>{{ number_format((float)$order->subtotal, 0, ',', '.') }}đ</strong>
            </div>
            <div class="os-total-row">
                <span>Phí vận chuyển</span>
                <strong>{{ number_format((float)$order->shipping_fee, 0, ',', '.') }}đ</strong>
            </div>
            <div class="os-grand-row">
                <span class="os-grand-lbl">Tổng thanh toán</span>
                <span class="os-grand-val">{{ number_format((float)$order->total_amount, 0, ',', '.') }}đ</span>
            </div>
        </div>
    </aside>
</div>{{-- /.os-layout --}}

{{-- ══════════════════════════════════════════════════════
     REVIEW MODAL (logic giữ nguyên)
══════════════════════════════════════════════════════ --}}
<div id="review-modal" class="os-modal-wrap">
    <div class="os-modal">
        <div class="os-modal-head">
            <h3 id="review-modal-title" class="os-modal-title">Đánh giá sách</h3>
            <button type="button" id="review-modal-close" class="os-modal-close">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>

        <form id="review-modal-form" method="POST" action="">
            @csrf
            <div class="os-modal-body">

                {{-- Star picker (visual) --}}
                <div class="os-modal-field">
                    <label class="os-modal-label">Xếp hạng</label>
                    <div class="os-star-picker" id="os-star-picker">
                        @for($s = 1; $s <= 5; $s++)
                            <span class="os-star-pick" data-val="{{ $s }}">★</span>
                        @endfor
                    </div>
                    {{-- Hidden select keeps the backend logic unchanged --}}
                    <select name="rating" id="review-modal-rating"
                            class="os-modal-select" style="margin-top:8px">
                        @for($rating = 5; $rating >= 1; $rating--)
                            <option value="{{ $rating }}">{{ $rating }} sao</option>
                        @endfor
                    </select>
                </div>

                <div class="os-modal-field">
                    <label class="os-modal-label">Nhận xét</label>
                    <textarea name="comment" id="review-modal-comment"
                              class="os-modal-textarea"
                              placeholder="Chia sẻ cảm nhận của bạn về cuốn sách này..."></textarea>
                </div>
            </div>

            <div class="os-modal-foot">
                <button type="button" id="review-modal-cancel" class="os-modal-cancel">Huỷ</button>
                <button type="submit" class="os-modal-submit">
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                    Lưu đánh giá
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    /* ── Review base URL (logic giữ nguyên) ── */
    const reviewBase = "{{ url('/don-hang/'.$order->id.'/danh-gia') }}";

    const modal       = document.getElementById('review-modal');
    const closeBtn    = document.getElementById('review-modal-close');
    const cancelBtn   = document.getElementById('review-modal-cancel');
    const form        = document.getElementById('review-modal-form');
    const titleEl     = document.getElementById('review-modal-title');
    const ratingSelect= document.getElementById('review-modal-rating');
    const commentField= document.getElementById('review-modal-comment');

    /* ── Open / close ── */
    const openModal  = () => modal.classList.add('is-open');
    const closeModal = () => modal.classList.remove('is-open');

    closeBtn .addEventListener('click', closeModal);
    cancelBtn.addEventListener('click', closeModal);
    modal    .addEventListener('click', e => { if (e.target === modal) closeModal(); });

    document.addEventListener('keydown', e => { if (e.key === 'Escape') closeModal(); });

    /* ── Populate modal ── */
    function openReviewModal(bookSlug, bookTitle, existingRating, existingComment) {
        titleEl.textContent   = 'Đánh giá: ' + (bookTitle || 'Sách này');
        form.action           = reviewBase + '/' + encodeURIComponent(bookSlug);
        ratingSelect.value    = existingRating || '5';
        commentField.value    = existingComment || '';
        paintStars(parseInt(ratingSelect.value, 10));
        openModal();
    }

    document.querySelectorAll('.open-review-modal').forEach(btn => {
        btn.addEventListener('click', () => {
            openReviewModal(
                btn.dataset.bookSlug,
                btn.dataset.bookTitle,
                btn.dataset.existingRating,
                btn.dataset.existingComment
            );
        });
    });

    /* ── Star picker ── */
    const stars = document.querySelectorAll('#os-star-picker .os-star-pick');

    function paintStars(val) {
        stars.forEach(s => s.classList.toggle('on', parseInt(s.dataset.val) <= val));
    }

    stars.forEach(s => {
        s.addEventListener('mouseenter', () => paintStars(parseInt(s.dataset.val)));
        s.addEventListener('mouseleave', () => paintStars(parseInt(ratingSelect.value)));
        s.addEventListener('click', () => {
            ratingSelect.value = s.dataset.val;
            paintStars(parseInt(s.dataset.val));
        });
    });

    ratingSelect.addEventListener('change', () => paintStars(parseInt(ratingSelect.value)));
});
</script>

@endsection