@extends('layouts.app')

@section('title', 'CatBook — Trang chủ')

@section('styles')
    <style>
        /* ─── Reset & Base ─────────────────────────────────────── */
        *, *::before, *::after { box-sizing: border-box; }

        :root {
            --cb-bg: var(--cb-brand-bg);
            --cb-surface: var(--cb-brand-surface);
            --cb-border: var(--cb-brand-border);
            --cb-text: var(--cb-brand-text);
            --cb-muted: var(--cb-brand-muted);
            --cb-accent: var(--cb-brand-accent);
            --cb-accent-dark: var(--cb-brand-accent-dark);
        }

        body {
            font-family: 'DM Sans', sans-serif;
            background: var(--cb-bg);
            color: var(--cb-text);
            margin: 0;
        }

        /* ─── Hero ──────────────────────────────────────────────── */
        .cb-hero {
            max-width: 1240px; margin: 0 auto;
            padding: 52px 40px 64px;
            display: grid; grid-template-columns: minmax(0, 1fr) 400px; gap: 56px; align-items: center;
            position: relative;
        }
        .cb-hero::before {
            content: '';
            position: absolute;
            top: 18px;
            left: 40px;
            right: 40px;
            height: 1px;
            background: linear-gradient(90deg, rgba(45,106,79,0), var(--cb-brand-accent-tint-strong), rgba(45,106,79,0));
            pointer-events: none;
        }
        .cb-hero-eyebrow {
            display: inline-flex; align-items: center; gap: 8px;
            font-size: 11px; font-weight: 600; letter-spacing: 1.8px;
            text-transform: uppercase; color: var(--cb-accent);
            background: rgba(255,255,255,0.78); padding: 5px 14px; border-radius: 999px;
            border: 1px solid var(--cb-border);
            box-shadow: 0 1px 0 rgba(255,255,255,0.9) inset;
            margin-bottom: 22px;
        }
        .cb-eyebrow-dot {
            width: 6px; height: 6px; border-radius: 50%;
            background: var(--cb-accent);
            animation: pulse-dot 2s ease-in-out infinite;
        }
        @keyframes pulse-dot {
            0%,100%{ opacity:1; transform:scale(1); }
            50%{ opacity:0.5; transform:scale(1.5); }
        }
        .cb-hero h1 {
            font-family: 'Playfair Display', serif;
            font-size: 56px; font-weight: 900;
            line-height: 1.06; letter-spacing: -2px;
            color: var(--cb-text); margin: 0;
        }
        .cb-hero h1 em { font-style: italic; color: var(--cb-accent); }
        .cb-hero-sub {
            margin-top: 20px; font-size: 16px; font-weight: 300;
            color: var(--cb-muted); line-height: 1.75; max-width: 460px;
        }

        /* Search bar */
        .cb-search-wrap { margin-top: 32px; display: flex; gap: 10px; }
        .cb-search-form { display: flex; gap: 10px; flex: 1; }
        .cb-search-input {
            flex: 1; font-family: 'DM Sans', sans-serif;
            font-size: 14px; padding: 14px 22px;
            border: 2px solid var(--cb-border); border-radius: 999px;
            background: var(--cb-brand-surface-strong); outline: none; color: var(--cb-text);
            transition: border-color 0.2s; min-width: 0;
        }
        .cb-search-input:focus { border-color: var(--cb-accent); }
        .cb-search-input::placeholder { color: #b0aa9e; }
        .cb-search-btn {
            font-family: 'DM Sans', sans-serif;
            font-size: 14px; font-weight: 600;
            padding: 14px 28px; border-radius: 999px;
            background: var(--cb-text); color: #fff;
            border: none; cursor: pointer;
            transition: background 0.2s, transform 0.15s; white-space: nowrap;
        }
        .cb-search-btn:hover { background: var(--cb-accent); transform: translateY(-1px); }

        /* Stats */
        .cb-hero-stats {
            display: flex; gap: 32px; margin-top: 32px;
            padding-top: 28px; border-top: 1px solid var(--cb-border);
        }
        .cb-stat-num {
            font-family: 'Playfair Display', serif;
            font-size: 28px; font-weight: 700; color: var(--cb-text);
            line-height: 1;
        }
        .cb-stat-lbl { font-size: 12px; color: var(--cb-muted); margin-top: 4px; }

        /* Hero visual — floating book cards */
        .cb-hero-visual { position: relative; display: flex; flex-direction: column; gap: 12px; }
        .cb-ai-badge {
            position: absolute; top: -10px; right: -8px;
            background: var(--cb-text); color: #fff;
            font-size: 11px; font-weight: 600;
            padding: 9px 16px; border-radius: 999px;
            box-shadow: 0 6px 20px rgba(0,0,0,0.22);
            display: flex; align-items: center; gap: 7px;
            animation: cb-float 3s ease-in-out infinite; z-index: 10;
        }
        @keyframes cb-float { 0%,100%{transform:translateY(0)} 50%{transform:translateY(-8px)} }
        .cb-ai-dot {
            width: 8px; height: 8px; border-radius: 50%;
            background: #4ade80;
            animation: pulse-dot 1.5s ease-in-out infinite;
        }
        .cb-book-card {
            background: var(--cb-brand-surface-strong); border-radius: 16px;
            padding: 16px 18px; display: flex; gap: 14px; align-items: center;
            box-shadow: 0 2px 14px rgba(0,0,0,0.06);
            border: 1px solid var(--cb-border);
            transition: transform 0.25s, box-shadow 0.25s; cursor: pointer;
        }
        .cb-book-card:nth-child(2) { margin-left: 24px; }
        .cb-book-card:nth-child(3) { margin-left: 12px; }
        .cb-book-card:hover { transform: translateX(5px) translateY(-2px); box-shadow: 0 8px 28px rgba(0,0,0,0.1); }
        .cb-book-thumb {
            width: 50px; height: 70px; border-radius: 8px;
            flex-shrink: 0; display: flex; align-items: center; justify-content: center;
            font-family: 'Playfair Display', serif; font-size: 24px; font-weight: 900;
        }
        .cb-book-thumb.t1 { background: var(--cb-brand-accent-soft); color: var(--cb-accent-dark); }
        .cb-book-thumb.t2 { background: #fde8d8; color: #7c3d12; }
        .cb-book-thumb.t3 { background: #e0eaff; color: #1e3a8a; }
        .cb-book-info { flex: 1; min-width: 0; }
        .cb-book-title { font-size: 13px; font-weight: 600; color: var(--cb-text); line-height: 1.35; }
        .cb-book-author { font-size: 11px; color: #999; margin-top: 3px; }
        .cb-book-price { font-size: 15px; font-weight: 700; color: var(--cb-accent); margin-top: 7px; }
        .cb-badge-hot {
            background: var(--cb-accent); color: #fff;
            font-size: 10px; font-weight: 700; padding: 3px 9px; border-radius: 999px;
            letter-spacing: 0.5px; white-space: nowrap; flex-shrink: 0;
        }
        .cb-badge-new {
            background: var(--cb-text); color: #fff;
            font-size: 10px; font-weight: 700; padding: 3px 9px; border-radius: 999px;
            letter-spacing: 0.5px; white-space: nowrap; flex-shrink: 0;
        }

        /* ─── Section wrapper ───────────────────────────────────── */
        .cb-section { max-width: 1140px; margin: 0 auto; padding: 0 40px 72px; }
        .cb-section-head {
            display: flex; align-items: baseline; justify-content: space-between;
            margin-bottom: 28px;
        }
        .cb-section-title {
            font-family: 'Playfair Display', serif;
            font-size: 28px; font-weight: 700; color: #0d1b10;
            letter-spacing: -0.5px;
        }
        .cb-see-all {
            font-size: 13px; font-weight: 500; color: var(--cb-accent);
            text-decoration: none;
            padding-bottom: 1px; border-bottom: 1px solid currentColor;
            transition: opacity 0.2s;
        }
        .cb-see-all:hover { opacity: 0.65; }

        /* ─── Divider ───────────────────────────────────────────── */
        .cb-divider { max-width: 1140px; margin: 0 auto 72px; padding: 0 40px; }
        .cb-divider hr { border: none; border-top: 1px solid #e0dbd0; }

        /* ─── Categories ────────────────────────────────────────── */
        .cb-cat-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; }
        @media(max-width: 768px) { .cb-cat-grid { grid-template-columns: repeat(2, 1fr); } }
        .cb-cat-card {
            background: #fff; border: 1px solid #ede9de;
            border-radius: 16px; padding: 24px 18px;
            text-align: center; cursor: pointer;
            transition: all 0.22s; text-decoration: none; display: block;
        }
        .cb-cat-card:hover {
            background: var(--cb-accent); border-color: var(--cb-accent);
            transform: translateY(-4px);
            box-shadow: 0 12px 32px rgba(45,106,79,0.2);
        }
        .cb-cat-card:hover .cb-cat-name { color: #fff; }
        .cb-cat-card:hover .cb-cat-count { color: rgba(255,255,255,0.65); }
        .cb-cat-icon { font-size: 28px; margin-bottom: 12px; line-height: 1; }
        .cb-cat-name { font-size: 14px; font-weight: 600; color: var(--cb-text); transition: color 0.22s; }
        .cb-cat-count { font-size: 12px; color: #aaa; margin-top: 5px; transition: color 0.22s; }

        /* ─── Book cards ────────────────────────────────────────── */
        .cb-books-grid { display: grid; grid-template-columns: repeat(3, 1fr); gap: 18px; }
        @media(max-width: 900px) { .cb-books-grid { grid-template-columns: repeat(2, 1fr); } }
        @media(max-width: 600px) { .cb-books-grid { grid-template-columns: 1fr; } }

        .cb-product-card {
            background: #fff; border: 1px solid #ede9de;
            border-radius: 18px; overflow: hidden;
            text-decoration: none; display: block;
            transition: all 0.25s;
        }
        .cb-product-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 48px rgba(0,0,0,0.1);
        }
        .cb-product-img {
            height: 200px; background: #f0ede6;
            display: flex; align-items: center; justify-content: center;
            font-family: 'Playfair Display', serif;
            font-size: 56px; font-weight: 700;
            position: relative; overflow: hidden;
        }
        /* When there's a real image */
        .cb-product-img img { width: 100%; height: 100%; object-fit: cover; }
        /* Color variants for placeholder */
        .cb-product-img.no-img { color: #c9bfa8; }
        .cb-ribbon {
            position: absolute; top: 12px; left: 12px;
            background: var(--cb-text); color: #fff;
            font-size: 10px; font-weight: 700; padding: 4px 12px;
            border-radius: 999px; letter-spacing: 0.8px;
        }
        .cb-product-body { padding: 18px; }
        .cb-product-title {
            font-size: 15px; font-weight: 600; color: var(--cb-text); line-height: 1.45;
            display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;
            line-clamp: 2; overflow: hidden;
        }
        .cb-product-author { font-size: 12px; color: #aaa; margin-top: 6px; }
        .cb-product-footer {
            display: flex; align-items: center; justify-content: space-between;
            margin-top: 16px; padding-top: 14px; border-top: 1px solid #f0ede6;
        }
        .cb-product-price { font-size: 18px; font-weight: 700; color: var(--cb-accent); }
        .cb-product-orig {
            font-size: 12px; color: #bbb; text-decoration: line-through; margin-left: 7px;
        }
        .cb-add-btn {
            width: 36px; height: 36px; border-radius: 50%;
            background: var(--cb-accent); color: #fff; border: none;
            font-size: 20px; cursor: pointer;
            display: flex; align-items: center; justify-content: center;
            transition: all 0.2s; flex-shrink: 0; line-height: 1;
        }
        .cb-add-btn:hover { background: var(--cb-accent-dark); transform: scale(1.12); }

        /* ─── CTA Strip ─────────────────────────────────────────── */
        .cb-cta { max-width: 1140px; margin: 0 auto 80px; padding: 0 40px; }
        .cb-cta-inner {
            background: #0d1b10; border-radius: 26px;
            padding: 56px 64px;
            display: flex; align-items: center; justify-content: space-between; gap: 40px;
        }
        .cb-cta-text h3 {
            font-family: 'Playfair Display', serif;
            font-size: 34px; font-weight: 700; color: #fff;
            letter-spacing: -0.5px; line-height: 1.2; margin: 0;
        }
        .cb-cta-text h3 em { font-style: italic; color: #4ade80; }
        .cb-cta-text p { font-size: 15px; color: #6a9e7a; margin: 10px 0 0; }
        .cb-cta-btn {
            font-family: 'DM Sans', sans-serif;
            font-size: 15px; font-weight: 600;
            padding: 16px 34px; border-radius: 999px;
            background: #4ade80; color: #0d1b10;
            border: none; cursor: pointer; white-space: nowrap;
            flex-shrink: 0; transition: all 0.2s;
        }
        .cb-cta-btn:hover { background: #86efac; transform: translateY(-2px); }

        /* ─── Footer ────────────────────────────────────────────── */
        .cb-footer {
            background: #0d1b10; color: #4a6455;
            padding: 32px 40px; font-size: 13px;
            display: flex; align-items: center; justify-content: space-between;
        }
        .cb-footer-logo {
            font-family: 'Playfair Display', serif;
            font-size: 18px; font-weight: 900; color: #fff;
        }
        .cb-footer-logo span { color: #4ade80; }
        .cb-footer-links { display: flex; gap: 24px; }
        .cb-footer-links a { color: #4a6455; text-decoration: none; font-size: 13px; transition: color 0.2s; }
        .cb-footer-links a:hover { color: #6a9e7a; }

        /* ─── Responsive ────────────────────────────────────────── */
        @media(max-width: 900px) {
            .cb-hero { grid-template-columns: 1fr; gap: 48px; }
            .cb-hero-visual { display: none; }
            .cb-hero h1 { font-size: 42px; }
            .cb-cta-inner { flex-direction: column; text-align: center; padding: 40px 32px; }
        }
    </style>
@endsection

@section('content')
        {{-- ── Hero ───────────────────────────────────────────────── --}}
        <section class="cb-hero">
            {{-- Left copy --}}
            <div class="cb-hero-left">
                <div class="cb-hero-eyebrow">
                    <span class="cb-eyebrow-dot"></span>
                    Kho sách trực tuyến
                </div>
                <h1>Tìm đúng cuốn sách<br><em>dành cho bạn</em></h1>
                <p class="cb-hero-sub">Hàng nghìn đầu sách, gợi ý thông minh từ AI. Khám phá tri thức theo cách của bạn.</p>

                <div class="cb-search-wrap">
                    <form method="GET" action="{{ route('catalog.categories') }}" class="cb-search-form">
                        <input name="q" type="search" value="{{ request('q') }}"
                               placeholder="Tìm sách, tác giả hoặc ISBN..."
                               class="cb-search-input" />
                        <button type="submit" class="cb-search-btn">Tìm kiếm</button>
                    </form>
                </div>

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
            </div>

            {{-- Right — floating book cards (decorative) --}}
            <div class="cb-hero-visual">
                <div class="cb-ai-badge">
                    <span class="cb-ai-dot"></span>AI đang hoạt động
                </div>

                @forelse(($featuredBooks ?? collect())->take(3) as $hb)
                    @php
                        $initial = mb_substr($hb->title, 0, 1);
                        $colors  = ['t1','t2','t3'];
                        $ci      = $loop->index % 3;
                    @endphp
                    <a href="{{ route('catalog.book', $hb->slug) }}" class="cb-book-card" style="text-decoration:none">
                        <div class="cb-book-thumb {{ $colors[$ci] }}">{{ $initial }}</div>
                        <div class="cb-book-info">
                            <div class="cb-book-title">{{ Str::limit($hb->title, 40) }}</div>
                            <div class="cb-book-author">{{ $hb->authors->pluck('name')->first() ?? 'Đang cập nhật' }}</div>
                            <div class="cb-book-price">{{ number_format($hb->discount_price ?? $hb->price, 0, ',', '.') }}đ</div>
                        </div>
                        @if($loop->first)
                            <span class="cb-badge-hot">HOT</span>
                        @elseif($loop->last)
                            <span class="cb-badge-new">MỚI</span>
                        @endif
                    </a>
                @empty
                    {{-- Placeholder cards khi chưa có data --}}
                    <div class="cb-book-card">
                        <div class="cb-book-thumb t1">S</div>
                        <div class="cb-book-info">
                            <div class="cb-book-title">Sapiens: Lược Sử Loài Người</div>
                            <div class="cb-book-author">Yuval Noah Harari</div>
                            <div class="cb-book-price">148.000đ</div>
                        </div>
                        <span class="cb-badge-hot">HOT</span>
                    </div>
                @endforelse
            </div>
        </section>

        {{-- ── Categories ─────────────────────────────────────────── --}}
        <section class="cb-section">
            <div class="cb-section-head">
                <h2 class="cb-section-title">Danh mục nổi bật</h2>
                <a href="{{ route('catalog.categories') }}" class="cb-see-all">Xem tất cả →</a>
            </div>

            @php
                $catIcons = ['📚','🧠','💼','🔬','🎨','📖','💡','🌍'];
            @endphp
            <div class="cb-cat-grid">
                @forelse($topCategories ?? [] as $cat)
                    <a href="{{ route('catalog.category', $cat->slug) }}" class="cb-cat-card">
                        <div class="cb-cat-icon">{{ $catIcons[$loop->index % count($catIcons)] }}</div>
                        <div class="cb-cat-name">{{ $cat->name }}</div>
                        <div class="cb-cat-count">{{ number_format($cat->books_count ?? 0) }} cuốn</div>
                    </a>
                @empty
                    <div style="grid-column:1/-1;text-align:center;padding:40px 0;color:#aaa;font-size:14px">
                        Chưa có danh mục để hiển thị.
                    </div>
                @endforelse
            </div>
        </section>

        <div class="cb-divider"><hr></div>

        {{-- ── Featured Books ──────────────────────────────────────── --}}
        <section class="cb-section">
            <div class="cb-section-head">
                <h2 class="cb-section-title">Sách nổi bật</h2>
            </div>

            <div class="cb-books-grid">
                @forelse($featuredBooks ?? [] as $book)
                    @php
                        $cover = null;
                        if (!empty($book->cover_image)) {
                            $cover = str_starts_with($book->cover_image, 'http')
                                ? $book->cover_image
                                : asset('storage/' . $book->cover_image);
                        }
                        $initial  = mb_substr($book->title, 0, 1);
                        $bgColors = ['#e8f5e9','#fff3e0','#e3f2fd','#fce4ec','#f3e5f5'];
                        $txColors = ['#2e7d32','#e65100','#1565c0','#880e4f','#4a148c'];
                        $ci       = $loop->index % 5;
                        $ribbons  = ['BESTSELLER','PHỔ BIẾN','MỚI','HOT','NỔI BẬT'];
                    @endphp
                    <a href="{{ route('catalog.book', $book->slug) }}" class="cb-product-card">
                        <div class="cb-product-img {{ $cover ? '' : 'no-img' }}"
                             @if(!$cover)style="background:{{ $bgColors[$ci] }};color:{{ $txColors[$ci] }}"@endif>
                            @if($cover)
                                <img src="{{ $cover }}" alt="{{ $book->title }}">
                            @else
                                {{ $initial }}
                            @endif
                            <span class="cb-ribbon">{{ $ribbons[$ci] }}</span>
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
                                {{-- nút giỏ hàng: gọi route add-to-cart hoặc xử lý bằng JS --}}
                                <button class="cb-add-btn"
                                        onclick="event.preventDefault(); addToCart('{{ $book->slug }}')"
                                        title="Thêm vào giỏ">+</button>
                            </div>
                        </div>
                    </a>
                @empty
                    <div style="grid-column:1/-1;text-align:center;padding:60px 0;color:#aaa;font-size:14px">
                        Chưa có sách nổi bật.
                    </div>
                @endforelse
            </div>
        </section>

        {{-- ── CTA Strip ───────────────────────────────────────────── --}}
        <div class="cb-cta">
            <div class="cb-cta-inner">
                <div class="cb-cta-text">
                    <h3>Để AI gợi ý cuốn sách<br><em>hoàn hảo</em> cho bạn</h3>
                    <p>Chỉ cần nói cho chatbot biết bạn muốn đọc gì — mọi thứ còn lại để CatBook lo.</p>
                </div>
                <button id="open-chat-btn" class="cb-cta-btn">Mở trợ lý AI ✦</button>
            </div>
        </div>
    </section>

    {{-- ── Footer ──────────────────────────────────────────────────── --}}
    <footer class="cb-footer">
        <div class="cb-footer-logo">Cat<span>Book</span></div>
        <div class="cb-footer-links">
            <a href="#">Chính sách</a>
            <a href="#">Liên hệ</a>
            <a href="#">Facebook</a>
        </div>
        <div>© {{ date('Y') }} CatBook</div>
    </footer>

    <script>
        // Mở chatbot
        document.getElementById('open-chat-btn')?.addEventListener('click', function () {
            const toggle = document.getElementById('chat-toggle-btn');
            if (toggle) toggle.click();
        });

        // Thêm vào giỏ hàng (AJAX)
        function addToCart(bookSlug) {
            fetch('/gio-hang/' + bookSlug, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content
                },
                body: JSON.stringify({ quantity: 1 })
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    // Cập nhật badge giỏ hàng nếu cần
                    const badge = document.querySelector('.cb-cart-badge');
                    if (badge && data.cart_count) badge.textContent = data.cart_count;
                }
            })
            .catch(console.error);
        }
    </script>
@endsection