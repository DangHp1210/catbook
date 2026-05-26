@extends('layouts.app')

@section('title', 'Giỏ hàng')

@section('content')

<style>
/* ─── Tokens ──────────────────────────────────────────── */
:root {
    --cb-bg:           var(--cb-brand-bg);
    --cb-border:       var(--cb-brand-border);
    --cb-text:         var(--cb-brand-text);
    --cb-muted:        var(--cb-brand-muted);
    --cb-white:        var(--cb-brand-white);
    --cb-accent:       var(--cb-brand-accent);
    --cb-accent-dark:  var(--cb-brand-accent-dark);
    --cb-accent-light: var(--cb-brand-accent-light);
    --cb-serif:        var(--cb-font-serif);
    --cb-sans:         var(--cb-font-sans);
}
html, body {
    background: var(--cb-bg);
    margin: 0;
}
/* ─── Page header ─────────────────────────────────────── */
.cb-page {
    width: min(1160px, calc(100% - 2rem)) !important; 
}
.ct-header {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 24px; gap: 12px; flex-wrap: wrap;
}
.ct-heading {
    font-family: var(--cb-serif);
    font-size: 34px; font-weight: 900;
    color: #0d1b10; letter-spacing: -1px; line-height: 1.1;
}
.ct-continue {
    font-family: var(--cb-sans); font-size: 13px; font-weight: 500;
    color: var(--cb-accent); text-decoration: none;
    display: inline-flex; align-items: center; gap: 6px;
    border-bottom: 1px solid var(--cb-accent); padding-bottom: 1px;
    transition: opacity .18s;
}
.ct-continue:hover { opacity: .65; }

/* ─── Flash ───────────────────────────────────────────── */
.ct-flash {
    display: flex; align-items: flex-start; gap: 10px;
    padding: 13px 18px; border-radius: 12px; border: 1px solid;
    font-family: var(--cb-sans); font-size: 13px; margin-bottom: 18px;
}
.ct-flash svg { flex-shrink: 0; margin-top: 1px; }
.ct-flash-ok  { background: #f0fdf4; border-color: #bbf7d0; color: #166534; }
.ct-flash-err { background: #fff1f2; border-color: #fecdd3; color: #9f1239; }

/* ─── Empty state ─────────────────────────────────────── */
.ct-empty {
    background: var(--cb-white); border: 2px dashed var(--cb-border);
    border-radius: 20px; padding: 72px 32px; text-align: center;
}
.ct-empty-icon { color: #c9bfa8; margin-bottom: 18px; }
.ct-empty h2 {
    font-family: var(--cb-serif); font-size: 24px; font-weight: 700;
    color: var(--cb-text); margin: 0 0 8px;
}
.ct-empty p {
    font-family: var(--cb-sans); font-size: 14px;
    color: var(--cb-muted); margin: 0 0 24px;
}
.ct-empty-btn {
    font-family: var(--cb-sans); font-size: 14px; font-weight: 600;
    padding: 11px 28px; border-radius: 10px;
    background: var(--cb-text); color: #fff; text-decoration: none;
    display: inline-flex; align-items: center; gap: 8px;
    transition: background .2s;
}
.ct-empty-btn:hover { background: var(--cb-accent); }

/* ─── 2-col layout ────────────────────────────────────── */
.ct-layout {
    display: grid;
    grid-template-columns: 1fr 360px;
    gap: 40px; align-items: start;
}
@media (max-width: 860px) { .ct-layout { grid-template-columns: 1fr; } }

/* ─── Cart item card ──────────────────────────────────── */
.ct-items { display: flex; flex-direction: column; gap: 12px; }

.ct-item {
    background: var(--cb-white); border: 1px solid var(--cb-border);
    border-radius: 16px; padding: 18px 20px;
    display: flex; gap: 18px; align-items: flex-start;
    transition: box-shadow .22s;
}
.ct-item:hover { box-shadow: 0 6px 22px rgba(0,0,0,.07); }

/* Cover thumb */
.ct-thumb {
    width: 76px; height: 106px; flex-shrink: 0;
    border-radius: 10px; overflow: hidden;
    background: #ede9e1; display: flex;
    align-items: center; justify-content: center;
}
.ct-thumb img { width: 100%; height: 100%; object-fit: cover; }
.ct-thumb-ph {
    font-family: var(--cb-serif); font-size: 28px;
    font-weight: 900; color: #c5bdb0;
}

/* Item body */
.ct-item-body {
    flex: 1; min-width: 0;
    display: flex; flex-direction: column; gap: 12px;
}

.ct-item-title {
    font-family: var(--cb-sans); font-size: 15px; font-weight: 600;
    color: var(--cb-text); text-decoration: none; line-height: 1.4;
    display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
    line-clamp: 2;
    transition: color .15s;
}
.ct-item-title:hover { color: var(--cb-accent); }
.ct-item-author {
    font-family: var(--cb-sans); font-size: 12px; color: #aaa;
    margin-top: 3px;
}

/* Unit price */
.ct-unit-price {
    font-family: var(--cb-sans); font-size: 12px; color: var(--cb-muted);
}
.ct-unit-price strong { color: var(--cb-text); font-weight: 600; }

/* Bottom row: qty form + total + delete */
.ct-item-foot {
    display: flex; align-items: center;
    justify-content: space-between; gap: 12px; flex-wrap: wrap;
}

/* Qty form */
.ct-qty-form { display: flex; align-items: center; gap: 8px; }
.ct-qty-lbl {
    font-family: var(--cb-sans); font-size: 12px; color: var(--cb-muted);
}
.ct-qty-wrap {
    display: flex; align-items: center;
    border: 1.5px solid var(--cb-border); border-radius: 8px;
    overflow: hidden; background: var(--cb-white);
}
.ct-qty-btn {
    width: 32px; height: 34px; border: none; background: transparent;
    font-size: 16px; color: var(--cb-text); cursor: pointer;
    transition: background .15s; font-family: var(--cb-sans);
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.ct-qty-btn:hover { background: var(--cb-bg); }
.ct-qty-input {
    width: 44px; height: 34px; border: none;
    border-left: 1px solid var(--cb-border);
    border-right: 1px solid var(--cb-border);
    text-align: center; outline: none;
    font-family: var(--cb-sans); font-size: 13px; font-weight: 600;
    color: var(--cb-text); background: var(--cb-white);
}
.ct-qty-input::-webkit-inner-spin-button,
.ct-qty-input::-webkit-outer-spin-button { -webkit-appearance: none; }

.ct-update-btn {
    font-family: var(--cb-sans); font-size: 12px; font-weight: 600;
    padding: 7px 14px; border-radius: 8px;
    border: 1.5px solid var(--cb-border);
    background: transparent; color: var(--cb-text);
    cursor: pointer; transition: all .18s; white-space: nowrap;
}
.ct-update-btn:hover { border-color: var(--cb-accent); color: var(--cb-accent); }

/* Item total price */
.ct-item-total {
    font-family: var(--cb-serif); font-size: 20px; font-weight: 700;
    color: var(--cb-accent); line-height: 1; white-space: nowrap;
}

/* Delete */
.ct-del-btn {
    font-family: var(--cb-sans); font-size: 12px; font-weight: 500;
    padding: 7px 12px; border-radius: 8px;
    border: 1.5px solid #fecdd3; background: transparent;
    color: #dc2626; cursor: pointer;
    transition: background .18s, border-color .18s;
    display: inline-flex; align-items: center; gap: 5px;
    white-space: nowrap;
}
.ct-del-btn:hover { background: #fff1f2; border-color: #fca5a5; }

/* ─── Summary aside ───────────────────────────────────── */
.ct-summary {
    background: var(--cb-white); border: 1px solid var(--cb-border);
    border-radius: 18px; overflow: hidden;
    position: sticky; top: 84px;
}
.ct-summary-head {
    padding: 20px 22px 16px;
    border-bottom: 1px solid var(--cb-border);
    position: relative;
}
.ct-summary-head::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
    background: linear-gradient(90deg, var(--cb-accent), #4ade80);
}
.ct-summary-title {
    font-family: var(--cb-serif); font-size: 20px; font-weight: 700;
    color: var(--cb-text);
}

.ct-summary-body { padding: 18px 22px; }

.ct-sum-row {
    display: flex; align-items: center; justify-content: space-between;
    font-family: var(--cb-sans); font-size: 13px;
    color: var(--cb-muted); margin-bottom: 10px;
}
.ct-sum-row:last-child { margin-bottom: 0; }
.ct-sum-row strong { color: var(--cb-text); font-weight: 600; }

.ct-sum-divider { height: 1px; background: var(--cb-border); margin: 14px 0; }

.ct-sum-total {
    display: flex; align-items: center; justify-content: space-between;
    font-family: var(--cb-sans);
}
.ct-sum-total-lbl { font-size: 14px; font-weight: 600; color: var(--cb-text); }
.ct-sum-total-val {
    font-family: var(--cb-serif); font-size: 28px; font-weight: 900;
    color: var(--cb-accent); letter-spacing: -.5px; line-height: 1;
}

.ct-summary-foot { padding: 0 22px 22px; }

.ct-checkout-btn {
    display: flex; align-items: center; justify-content: center; gap: 8px;
    width: 100%;
    font-family: var(--cb-sans); font-size: 15px; font-weight: 600;
    padding: 14px; border-radius: 12px;
    background: var(--cb-accent); color: #fff;
    text-decoration: none; border: none; cursor: pointer;
    transition: background .2s, transform .15s;
    margin-bottom: 12px;
}
.ct-checkout-btn:hover { background: var(--cb-accent-dark); transform: translateY(-1px); }

.ct-free-ship {
    display: flex; align-items: center; gap: 7px;
    font-family: var(--cb-sans); font-size: 12px; color: var(--cb-muted);
    background: var(--cb-bg); border: 1px solid var(--cb-border);
    border-radius: 9px; padding: 9px 12px;
}
.ct-free-ship svg { flex-shrink: 0; color: var(--cb-accent); }

/* progress bar toward free shipping */
.ct-ship-bar-wrap { margin-top: 8px; }
.ct-ship-bar-track {
    height: 5px; background: var(--cb-border); border-radius: 999px; overflow: hidden;
    margin-top: 6px;
}
.ct-ship-bar-fill {
    height: 100%; border-radius: 999px;
    background: linear-gradient(90deg, var(--cb-accent), #4ade80);
    transition: width .4s ease;
}
.ct-page-gap { margin-bottom: 72px; }
body {
        font-family: var(--cb-sans, 'DM Sans', system-ui, sans-serif);
        background: var(--cb-bg);
        color: var(--cb-text);
        margin: 0;
}

</style>

{{-- ── Page header ──────────────────────────────────────── --}}
<div class="ct-page-gap p-5 rounded-xl">
<div class="ct-header">
    <h1 class="ct-heading">Giỏ hàng của bạn</h1>
    <a href="{{ route('catalog.categories') }}" class="ct-continue">
        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
            <path d="M19 12H5M12 19l-7-7 7-7"/>
        </svg>
        Tiếp tục mua sách
    </a>
</div>

{{-- ── Flash messages ───────────────────────────────────── --}}
@if(session('success'))
    <div class="ct-flash ct-flash-ok" data-flash="success">
        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
            <polyline points="20 6 9 17 4 12"/>
        </svg>
        {{ session('success') }}
    </div>
@endif

@if(session('error'))
    <div class="ct-flash ct-flash-err">
        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="10"/>
            <line x1="15" y1="9" x2="9" y2="15"/>
            <line x1="9" y1="9" x2="15" y2="15"/>
        </svg>
        {{ session('error') }}
    </div>
@endif

{{-- ══════════════════════════════════════════════════════
     EMPTY STATE
════════════════════════════════════════════════════════ --}}
@if($items->isEmpty())
    <div class="ct-empty">
        <div class="ct-empty-icon">
            <svg width="56" height="56" fill="none" stroke="currentColor" stroke-width="1.4" viewBox="0 0 24 24">
                <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/>
                <line x1="3" y1="6" x2="21" y2="6"/>
                <path d="M16 10a4 4 0 01-8 0"/>
            </svg>
        </div>
        <h2>Giỏ hàng đang trống</h2>
        <p>Hãy thêm vài cuốn sách bạn yêu thích để tiếp tục.</p>
        <a href="{{ route('catalog.categories') }}" class="ct-empty-btn">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <path d="M12 5v14M5 12l7-7 7 7"/>
            </svg>
            Khám phá sách ngay
        </a>
    </div>

@else

{{-- ══════════════════════════════════════════════════════
     CART WITH ITEMS
════════════════════════════════════════════════════════ --}}
@php
    $freeShipThreshold = 299000;
    $pct = min(100, round($subtotal / $freeShipThreshold * 100));
    $remaining = max(0, $freeShipThreshold - $subtotal);
@endphp

<div class="ct-layout">

    {{-- ── Left: item list ──────────────────────────────── --}}
    <div class="ct-items">
        @foreach($items as $item)
            @php
                $book = $item->book;
                $cover = null;
                if (!empty($book?->cover_image)) {
                    $cover = str_starts_with($book->cover_image, 'http://') || str_starts_with($book->cover_image, 'https://')
                        ? $book->cover_image
                        : asset('storage/'.$book->cover_image);
                }
                $initials = mb_strtoupper(mb_substr($book?->title ?? '', 0, 1));
            @endphp
            <article class="ct-item">

                {{-- Cover --}}
                <a href="{{ route('catalog.book', $book?->slug ?? $book?->id) }}" class="ct-thumb">
                    @if($cover)
                        <img src="{{ $cover }}" alt="{{ $book?->title ?? 'Sách' }}" loading="lazy">
                    @else
                        <span class="ct-thumb-ph">{{ $initials }}</span>
                    @endif
                </a>

                {{-- Body --}}
                <div class="ct-item-body">

                    {{-- Title + author --}}
                    <div>
                        <a href="{{ route('catalog.book', $book?->slug ?? $book?->id) }}"
                           class="ct-item-title">{{ $book?->title ?? 'Sách' }}</a>
                        <p class="ct-item-author">
                            {{ $book?->authors->pluck('name')->first() ?: 'Đang cập nhật tác giả' }}
                        </p>
                    </div>

                    {{-- Unit price --}}
                    <p class="ct-unit-price">
                        Đơn giá: <strong>{{ number_format((float)$item->unit_price, 0, ',', '.') }}đ</strong>
                    </p>

                    {{-- Footer: qty + total + delete --}}
                    <div class="ct-item-foot">

                        {{-- Qty update form --}}
                        <form method="POST"
                              action="{{ route('cart.items.update', $item->id) }}"
                              class="ct-qty-form"
                              id="qty-form-{{ $item->id }}">
                            @csrf
                            @method('PATCH')
                            <span class="ct-qty-lbl">Số lượng</span>
                            <div class="ct-qty-wrap">
                                <button type="button" class="ct-qty-btn js-qty-btn"
                                    data-item-id="{{ $item->id }}" data-delta="-1">−</button>
                                <input id="qty-{{ $item->id }}"
                                       name="quantity"
                                       type="number"
                                       min="1" max="99"
                                       value="{{ $item->quantity }}"
                                       class="ct-qty-input">
                                <button type="button" class="ct-qty-btn js-qty-btn"
                                    data-item-id="{{ $item->id }}" data-delta="1">+</button>
                            </div>
                            <button type="submit" class="ct-update-btn">Cập nhật</button>
                        </form>

                        {{-- Row right: total + delete --}}
                        <div style="display:flex;align-items:center;gap:12px;flex-wrap:wrap">
                            <span class="ct-item-total">
                                {{ number_format((float)$item->unit_price * $item->quantity, 0, ',', '.') }}đ
                            </span>

                            <form method="POST"
                                  action="{{ route('cart.items.destroy', $item->id) }}">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="ct-del-btn">
                                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <polyline points="3 6 5 6 21 6"/>
                                        <path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/>
                                        <path d="M10 11v6M14 11v6"/>
                                        <path d="M9 6V4h6v2"/>
                                    </svg>
                                    Xoá
                                </button>
                            </form>
                        </div>

                    </div>
                </div>
            </article>
        @endforeach
    </div>

    {{-- ── Right: summary ───────────────────────────────── --}}
    <aside class="ct-summary">

        <div class="ct-summary-head">
            <p class="ct-summary-title">Tóm tắt đơn hàng</p>
        </div>

        <div class="ct-summary-body">
            <div class="ct-sum-row">
                <span>Tạm tính</span>
                <strong>{{ number_format($subtotal, 0, ',', '.') }}đ</strong>
            </div>
            <div class="ct-sum-row">
                <span>Phí vận chuyển</span>
                <strong style="color:var(--cb-muted);font-weight:500">Tính ở bước thanh toán</strong>
            </div>

            <div class="ct-sum-divider"></div>

            <div class="ct-sum-total">
                <span class="ct-sum-total-lbl">Tổng dự kiến</span>
                <span class="ct-sum-total-val">{{ number_format($subtotal, 0, ',', '.') }}đ</span>
            </div>
        </div>

        <div class="ct-summary-foot">

            <a href="{{ route('checkout.show') }}" class="ct-checkout-btn">
                <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path d="M5 12h14M12 5l7 7-7 7"/>
                </svg>
                Tiến hành thanh toán
            </a>

            {{-- Free shipping progress --}}
            <div class="ct-free-ship">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <rect x="1" y="3" width="15" height="13" rx="1"/>
                    <path d="M16 8h4l3 5v3h-7V8z"/>
                    <circle cx="5.5" cy="18.5" r="2.5"/>
                    <circle cx="18.5" cy="18.5" r="2.5"/>
                </svg>
                @if($remaining > 0)
                    <span>Mua thêm <strong style="color:var(--cb-accent)">{{ number_format($remaining, 0, ',', '.') }}đ</strong> để được miễn phí vận chuyển</span>
                @else
                    <span style="color:var(--cb-accent);font-weight:600">🎉 Bạn được miễn phí vận chuyển!</span>
                @endif
            </div>

            <div class="ct-ship-bar-wrap">
                <div class="ct-ship-bar-track">
                    <div class="ct-ship-bar-fill" data-pct="{{ $pct }}"></div>
                </div>
            </div>

        </div>
    </aside>

</div>{{-- /.ct-layout --}}
@endif

</div>{{-- /.ct-page-gap --}}

<script>
/* ── Qty ± buttons ────────────────────────────────────── */
function ctAdj(id, delta) {
    const inp = document.getElementById('qty-' + id);
    if (!inp) return;
    const max = parseInt(inp.max, 10) || 99;
    const val = Math.min(Math.max((parseInt(inp.value, 10) || 1) + delta, 1), max);
    inp.value = val;
}

/* Attach click handlers to buttons that use data attributes (avoids Blade braces in attributes) */
(function () {
    const buttons = document.querySelectorAll('.js-qty-btn');
    buttons.forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.dataset.itemId;
            const delta = parseInt(btn.dataset.delta, 10) || 0;
            ctAdj(id, delta);
        });
    });
})();

/* Set free-shipping progress bar widths from data attribute (avoids Blade inside style) */
(function () {
    const fills = document.querySelectorAll('.ct-ship-bar-fill');
    fills.forEach(el => {
        const pct = parseFloat(el.dataset.pct) || 0;
        el.style.width = pct + '%';
    });
})();
</script>

@endsection