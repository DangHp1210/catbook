@extends('layouts.app')

@section('title', $book->title)

@section('content')
<style>

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
/* ── Flash messages ──────────────────────────────────── */
.bd-alert {
    display: flex; align-items: flex-start; gap: 10px;
    padding: 13px 18px; border-radius: 12px; border: 1px solid;
    font-family: var(--cb-sans); font-size: 13px; margin-bottom: 22px;
}
.bd-alert svg { flex-shrink: 0; margin-top: 1px; }
.bd-alert-ok  { background: #f0fdf4; border-color: #bbf7d0; color: #166534; }
.bd-alert-err { background: #fff1f2; border-color: #fecdd3; color: #9f1239; }

/* ── Breadcrumb ──────────────────────────────────────── */
.bd-crumb {
    display: flex; align-items: center; gap: 6px; flex-wrap: wrap;
    font-family: var(--cb-sans); font-size: 12px;
    color: var(--cb-muted); margin-bottom: 28px;
}
.bd-crumb a { color: var(--cb-muted); text-decoration: none; transition: color .15s; }
.bd-crumb a:hover { color: var(--cb-accent); }
.bd-crumb-sep { opacity: .4; }

/* ════════════════════════════════════════════════════════
   MAIN 2-COL GRID
════════════════════════════════════════════════════════ */
.bd-grid {
    display: grid;
    grid-template-columns: 300px 1fr;
    gap: 32px;
    align-items: start;
    margin-bottom: 48px;
}
@media (max-width: 860px) {
    .bd-grid { grid-template-columns: 1fr; }
}

/* ── LEFT: Cover column ──────────────────────────────── */
.bd-left { position: sticky; top: 84px; }

.bd-cover {
    background: var(--cb-white);
    border: 1px solid var(--cb-border);
    border-radius: 20px;
    overflow: hidden;
    aspect-ratio: 3 / 4;
    display: flex; align-items: center; justify-content: center;
    position: relative;
}
.bd-cover img {
    width: 100%; height: 100%; object-fit: cover; display: block;
    transition: transform .45s ease;
}
.bd-cover:hover img { transform: scale(1.04); }

.bd-cover-placeholder {
    display: flex; flex-direction: column;
    align-items: center; justify-content: center;
    width: 100%; height: 100%;
    background: linear-gradient(135deg, #f0ede6, #e8e3d8);
    gap: 14px;
}
.bd-cover-ph-letter {
    font-family: var(--cb-serif);
    font-size: 88px; font-weight: 900; color: #c5bdb0; line-height: 1;
}
.bd-cover-ph-label {
    font-family: var(--cb-sans); font-size: 12px; color: #b0a898;
}

.bd-cover-badge {
    position: absolute; top: 14px; left: 14px;
    background: #dc2626; color: #fff;
    font-family: var(--cb-sans); font-size: 11px; font-weight: 700;
    padding: 4px 13px; border-radius: 999px; letter-spacing: .4px;
}

/* Khai báo thêm một số biến màu sắc cho giao diện tươi sáng hơn (có thể tùy chỉnh) */
:root {
    --cb-primary-light: #e2e0dd;
    --cb-primary-text: #1c1c1c;
    --cb-text-main: #1f2937;
    --cb-text-muted: #4b5563;
    --cb-border-light: #f1f5f9;
}

/* Khung ngoài của thẻ */
.bd-perks {
    margin-top: 16px;
    background: var(--cb-white, #ffffff);
    border: 1px solid var(--cb-border, #e2e8f0);
    border-radius: 16px; /* Bo góc mềm mại hơn */
    box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04); /* Đổ bóng nhẹ */
    overflow: hidden;
    transition: box-shadow 0.3s ease;
}

.bd-perks:hover {
    box-shadow: 0 6px 24px rgba(0, 0, 0, 0.08);
}

/* Tiêu đề của thẻ */
.bd-perks-head {
    padding: 14px 16px;
    border-bottom: 1px solid var(--cb-border, #e2e8f0);
    font-family: var(--cb-serif);
    font-size: 15px;
    font-weight: 700;
    color: var(--cb-primary-text);
    display: flex;
    align-items: center;
    gap: 8px;
}

/* Thêm icon nhỏ trang trí vào tiêu đề cho hợp vibe CatBook */
.bd-perks-head::before {
    content: '🙀'; 
    font-size: 16px;
}

/* Danh sách đặc quyền */
.bd-perks-list { 
    display: flex;
    flex-direction: column;
    gap: 4px;
}

/* Từng mục đặc quyền */
.bd-perk-item {
    display: flex; 
    align-items: center;
    gap: 14px;
    padding: 12px 14px;
    border-radius: 10px;
    font-family: var(--cb-sans, system-ui, sans-serif);
    font-size: 14px;
    line-height: 1.4;
    color: var(--cb-text-muted);
    transition: background-color 0.2s ease, transform 0.2s ease;
}

/* Icon bọc trong hình tròn */
.bd-perk-ico {
    width: 38px; 
    height: 38px; 
    flex-shrink: 0;
    display: inline-flex; 
    align-items: center; 
    justify-content: center;
    font-size: 18px;
    background: var(--cb-primary-light);
    border-radius: 50%;
    color: var(--cb-primary-text);
}

/* Khối văn bản */
.bd-perk-text {
    flex: 1;
}

/* In đậm chữ */
.bd-perk-text strong { 
    color: var(--cb-text-main); 
    font-weight: 600;
}


/* ── RIGHT: Info column ──────────────────────────────── */
.bd-right { display: flex; flex-direction: column; gap: 22px; }

/* Eyebrow pill */
.bd-eyebrow {
    display: inline-flex; align-items: center; gap: 6px;
    font-family: var(--cb-sans);
    font-size: 11px; font-weight: 600; letter-spacing: 1.6px;
    text-transform: uppercase; color: var(--cb-accent);
    background: var(--cb-accent-light);
    padding: 4px 13px; border-radius: 999px; margin-bottom: 10px;
}

/* Title */
.bd-title {
    font-family: var(--cb-serif);
    font-size: 36px; font-weight: 900; color: #0d1b10;
    letter-spacing: -1.2px; line-height: 1.08; margin: 0 0 14px;
}

/* Authors */
.bd-authors {
    font-family: var(--cb-sans); font-size: 14px; color: var(--cb-muted);
    margin-bottom: 14px;
}
.bd-authors strong { color: var(--cb-text); }

/* Category tags */
.bd-tags { display: flex; flex-wrap: wrap; gap: 6px; }
.bd-tag {
    font-family: var(--cb-sans); font-size: 12px; font-weight: 500;
    padding: 5px 14px; border-radius: 999px;
    background: var(--cb-bg); border: 1px solid var(--cb-border);
    color: var(--cb-muted); text-decoration: none; transition: all .18s;
}
.bd-tag:hover {
    border-color: var(--cb-accent); color: var(--cb-accent);
    background: var(--cb-accent-light);
}

/* ── Price card ──────────────────────────────────────── */
.bd-price-card {
    background: var(--cb-white); border: 1px solid var(--cb-border);
    border-radius: 18px; padding: 22px 26px;
    position: relative; overflow: hidden;
}
/* top accent bar */
.bd-price-card::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
    background: linear-gradient(90deg, var(--cb-accent), #4ade80);
}

.bd-price-lbl {
    font-family: var(--cb-sans); font-size: 11px; font-weight: 600;
    letter-spacing: 1.3px; text-transform: uppercase;
    color: #b0a898; margin-bottom: 8px;
}
.bd-price-row {
    display: flex; align-items: flex-end; gap: 12px;
    flex-wrap: wrap; margin-bottom: 12px;
}
.bd-price-main {
    font-family: var(--cb-serif); font-size: 40px; font-weight: 900;
    color: var(--cb-accent); letter-spacing: -1px; line-height: 1;
}
.bd-price-orig {
    font-family: var(--cb-sans); font-size: 17px;
    color: #c0b8b0; text-decoration: line-through;
}
.bd-price-save {
    font-family: var(--cb-sans); font-size: 12px; font-weight: 700;
    background: #fef3c7; color: #92400e;
    padding: 3px 10px; border-radius: 999px; align-self: center;
}

/* Stock status */
.bd-stock {
    display: inline-flex; align-items: center; gap: 7px;
    font-family: var(--cb-sans); font-size: 13px; font-weight: 500;
    padding: 6px 14px; border-radius: 999px; margin-bottom: 20px;
}
.bd-stock-dot {
    width: 7px; height: 7px; border-radius: 50%; flex-shrink: 0;
    animation: pulse-dot 2s ease-in-out infinite;
}
@keyframes pulse-dot {
    0%,100% { opacity:1; transform:scale(1); }
    50%      { opacity:.5; transform:scale(1.4); }
}
.bd-stock.in  { background: var(--cb-accent-light); color: var(--cb-accent); }
.bd-stock.in .bd-stock-dot  { background: var(--cb-accent); }
.bd-stock.out { background: #fff1f2; color: #dc2626; }
.bd-stock.out .bd-stock-dot { background: #dc2626; }

/* Action row */
.bd-actions { display: flex; flex-wrap: wrap; gap: 10px; align-items: center; }

/* Qty control */
.bd-qty-wrap {
    display: flex; align-items: center;
    border: 1.5px solid var(--cb-border); border-radius: 10px;
    overflow: hidden; background: var(--cb-white);
}
.bd-qty-btn {
    width: 38px; height: 44px; border: none; background: transparent;
    font-size: 18px; color: var(--cb-text); cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    font-family: var(--cb-sans); transition: background .15s;
}
.bd-qty-btn:hover { background: var(--cb-bg); }
.bd-qty-input {
    width: 52px; height: 44px; border: none;
    border-left: 1px solid var(--cb-border);
    border-right: 1px solid var(--cb-border);
    text-align: center; outline: none;
    font-family: var(--cb-sans); font-size: 14px; font-weight: 600;
    color: var(--cb-text); background: var(--cb-white);
}
.bd-qty-input::-webkit-inner-spin-button,
.bd-qty-input::-webkit-outer-spin-button { -webkit-appearance: none; }

/* Buttons */
.bd-btn-cart {
    font-family: var(--cb-sans); font-size: 14px; font-weight: 600;
    padding: 0 24px; height: 44px; border-radius: 10px; border: none;
    background: var(--cb-text); color: #fff; cursor: pointer;
    display: flex; align-items: center; gap: 8px;
    white-space: nowrap; flex: 1;
    transition: background .2s, transform .15s;
}
.bd-btn-cart:hover:not(:disabled) { background: var(--cb-accent); transform: translateY(-1px); }
.bd-btn-cart:disabled { opacity: .4; cursor: not-allowed; }

.bd-btn-outline {
    font-family: var(--cb-sans); font-size: 13px; font-weight: 500;
    padding: 0 18px; height: 44px; border-radius: 10px;
    border: 1.5px solid var(--cb-border); background: var(--cb-white);
    color: var(--cb-text); text-decoration: none; cursor: pointer;
    display: inline-flex; align-items: center; gap: 7px;
    transition: border-color .2s, color .2s;
}
.bd-btn-outline:hover { border-color: var(--cb-accent); color: var(--cb-accent); }

.bd-btn-login {
    font-family: var(--cb-sans); font-size: 14px; font-weight: 600;
    padding: 0 28px; height: 44px; border-radius: 10px; border: none;
    background: var(--cb-text); color: #fff; text-decoration: none;
    display: inline-flex; align-items: center; gap: 8px;
    transition: background .2s;
}
.bd-btn-login:hover { background: var(--cb-accent); }

/* ── Info table ──────────────────────────────────────── */
.bd-table {
    background: var(--cb-white); border: 1px solid var(--cb-border);
    border-radius: 16px; overflow: hidden;
}
.bd-table-head {
    padding: 12px 20px;
    font-family: var(--cb-sans); font-size: 10px; font-weight: 700;
    letter-spacing: 1.5px; text-transform: uppercase; color: #b0a898;
    border-bottom: 1px solid var(--cb-border);
}
.bd-table-row {
    display: grid; grid-template-columns: 148px 1fr;
    padding: 11px 20px; border-bottom: 1px solid var(--cb-border);
    font-family: var(--cb-sans); font-size: 13px;
}
.bd-table-row:last-child { border-bottom: none; }
.bd-table-key { color: var(--cb-muted); font-weight: 500; }
.bd-table-val { color: var(--cb-text); font-weight: 500; }

/* ── Description card ────────────────────────────────── */
.bd-desc-card {
    background: var(--cb-white); border: 1px solid var(--cb-border);
    border-radius: 16px; padding: 22px 26px;
}
.bd-desc-title {
    font-family: var(--cb-serif); font-size: 20px; font-weight: 700;
    color: var(--cb-text); margin-bottom: 14px;
}
.bd-desc-body {
    font-family: var(--cb-sans); font-size: 14px; color: #555;
    line-height: 1.82; white-space: pre-line;
}
.bd-desc-empty {
    font-family: var(--cb-sans); font-size: 14px;
    color: #b0a898; font-style: italic;
}
/* read-more */
.bd-desc-inner { overflow: hidden; }
.bd-desc-inner.clamped {
    max-height: 116px;
    -webkit-mask-image: linear-gradient(180deg,#000 50%,transparent);
    mask-image: linear-gradient(180deg,#000 50%,transparent);
}
.bd-read-btn {
    margin-top: 10px;
    font-family: var(--cb-sans); font-size: 13px; font-weight: 600;
    color: var(--cb-accent); background: none; border: none;
    cursor: pointer; padding: 0;
    text-decoration: underline; text-underline-offset: 3px;
}

/* ── Reviews card ────────────────────────────────────── */
.bd-reviews {
    background: var(--cb-white); border: 1px solid var(--cb-border);
    border-radius: 16px; overflow: hidden;
}
.bd-reviews-hd {
    display: flex; align-items: center; justify-content: space-between;
    gap: 16px; flex-wrap: wrap;
    padding: 18px 22px; border-bottom: 1px solid var(--cb-border);
}
.bd-reviews-title {
    font-family: var(--cb-serif); font-size: 20px; font-weight: 700; color: var(--cb-text);
}
.bd-rating-row { display: flex; align-items: center; gap: 12px; }
.bd-rating-big {
    font-family: var(--cb-serif); font-size: 40px; font-weight: 900;
    color: var(--cb-text); line-height: 1;
}
.bd-stars { display: flex; gap: 2px; }
.bd-star     { font-size: 15px; color: #f59e0b; }
.bd-star.e   { color: #e0dbd0; }
.bd-rating-cnt {
    font-family: var(--cb-sans); font-size: 12px; color: var(--cb-muted); margin-top: 3px;
}

/* Review item */
.bd-review {
    padding: 16px 22px; border-bottom: 1px solid var(--cb-border);
}
.bd-review:last-child { border-bottom: none; }
.bd-review-top {
    display: flex; align-items: flex-start; justify-content: space-between;
    gap: 10px; margin-bottom: 8px; flex-wrap: wrap;
}
.bd-reviewer { display: flex; align-items: center; gap: 10px; }
.bd-av {
    width: 34px; height: 34px; border-radius: 50%;
    background: var(--cb-accent); color: #fff;
    font-family: var(--cb-sans); font-size: 13px; font-weight: 700;
    display: flex; align-items: center; justify-content: center; flex-shrink: 0;
}
.bd-rv-name {
    font-family: var(--cb-sans); font-size: 13px; font-weight: 600; color: var(--cb-text);
}
.bd-rv-date {
    font-family: var(--cb-sans); font-size: 11px; color: var(--cb-muted); margin-top: 2px;
}
.bd-rv-score {
    font-family: var(--cb-sans); font-size: 12px; font-weight: 700;
    background: #fef9ee; color: #92400e; border: 1px solid #fde68a;
    padding: 3px 10px; border-radius: 999px; white-space: nowrap; flex-shrink: 0;
}
.bd-rv-comment {
    font-family: var(--cb-sans); font-size: 13px; color: #555;
    line-height: 1.72; white-space: pre-line;
}
.bd-reviews-empty {
    padding: 36px 22px; text-align: center;
    font-family: var(--cb-sans); font-size: 13px; color: var(--cb-muted);
}

/* ── Related books ───────────────────────────────────── */
.bd-related-hd {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 18px; flex-wrap: wrap; gap: 10px;
}
.bd-related-title {
    font-family: var(--cb-serif); font-size: 26px; font-weight: 700;
    color: var(--cb-text); letter-spacing: -.5px;
}
.bd-related-more {
    font-family: var(--cb-sans); font-size: 13px; font-weight: 500;
    color: var(--cb-accent); text-decoration: none;
    border-bottom: 1px solid var(--cb-accent); padding-bottom: 1px;
    transition: opacity .18s;
}
.bd-related-more:hover { opacity: .65; }

.bd-rel-grid {
    display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px;
}
@media (max-width: 900px) { .bd-rel-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 480px) { .bd-rel-grid { grid-template-columns: 1fr; } }

.bd-rel-card {
    background: var(--cb-white); border: 1px solid var(--cb-border);
    border-radius: 14px; overflow: hidden;
    text-decoration: none; display: block;
    transition: transform .22s, box-shadow .22s;
}
.bd-rel-card:hover { transform: translateY(-4px); box-shadow: 0 12px 32px rgba(0,0,0,.09); }

.bd-rel-thumb {
    height: 160px; background: #ede9e1; overflow: hidden;
    display: flex; align-items: center; justify-content: center;
}
.bd-rel-thumb img { width: 100%; height: 100%; object-fit: cover; transition: transform .35s; }
.bd-rel-card:hover .bd-rel-thumb img { transform: scale(1.05); }
.bd-rel-thumb-ph {
    font-family: var(--cb-serif); font-size: 40px; font-weight: 900; color: #c5bdb0;
}
.bd-rel-body { padding: 12px 14px; }
.bd-rel-title {
    font-family: var(--cb-sans); font-size: 13px; font-weight: 600; color: var(--cb-text);
    line-height: 1.4; margin-bottom: 4px;
    display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
        line-clamp: 2;
    transition: color .15s;
}
.bd-rel-card:hover .bd-rel-title { color: var(--cb-accent); }
.bd-rel-author { font-family: var(--cb-sans); font-size: 11px; color: #aaa; margin-bottom: 8px; }
.bd-rel-price  { font-family: var(--cb-sans); font-size: 14px; font-weight: 700; color: var(--cb-accent); }
</style>

{{-- ── Flash messages ──────────────────────────────────────── --}}
@if(session('success'))
    <div class="bd-alert bd-alert-ok">
        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
            <polyline points="20 6 9 17 4 12"/>
        </svg>
        {{ session('success') }}
    </div>
@endif
@if(session('error'))
    <div class="bd-alert bd-alert-err">
        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
            <circle cx="12" cy="12" r="10"/>
            <line x1="15" y1="9" x2="9" y2="15"/>
            <line x1="9" y1="9" x2="15" y2="15"/>
        </svg>
        {{ session('error') }}
    </div>
@endif

{{-- ── Breadcrumb ───────────────────────────────────────────── --}}
<nav class="bd-crumb" aria-label="breadcrumb">
    <a href="{{ route('home') }}">Trang chủ</a>
    <span class="bd-crumb-sep">/</span>
    <a href="{{ route('catalog.categories') }}">Danh mục</a>
    <span class="bd-crumb-sep">/</span>
    <span style="color:var(--cb-text);font-weight:500">{{ Str::limit($book->title, 52) }}</span>
</nav>

{{-- ════════════════════════════════════════════════════════
     MAIN GRID
════════════════════════════════════════════════════════ --}}
@php
    $cover = null;
    if (!empty($book->cover_image)) {
        $cover = str_starts_with($book->cover_image, 'http://') || str_starts_with($book->cover_image, 'https://')
            ? $book->cover_image
            : asset('storage/'.$book->cover_image);
    }
    $hasDiscount = $book->discount_price && $book->discount_price < $book->price;
    $discountPct = $hasDiscount
        ? round(((float)$book->price - (float)$book->discount_price) / (float)$book->price * 100)
        : 0;
    $initial = mb_strtoupper(mb_substr($book->title, 0, 1));
@endphp

<div class="bd-grid">

    {{-- ── LEFT: Cover ─────────────────────────────────────── --}}
    <div class="bd-left">
        <div class="bd-cover">
            @if($cover)
                <img src="{{ $cover }}" alt="{{ $book->title }}">
            @else
                <div class="bd-cover-placeholder">
                    <span class="bd-cover-ph-letter">{{ $initial }}</span>
                    <span class="bd-cover-ph-label">Không có ảnh bìa</span>
                </div>
            @endif

            @if($hasDiscount)
                <span class="bd-cover-badge">-{{ $discountPct }}%</span>
            @endif
        </div>

        {{-- Quick-info chips --}}
        <div class="bd-perks" aria-label="Thông tin nổi bật">
            <div class="bd-perks-head">Chỉ có ở CatBook</div>
            <div class="bd-perks-list">
                <div class="bd-perk-item">
                    <span class="bd-perk-ico" aria-hidden="true">📄</span>
                    <span class="bd-perk-text">Sản phẩm <strong>100% chính hãng</strong></span>
                </div>
                <div class="bd-perk-item">
                    <span class="bd-perk-ico" aria-hidden="true">🎧</span>
                    <span class="bd-perk-text">Tư vấn mua sách 24/7</span>
                </div>
                <div class="bd-perk-item">
                    <span class="bd-perk-ico" aria-hidden="true">🚚</span>
                    <span class="bd-perk-text">Miễn phí vận chuyển cho<br>Đơn hàng từ 299.000đ</span>
                </div>
                <div class="bd-perk-item">
                    <span class="bd-perk-ico" aria-hidden="true">📞</span>
                    <span class="bd-perk-text">Hotline: <strong>1900 1210</strong></span>
                </div>
            </div>
        </div>
    </div>

    {{-- ── RIGHT: Info ──────────────────────────────────────── --}}
    <article class="bd-right">

        {{-- Title block --}}
        <div>
            @if($book->categories->isNotEmpty())
                <div class="bd-eyebrow">{{ $book->categories->first()->name }}</div>
            @endif

            <h1 class="bd-title">{{ $book->title }}</h1>

            <p class="bd-authors">
                Tác giả:&nbsp;
                <strong>{{ $book->authors->pluck('name')->join(', ') ?: 'Đang cập nhật' }}</strong>
            </p>

            @if($book->categories->isNotEmpty())
                <div class="bd-tags">
                    @foreach($book->categories as $cat)
                        <a href="{{ route('catalog.category', ['category' => $cat->slug ?: \Illuminate\Support\Str::slug($cat->name)]) }}" class="bd-tag">
                            {{ $cat->name }}
                        </a>
                    @endforeach
                </div>
            @endif
        </div>

        {{-- Price card --}}
        <div class="bd-price-card">
            <p class="bd-price-lbl">Giá bán</p>

            <div class="bd-price-row">
                <span class="bd-price-main">
                    {{ number_format((float)($book->discount_price ?? $book->price), 0, ',', '.') }}đ
                </span>
                @if($book->discount_price)
                    <span class="bd-price-orig">
                        {{ number_format((float)$book->price, 0, ',', '.') }}đ
                    </span>
                    <span class="bd-price-save">Tiết kiệm {{ $discountPct }}%</span>
                @endif
            </div>

            <div class="bd-stock {{ $book->stock_quantity > 0 ? 'in' : 'out' }}">
                <span class="bd-stock-dot"></span>
                {{ $book->stock_quantity > 0
                    ? 'Còn hàng'
                    : 'Tạm hết hàng' }}
            </div>

            {{-- Actions --}}
            <div class="bd-actions">
                @auth
                    <form method="POST"
                          action="{{ route('cart.store', $book->slug) }}"
                          style="display:contents">
                        @csrf
                        <div class="bd-qty-wrap">
                            <button type="button" class="bd-qty-btn" onclick="bdAdj(-1)">−</button>
                            <input id="bd-qty" type="number" name="quantity"
                                   class="bd-qty-input" value="1" min="1"
                                   max="{{ max(1, $book->stock_quantity) }}">
                            <button type="button" class="bd-qty-btn" onclick="bdAdj(1)">+</button>
                        </div>

                        <button type="submit" class="bd-btn-cart"
                                {{ $book->stock_quantity <= 0 ? 'disabled' : '' }}>
                            <svg width="15" height="15" fill="none" stroke="currentColor"
                                 stroke-width="2" viewBox="0 0 24 24">
                                <path d="M6 2L3 6v14a2 2 0 002 2h14a2 2 0 002-2V6l-3-4z"/>
                                <line x1="3" y1="6" x2="21" y2="6"/>
                                <path d="M16 10a4 4 0 01-8 0"/>
                            </svg>
                            Thêm vào giỏ hàng
                        </button>
                    </form>

                    <a href="{{ route('cart.index') }}" class="bd-btn-outline">
                        <svg width="14" height="14" fill="none" stroke="currentColor"
                             stroke-width="2" viewBox="0 0 24 24">
                            <path d="M5 12h14M12 5l7 7-7 7"/>
                        </svg>
                        Xem giỏ hàng
                    </a>
                @else
                    <a href="{{ route('login') }}" class="bd-btn-login">
                        Đăng nhập để mua
                    </a>
                @endauth
            </div>
        </div>

        {{-- Info table --}}
        <div class="bd-table">
            <div class="bd-table-head">Thông tin sách</div>

            @if($book->publisher)
                <div class="bd-table-row">
                    <span class="bd-table-key">Nhà xuất bản</span>
                    <span class="bd-table-val">{{ $book->publisher->name }}</span>
                </div>
            @endif
            <div class="bd-table-row">
                <span class="bd-table-key">ISBN</span>
                <span class="bd-table-val" style="font-family:monospace;letter-spacing:.4px">
                    {{ $book->isbn ?? 'N/A' }}
                </span>
            </div>
            <div class="bd-table-row">
                <span class="bd-table-key">Số trang</span>
                <span class="bd-table-val">
                    {{ $book->page_count ? number_format($book->page_count).' trang' : 'Đang cập nhật' }}
                </span>
            </div>
            <div class="bd-table-row">
                <span class="bd-table-key">Ngôn ngữ</span>
                <span class="bd-table-val">{{ $book->language ?? 'Đang cập nhật' }}</span>
            </div>
            <div class="bd-table-row">
                <span class="bd-table-key">Hình thức</span>
                <span class="bd-table-val">{{ $book->books_format ?? 'Đang cập nhật' }}</span>
            </div>
            <div class="bd-table-row">
                <span class="bd-table-key">Năm xuất bản</span>
                <span class="bd-table-val">{{ $book->publication_year ?? 'Đang cập nhật' }}</span>
            </div>
            <div class="bd-table-row">
                <span class="bd-table-key">Danh mục</span>
                <span class="bd-table-val">
                    {{ $book->categories->pluck('name')->join(', ') ?: 'Đang cập nhật' }}
                </span>
            </div>
        </div>

        {{-- Description --}}
        <div class="bd-desc-card">
            <h2 class="bd-desc-title">Mô tả sách</h2>
            @if($book->description)
                <div id="bd-desc-inner" class="bd-desc-inner clamped">
                    <p class="bd-desc-body">{{ $book->description }}</p>
                </div>
                <button id="bd-read-btn" class="bd-read-btn">Xem thêm ↓</button>
            @else
                <p class="bd-desc-empty">Chưa có mô tả chi tiết cho sản phẩm này.</p>
            @endif
        </div>

        {{-- Reviews --}}
        <div class="bd-reviews">
            <div class="bd-reviews-hd">
                <h2 class="bd-reviews-title">Đánh giá</h2>
                <div class="bd-rating-row">
                    <span class="bd-rating-big">
                        {{ number_format((float)($book->reviews_avg_rating ?? 0), 1) }}
                    </span>
                    <div>
                        <div class="bd-stars">
                            @php $avg = round((float)($book->reviews_avg_rating ?? 0)); @endphp
                            @for($s = 1; $s <= 5; $s++)
                                <span class="bd-star {{ $s <= $avg ? '' : 'e' }}">★</span>
                            @endfor
                        </div>
                        <div class="bd-rating-cnt">{{ $book->reviews_count }} đánh giá</div>
                    </div>
                </div>
            </div>

            @forelse($book->reviews as $review)
                <div class="bd-review">
                    <div class="bd-review-top">
                        <div class="bd-reviewer">
                            <div class="bd-av">
                                {{ mb_strtoupper(mb_substr($review->user?->full_name ?? 'U', 0, 1)) }}
                            </div>
                            <div>
                                <div class="bd-rv-name">
                                    {{ $review->user?->full_name ?? 'Người dùng' }}
                                </div>
                                <div class="bd-rv-date">
                                    {{ $review->created_at?->format('d/m/Y H:i') }}
                                </div>
                            </div>
                        </div>
                        <span class="bd-rv-score">{{ $review->rating }}/5 ★</span>
                    </div>
                    @if($review->comment)
                        <p class="bd-rv-comment">{{ $review->comment }}</p>
                    @endif
                </div>
            @empty
                <div class="bd-reviews-empty">
                    Chưa có đánh giá nào cho cuốn sách này.
                </div>
            @endforelse
        </div>

    </article>
</div>{{-- /.bd-grid --}}

{{-- ════════════════════════════════════════════════════════
     RELATED BOOKS
════════════════════════════════════════════════════════ --}}
<section>
    <div class="bd-related-hd">
        <h2 class="bd-related-title">Sách liên quan</h2>
        <a href="{{ route('catalog.categories') }}" class="bd-related-more">Xem thêm →</a>
    </div>

    <div class="bd-rel-grid">
        @forelse($relatedBooks as $related)
            @php
                $relatedCover = null;
                if (!empty($related->cover_image)) {
                    $relatedCover = str_starts_with($related->cover_image, 'http://') || str_starts_with($related->cover_image, 'https://')
                        ? $related->cover_image
                        : asset('storage/'.$related->cover_image);
                }
                $relInit = mb_strtoupper(mb_substr($related->title, 0, 1));
            @endphp
            <article class="bd-rel-card">
                <a href="{{ route('catalog.book', $related->slug) }}" style="text-decoration:none">
                    <div class="bd-rel-thumb">
                        @if($relatedCover)
                            <img src="{{ $relatedCover }}" alt="{{ $related->title }}" loading="lazy">
                        @else
                            <span class="bd-rel-thumb-ph">{{ $relInit }}</span>
                        @endif
                    </div>
                    <div class="bd-rel-body">
                        <p class="bd-rel-title">{{ $related->title }}</p>
                        <p class="bd-rel-author">
                            {{ $related->authors->pluck('name')->first() ?: 'Đang cập nhật tác giả' }}
                        </p>
                        <p class="bd-rel-price">
                            {{ number_format((float)($related->discount_price ?? $related->price), 0, ',', '.') }}đ
                        </p>
                    </div>
                </a>
            </article>
        @empty
            <p style="font-family:var(--cb-sans);font-size:13px;color:var(--cb-muted);grid-column:1/-1">
                Chưa có sách liên quan.
            </p>
        @endforelse
    </div>
</section>

<script>
/* ── Qty ± ───────────────────────────────────────────── */
function bdAdj(delta) {
    const inp = document.getElementById('bd-qty');
    if (!inp) return;
    const max = parseInt(inp.max, 10) || 999;
    const val = Math.min(Math.max((parseInt(inp.value, 10) || 1) + delta, 1), max);
    inp.value = val;
}

/* ── Read-more toggle ────────────────────────────────── */
(function () {
    const inner = document.getElementById('bd-desc-inner');
    const btn   = document.getElementById('bd-read-btn');
    if (!inner || !btn) return;
    if (inner.scrollHeight <= 130) {
        inner.classList.remove('clamped');
        btn.style.display = 'none';
        return;
    }
    let open = false;
    btn.addEventListener('click', function () {
        open = !open;
        inner.classList.toggle('clamped', !open);
        btn.textContent = open ? 'Thu gọn ↑' : 'Xem thêm ↓';
    });
})();
</script>

@endsection