@extends('layouts.app')

@section('title', 'CatBook — Trang chủ')

@section('styles')
<style>
*, *::before, *::after { box-sizing: border-box; }

:root {
    --cb-bg: var(--cb-brand-bg, #f8f6f1);
    --cb-border: var(--cb-brand-border, #e8e3d8);
    --cb-text: var(--cb-brand-text, #1a1a1a);
    --cb-muted: var(--cb-brand-muted, #5a5a5a);
    --cb-white: #ffffff;
    --cb-accent: var(--cb-brand-accent, #2d6a4f);
    --cb-accent-dark: var(--cb-brand-accent-dark, #1b4332);
    --cb-accent-light: var(--cb-brand-accent-light, #d8f3dc);
    --cb-serif:        'Playfair Display', Georgia, serif;
    --cb-sans:         'DM Sans', system-ui, sans-serif;
}

html,body {
    background-color: var(--cb-bg);
    }
    
/* ── Hero ─────────────────────────────────────────────── */
.cb-hero {
    max-width: 1240px; margin: 0 auto;
    padding: 40px 40px 80px;
    display: flex; flex-direction: column;
    align-items: center; text-align: center;
    position: relative;
}
/* Decorative line removed */
.cb-hero-eyebrow {
    display: inline-flex; align-items: center; gap: 8px;
    font-size: 11px; font-weight: 600; letter-spacing: 1.8px;
    text-transform: uppercase; color: var(--cb-accent);
    background: rgba(255,255,255,.78); padding: 5px 14px;
    border-radius: 999px; border: 1px solid var(--cb-border);
    box-shadow: 0 1px 0 rgba(255,255,255,.9) inset;
    margin-bottom: 22px;
}
.cb-eyebrow-dot {
    width: 6px; height: 6px; border-radius: 50%;
    background: var(--cb-accent);
    animation: pulse-dot 2s ease-in-out infinite;
}
@keyframes pulse-dot {
    0%,100%{ opacity:1; transform:scale(1); }
    50%{ opacity:.5; transform:scale(1.5); }
}
.cb-hero h1 {
    font-family: var(--cb-serif);
    font-size: 64px; font-weight: 900;
    line-height: 1.06; letter-spacing: -2.5px;
    color: var(--cb-text); margin: 0; max-width: 720px;
}
.cb-hero h1 em { font-style: italic; color: var(--cb-accent); }
.cb-hero-sub {
    margin-top: 20px; font-size: 17px; font-weight: 300;
    color: var(--cb-muted); line-height: 1.75; max-width: 560px;
}

/* Search */
.cb-search-wrap { margin-top: 36px; display: flex; gap: 10px; width: 100%; max-width: 580px; }
.cb-search-form { display: flex; gap: 10px; flex: 1; }
.cb-search-input {
    flex: 1; font-family: var(--cb-sans); font-size: 14px;
    padding: 14px 22px; border: 2px solid var(--cb-border);
    border-radius: 999px; background: var(--cb-white);
    outline: none; color: var(--cb-text); transition: border-color .2s; min-width: 0;
}
.cb-search-input:focus { border-color: var(--cb-accent); }
.cb-search-input::placeholder { color: #b0aa9e; }
.cb-search-btn {
    font-family: var(--cb-sans); font-size: 14px; font-weight: 600;
    padding: 14px 28px; border-radius: 999px;
    background: var(--cb-text); color: #fff; border: none;
    cursor: pointer; transition: background .2s, transform .15s; white-space: nowrap;
}
.cb-search-btn:hover { background: var(--cb-accent); transform: translateY(-1px); }

/* Stats (hidden, kept for Blade variables) */
.cb-hero-stats { display: none; }

/* ── Section wrapper ──────────────────────────────────── */
.cb-section { max-width: 1140px; margin: 0 auto; padding: 0 40px 72px; }
.cb-section-head {
    display: flex; align-items: center; justify-content: space-between;
    margin-bottom: 28px; flex-wrap: wrap; gap: 12px;
}
.cb-section-label {
    display: inline-flex; align-items: center; gap: 8px;
    margin-bottom: 8px;
}
.cb-section-tag {
    font-family: var(--cb-sans); font-size: 11px; font-weight: 700;
    letter-spacing: 1.4px; text-transform: uppercase;
    padding: 4px 12px; border-radius: 999px;
}
.cb-tag-bestseller { background: #fef3c7; color: #92400e; }
.cb-tag-new        { background: var(--cb-accent-light); color: var(--cb-accent); }
.cb-tag-sale       { background: #fff1f2; color: #dc2626; }

.cb-section-title {
    font-family: var(--cb-serif);
    font-size: 28px; font-weight: 700; color: #0d1b10; letter-spacing: -.5px;
    margin: 0;
}
.cb-see-all {
    font-size: 13px; font-weight: 500; color: var(--cb-accent);
    text-decoration: none; padding-bottom: 1px;
    border-bottom: 1px solid currentColor; transition: opacity .2s; white-space: nowrap;
}
.cb-see-all:hover { opacity: .65; }

/* Divider */
.cb-divider { max-width: 1140px; margin: 0 auto 72px; padding: 0 40px; }
.cb-divider hr { border: none; border-top: 1px solid #e0dbd0; }

/* ── Book product card ────────────────────────────────── */
.cb-books-grid { display: grid; grid-template-columns: repeat(4,1fr); gap: 16px; }
@media(max-width:1100px){ .cb-books-grid { grid-template-columns: repeat(3,1fr); } }
@media(max-width:768px) { .cb-books-grid { grid-template-columns: repeat(2,1fr); } }
@media(max-width:480px) { .cb-books-grid { grid-template-columns: 1fr; } }

.cb-product-card {
    background: var(--cb-white); border: 1px solid var(--cb-border);
    border-radius: 16px; overflow: hidden;
    text-decoration: none; display: block; transition: all .25s;
}
.cb-product-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 16px 40px rgba(0,0,0,.09);
    border-color: #d8d2c8;
}
.cb-product-img {
    height: 180px; background: #f0ede6;
    display: flex; align-items: center; justify-content: center;
    font-family: var(--cb-serif); font-size: 52px; font-weight: 700;
    position: relative; overflow: hidden;
}
.cb-product-img img { width: 100%; height: 100%; object-fit: cover; transition: transform .35s; }
.cb-product-card:hover .cb-product-img img { transform: scale(1.04); }
.cb-product-img.no-img { color: #c9bfa8; }

/* ribbon on image */
.cb-ribbon {
    position: absolute; top: 10px; left: 10px;
    font-family: var(--cb-sans); font-size: 10px; font-weight: 700;
    padding: 3px 10px; border-radius: 999px; letter-spacing: .6px;
}
.cb-ribbon-seller { background: #f59e0b; color: #fff; }
.cb-ribbon-new    { background: var(--cb-accent); color: #fff; }
.cb-ribbon-sale   { background: #dc2626; color: #fff; }

.cb-product-body { padding: 14px 16px; }
.cb-product-title {
    font-family: var(--cb-sans); font-size: 13px; font-weight: 600;
    color: var(--cb-text); line-height: 1.45;
    display: -webkit-box; -webkit-line-clamp: 2; line-clamp: 2; -webkit-box-orient: vertical;
    overflow: hidden; height: 2.9em;
}
.cb-product-author { font-size: 11px; color: #aaa; margin-top: 5px; }
.cb-product-footer {
    display: flex; align-items: center; justify-content: space-between;
    margin-top: 14px; padding-top: 12px; border-top: 1px solid var(--cb-border);
}
.cb-product-price { font-size: 17px; font-weight: 700; color: var(--cb-accent); }
.cb-product-orig  { font-size: 11px; color: #bbb; text-decoration: line-through; margin-left: 6px; }
.cb-add-btn {
    width: 34px; height: 34px; border-radius: 50%;
    background: var(--cb-text); color: #fff; border: none;
    font-size: 20px; cursor: pointer;
    display: flex; align-items: center; justify-content: center;
    transition: all .2s; flex-shrink: 0; line-height: 1;
}
.cb-add-btn:hover { background: var(--cb-accent); transform: scale(1.12); }

/* Empty state */
.cb-empty-state {
    grid-column: 1/-1; padding: 56px 32px; text-align: center;
    font-family: var(--cb-sans); font-size: 14px; color: #aaa;
}

/* ── Footer ───────────────────────────────────────────── */
.cb-footer {
    font-family: var(--cb-sans);
    background: var(--cb-bg); color: var(--cb-text);
    border-top: 1px solid #1e5131;
    padding: 16px 20px 18px;
}
.cb-footer-top {
    display: grid; grid-template-columns: 2fr 1fr 1fr; gap: 40px;
    max-width: 1270px; margin: 0 auto 24px;
}
@media(max-width:768px){ .cb-footer-top { grid-template-columns: 1fr; gap: 32px; } }
.cb-footer-brand { max-width: 320px; }
.cb-footer-logo {
    font-family: var(--cb-serif); font-size: 28px; font-weight: 900;
    color: #000; margin-bottom: 16px;
}
.cb-footer-logo span { color: var(--cb-accent); }
.cb-footer-desc { font-size: 14px; line-height: 1.6; color: #202221; margin-bottom: 20px; }
.cb-footer-contact p {
    margin: 0 0 8px; font-size: 14px;
    display: flex; align-items: center; gap: 8px;
}
.cb-footer-heading {
    color: #1d1c1c; font-size: 15px; font-weight: 700;
    text-transform: uppercase; letter-spacing: 1px; margin: 0 0 20px;
}
.cb-footer-links { display: flex; flex-direction: column; gap: 12px; }
.cb-footer-links a {
    color: #202221; text-decoration: none; font-size: 14px;
    transition: all .2s ease; width: fit-content;
}
.cb-footer-links a:hover { color: #0a6816; transform: translateX(4px); }
.cb-footer-bottom {
    display: flex; align-items: center; justify-content: space-between;
    max-width: 1270px; margin: 0 auto;
    font-size: 13px; color: #090909; flex-wrap: wrap; gap: 16px;
}
.cb-socials { display: flex; align-items: center; gap: 20px; }
.cb-socials a { color: #0e0e0e; text-decoration: none; font-size: 14px; transition: color .2s; }
.cb-socials a:hover { color: var(--cb-accent); }

/* ── Chatbot FAB ──────────────────────────────────────── */
.cb-chat-fab {
    position: fixed; bottom: 28px; right: 28px; z-index: 100;
    width: 58px; height: 58px; border-radius: 50%;
    background: var(--cb-accent); color: #fff;
    border: none; cursor: pointer;
    box-shadow: 0 6px 24px rgba(45,106,79,.45);
    display: flex; align-items: center; justify-content: center;
    transition: transform .22s, box-shadow .22s, background .2s;
    animation: fab-bounce 3s ease-in-out infinite;
}
.cb-chat-fab:hover {
    background: var(--cb-accent-dark);
    transform: scale(1.1);
    box-shadow: 0 10px 32px rgba(45,106,79,.55);
    animation: none;
}
@keyframes fab-bounce {
    0%,100%{ transform:translateY(0); }
    50%    { transform:translateY(-6px); }
}
.cb-chat-fab-dot {
    position: absolute; top: 4px; right: 4px;
    width: 12px; height: 12px; border-radius: 50%;
    background: #4ade80; border: 2px solid var(--cb-white);
    animation: pulse-dot 2s ease-in-out infinite;
}
.cb-chat-fab-tooltip {
    position: absolute; right: 68px; top: 50%;
    transform: translateY(-50%);
    background: #0d1b10; color: #fff;
    font-family: var(--cb-sans); font-size: 12px; font-weight: 600;
    padding: 7px 14px; border-radius: 999px; white-space: nowrap;
    pointer-events: none; opacity: 0; transition: opacity .2s;
    box-shadow: 0 4px 16px rgba(0,0,0,.2);
}
.cb-chat-fab:hover .cb-chat-fab-tooltip { opacity: 1; }

/* ── Responsive ───────────────────────────────────────── */
@media(max-width:900px){
    .cb-hero { padding: 48px 20px 56px; }
    .cb-hero h1 { font-size: 40px; letter-spacing: -1.5px; }
    .cb-search-wrap { max-width: 100%; }
    .cb-section { padding: 0 20px 56px; }
    .cb-divider { padding: 0 20px; }
}
</style>
@endsection

@section('content')

{{-- ══════════════════════════════════════════════════
     HERO
══════════════════════════════════════════════════ --}}
<section class="cb-hero">
    <h1>Tìm đúng cuốn sách<br><em>dành cho bạn</em></h1>

    <div class="cb-search-wrap">
        <form method="GET" action="{{ route('catalog.categories') }}" class="cb-search-form">
            <input name="q" type="search" value="{{ request('q') }}"
                   placeholder="Tìm sách, tác giả hoặc ISBN..."
                   class="cb-search-input"
                   required
                   oninvalid="this.setCustomValidity('Vui lòng nhập từ khóa tìm kiếm')"
                   oninput="this.setCustomValidity('')" />
            <button type="submit" class="cb-search-btn">Tìm kiếm</button>
        </form>
    </div>

    {{-- Stats (giữ biến Blade, ẩn bằng CSS) --}}
    <div class="cb-hero-stats">
        <div>
            <div class="cb-stat-num">{{ number_format($stats['books'] ?? 0) }}+</div>
            <div class="cb-stat-lbl">Đầu sách</div>
        </div>
        <div>
            <div class="cb-stat-num">{{ number_format($stats['authors'] ?? 0) }}</div>
            <div class="cb-stat-lbl">Tác giả</div>
        </div>
        <div>
            <div class="cb-stat-num">{{ number_format($stats['categories'] ?? 0) }}</div>
            <div class="cb-stat-lbl">Danh mục</div>
        </div>
    </div>
</section>

{{-- ══════════════════════════════════════════════════
     NEW BOOKS
══════════════════════════════════════════════════ --}}
<section class="cb-section">
    <div class="cb-section-head">
        <div>
            <div class="cb-section-label">
                <span class="cb-section-tag cb-tag-new">✨ Mới nhất</span>
            </div>
            <h2 class="cb-section-title">Sách mới nhập kho</h2>
        </div>
        <a href="{{ route('catalog.categories') }}?sort=newest" class="cb-see-all">Xem tất cả →</a>
    </div>

    <div class="cb-books-grid">
        @forelse(collect($newBooks ?? [])->take(4) as $book)
            @php
                $cover = null;
                if (!empty($book->cover_image)) {
                    $cover = str_starts_with($book->cover_image, 'http')
                        ? $book->cover_image
                        : asset('storage/' . $book->cover_image);
                }
                $initial  = mb_substr($book->title, 0, 1);
                $bgColors = ['#e8f5e9','#e3f2fd','#fce4ec','#f3e5f5','#fff3e0'];
                $txColors = ['#2e7d32','#1565c0','#880e4f','#4a148c','#e65100'];
                $ci       = $loop->index % 5;
                $styleAttr = $cover ? '' : 'style="background:'.$bgColors[$ci].';color:'.$txColors[$ci].'"';
            @endphp
            <a href="{{ route('catalog.book', $book->slug) }}" class="cb-product-card">
                <div class="cb-product-img {{ $cover ? '' : 'no-img' }}" {!! $styleAttr !!}>
                    @if($cover)
                        <img src="{{ $cover }}" alt="{{ $book->title }}">
                    @else
                        {{ $initial }}
                    @endif
                    <span class="cb-ribbon cb-ribbon-new">MỚI</span>
                </div>
                <div class="cb-product-body">
                    <div class="cb-product-title">{{ $book->title }}</div>
                    <div class="cb-product-author">{{ $book->authors->pluck('name')->first() ?? 'Đang cập nhật' }}</div>
                    <div class="cb-product-footer">
                        <div>
                            <span class="cb-product-price">
                                {{ number_format((float)($book->discount_price ?? $book->price), 0, ',', '.') }}đ
                            </span>
                            @if($book->discount_price && $book->discount_price < $book->price)
                                <span class="cb-product-orig">{{ number_format((float)$book->price, 0, ',', '.') }}đ</span>
                            @endif
                        </div>
                        <button type="button" class="cb-add-btn"
                                data-cart-url="{{ route('cart.store', $book->slug) }}"
                                onclick="submitAddToCart(event, this.dataset.cartUrl)"
                                title="Thêm vào giỏ">+</button>
                    </div>
                </div>
            </a>
        @empty
            <div class="cb-empty-state">Chưa có sách mới nhập kho.</div>
        @endforelse
    </div>
</section>

<div class="cb-divider"><hr></div>

{{-- ══════════════════════════════════════════════════
     DISCOUNT BOOKS
══════════════════════════════════════════════════ --}}
<section class="cb-section">
    <div class="cb-section-head">
        <div>
            <div class="cb-section-label">
                <span class="cb-section-tag cb-tag-sale">🔥 Giảm giá</span>
            </div>
            <h2 class="cb-section-title">Sách đang giảm giá</h2>
        </div>
        <a href="{{ route('catalog.categories') }}?sort=discount" class="cb-see-all">Xem tất cả →</a>
    </div>

    <div class="cb-books-grid">
        @forelse(collect($discountBooks ?? [])->take(8) as $book)
            @php
                $cover = null;
                if (!empty($book->cover_image)) {
                    $cover = str_starts_with($book->cover_image, 'http')
                        ? $book->cover_image
                        : asset('storage/' . $book->cover_image);
                }
                $initial  = mb_substr($book->title, 0, 1);
                $bgColors = ['#fce4ec','#fff3e0','#e8f5e9','#e3f2fd','#f3e5f5'];
                $txColors = ['#880e4f','#e65100','#2e7d32','#1565c0','#4a148c'];
                $ci       = $loop->index % 5;
                $discountPct = ($book->discount_price && $book->price > 0)
                    ? round((1 - (float)$book->discount_price / (float)$book->price) * 100)
                    : 0;
                $styleAttr = $cover ? '' : 'style="background:'.$bgColors[$ci].';color:'.$txColors[$ci].'"';
            @endphp
            <a href="{{ route('catalog.book', $book->slug) }}" class="cb-product-card">
                <div class="cb-product-img {{ $cover ? '' : 'no-img' }}" {!! $styleAttr !!}>
                    @if($cover)
                        <img src="{{ $cover }}" alt="{{ $book->title }}">
                    @else
                        {{ $initial }}
                    @endif
                    @if($discountPct > 0)
                        <span class="cb-ribbon cb-ribbon-sale">-{{ $discountPct }}%</span>
                    @endif
                </div>
                <div class="cb-product-body">
                    <div class="cb-product-title">{{ $book->title }}</div>
                    <div class="cb-product-author">{{ $book->authors->pluck('name')->first() ?? 'Đang cập nhật' }}</div>
                    <div class="cb-product-footer">
                        <div>
                            <span class="cb-product-price">
                                {{ number_format((float)($book->discount_price ?? $book->price), 0, ',', '.') }}đ
                            </span>
                            @if($book->discount_price && $book->discount_price < $book->price)
                                <span class="cb-product-orig">{{ number_format((float)$book->price, 0, ',', '.') }}đ</span>
                            @endif
                        </div>
                        <button type="button" class="cb-add-btn"
                                data-cart-url="{{ route('cart.store', $book->slug) }}"
                                onclick="submitAddToCart(event, this.dataset.cartUrl)"
                                title="Thêm vào giỏ">+</button>
                    </div>
                </div>
            </a>
        @empty
            <div class="cb-empty-state">Hiện chưa có sách nào đang giảm giá.</div>
        @endforelse
    </div>
</section>

{{-- Footer is now rendered site-wide in layouts.app; removed duplicate here. --}}

@endsection

@section('scripts')
<script>
/* ── Add to cart ──────────────────────────────────────── */
function submitAddToCart(event, actionUrl) {
    event.preventDefault();
    event.stopPropagation();
    const csrf = document.querySelector('meta[name="csrf-token"]')?.content;
    if (!csrf) return;
    const form = document.createElement('form');
    form.method = 'POST'; form.action = actionUrl; form.style.display = 'none';
    const t = document.createElement('input'); t.type='hidden'; t.name='_token'; t.value=csrf;
    const q = document.createElement('input'); q.type='hidden'; q.name='quantity'; q.value='1';
    form.appendChild(t); form.appendChild(q);
    document.body.appendChild(form); form.submit();
}
</script>

@endsection