@extends('layouts.admin', ['title' => 'Dashboard Admin'])

@section('styles')
<style>
/* ─── Design tokens ───────────────────────────────────── */
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
html, body {
    background: var(--cb-bg);
    margin: 0;
}
/* ─── Page header ─────────────────────────────────────── */
.ad-header {
    display: flex; align-items: flex-end; justify-content: space-between;
    gap: 16px; flex-wrap: wrap;
    margin-bottom: 28px;
    padding-bottom: 24px;
    border-bottom: 1px solid var(--cb-border);
}
.ad-eyebrow {
    display: inline-flex; align-items: center; gap: 7px;
    font-family: var(--cb-sans); font-size: 11px; font-weight: 600;
    letter-spacing: 1.8px; text-transform: uppercase;
    color: var(--cb-accent); background: var(--cb-accent-light);
    padding: 4px 13px; border-radius: 999px; margin-bottom: 10px;
}
.ad-eyebrow-dot {
    width: 6px; height: 6px; border-radius: 50%;
    background: var(--cb-accent);
    animation: ad-pulse 2s ease-in-out infinite;
}
@keyframes ad-pulse {
    0%,100%{ opacity:1; transform:scale(1); }
    50%{ opacity:.5; transform:scale(1.5); }
}
.ad-heading {
    font-family: normal;
    font-size: 48px; font-weight: 900; color: #0d1b10;
    letter-spacing: -0.5px; line-height: 1.08; margin: 0;
}
.ad-heading em { font-style: italic; color: var(--cb-accent); }

.ad-timestamp {
    font-family: var(--cb-sans); font-size: 12px; color: var(--cb-muted);
    margin-top: 6px;
}

.ad-header-right {
    display: flex; gap: 8px; align-items: center;
}
.ad-btn-outline {
    font-family: var(--cb-sans); font-size: 13px; font-weight: 500;
    padding: 9px 18px; border-radius: 10px;
    border: 1.5px solid var(--cb-border); background: var(--cb-white);
    color: var(--cb-muted); text-decoration: none; cursor: pointer;
    display: inline-flex; align-items: center; gap: 7px;
    transition: all .18s;
}
.ad-btn-outline:hover { border-color: var(--cb-accent); color: var(--cb-accent); }

/* ─── Stats strip ─────────────────────────────────────── */
.ad-stats-strip {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 12px;
    margin-bottom: 28px;
}
@media (max-width: 900px) { .ad-stats-strip { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 500px) { .ad-stats-strip { grid-template-columns: 1fr; } }

.ad-stat-chip {
    background: var(--cb-white); border: 1px solid var(--cb-border);
    border-radius: 14px; padding: 16px 18px;
    display: flex; flex-direction: column; gap: 4px;
    position: relative; overflow: hidden;
}
.ad-stat-chip::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 2px;
}
.ad-stat-chip.chip-users::before  { background: #6366f1; }
.ad-stat-chip.chip-books::before  { background: var(--cb-accent); }
.ad-stat-chip.chip-orders::before { background: #f59e0b; }
.ad-stat-chip.chip-revenue::before{ background: #10b981; }

.ad-stat-lbl {
    font-family: var(--cb-sans); font-size: 11px; font-weight: 600;
    letter-spacing: 1px; text-transform: uppercase; color: #b0a898;
}
.ad-stat-val {
    font-family: var(--cb-serif); font-size: 28px; font-weight: 900;
    color: var(--cb-text); letter-spacing: -.5px; line-height: 1.1;
}
.ad-stat-val.green { color: var(--cb-accent); }
.ad-stat-sub {
    font-family: var(--cb-sans); font-size: 11px; color: var(--cb-muted);
}

/* ─── Section title ───────────────────────────────────── */
.ad-section-title {
    font-family: var(--cb-sans); font-size: 11px; font-weight: 700;
    letter-spacing: 1.5px; text-transform: uppercase; color: #b0a898;
    margin-bottom: 14px;
}

/* ─── Module grid ─────────────────────────────────────── */
.ad-grid {
    display: grid;
    grid-template-columns: repeat(3, 1fr);
    gap: 14px;
}
@media (max-width: 900px) { .ad-grid { grid-template-columns: repeat(2, 1fr); } }
@media (max-width: 540px) { .ad-grid { grid-template-columns: 1fr; } }

/* Module card */
.ad-card {
    background: var(--cb-white); border: 1px solid var(--cb-border);
    border-radius: 16px; padding: 20px 22px;
    text-decoration: none; display: flex; flex-direction: column; gap: 12px;
    transition: transform .22s, box-shadow .22s, border-color .22s;
    position: relative; overflow: hidden;
}
.ad-card:hover {
    transform: translateY(-3px);
    box-shadow: 0 10px 30px rgba(0,0,0,.08);
    border-color: var(--cb-accent);
}

/* Colored left bar */
.ad-card::before {
    content: ''; position: absolute; top: 0; left: 0; bottom: 0; width: 3px;
    border-radius: 16px 0 0 16px;
    transition: width .22s;
}
.ad-card:hover::before { width: 4px; }

.ad-card-users::before   { background: #6366f1; }
.ad-card-books::before   { background: var(--cb-accent); }
.ad-card-authors::before { background: #8b5cf6; }
.ad-card-cats::before    { background: #f59e0b; }
.ad-card-pubs::before    { background: #0ea5e9; }
.ad-card-orders::before  { background: #ef4444; }
.ad-card-revenue::before { background: #10b981; }

/* Card top row */
.ad-card-top {
    display: flex; align-items: flex-start; justify-content: space-between; gap: 8px;
}

/* Icon */
.ad-card-icon {
    width: 40px; height: 40px; border-radius: 10px; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    font-size: 18px;
}
.ad-card-users   .ad-card-icon { background: #eef2ff; }
.ad-card-books   .ad-card-icon { background: var(--cb-accent-light); }
.ad-card-authors .ad-card-icon { background: #f5f3ff; }
.ad-card-cats    .ad-card-icon { background: #fffbeb; }
.ad-card-pubs    .ad-card-icon { background: #e0f2fe; }
.ad-card-orders  .ad-card-icon { background: #fff1f2; }
.ad-card-revenue .ad-card-icon { background: #ecfdf5; }

/* Count badge */
.ad-card-badge {
    font-family: var(--cb-sans); font-size: 13px; font-weight: 700;
    padding: 4px 12px; border-radius: 999px; flex-shrink: 0;
}
.ad-card-users   .ad-card-badge { background: #eef2ff; color: #4f46e5; }
.ad-card-books   .ad-card-badge { background: var(--cb-accent-light); color: var(--cb-accent); }
.ad-card-authors .ad-card-badge { background: #f5f3ff; color: #7c3aed; }
.ad-card-cats    .ad-card-badge { background: #fffbeb; color: #d97706; }
.ad-card-pubs    .ad-card-badge { background: #e0f2fe; color: #0284c7; }
.ad-card-orders  .ad-card-badge { background: #fff1f2; color: #dc2626; }
.ad-card-revenue .ad-card-badge { background: #ecfdf5; color: #059669; }

/* Card text */
.ad-card-name {
    font-family: var(--cb-sans); font-size: 15px; font-weight: 600;
    color: var(--cb-text); margin: 0;
}
.ad-card-desc {
    font-family: var(--cb-sans); font-size: 12px; color: var(--cb-muted);
    line-height: 1.5; flex: 1;
}

/* CTA row */
.ad-card-cta {
    display: flex; align-items: center; gap: 6px;
    font-family: var(--cb-sans); font-size: 12px; font-weight: 600;
    color: var(--cb-accent); margin-top: 2px;
    transition: gap .18s;
}
.ad-card:hover .ad-card-cta { gap: 10px; }
.ad-card-cta svg { transition: transform .18s; }
.ad-card:hover .ad-card-cta svg { transform: translateX(3px); }
</style>
@endsection

@section('content')
{{-- ── Page header ──────────────────────────────────────── --}}
<div class="ad-header">
    <div class="ad-header-left">
        <div class="ad-eyebrow">
            <span class="ad-eyebrow-dot"></span>
            Bảng điều khiển
        </div>
        <h1 class="ad-heading">Tổng quan <em>hệ thống</em></h1>
        <p class="ad-timestamp">
            Cập nhật lần cuối: {{ now()->format('H:i — d/m/Y') }}
        </p>
    </div>
</div>

{{-- ── Quick stats strip ────────────────────────────────── --}}
<div class="ad-stats-strip">
    <div class="ad-stat-chip chip-users">
        <span class="ad-stat-lbl">Người dùng</span>
        <span class="ad-stat-val">{{ number_format($stats['users']) }}</span>
        <span class="ad-stat-sub">Tài khoản đăng ký</span>
    </div>
    <div class="ad-stat-chip chip-books">
        <span class="ad-stat-lbl">Đầu sách</span>
        <span class="ad-stat-val">{{ number_format($stats['books']) }}</span>
        <span class="ad-stat-sub">Trong kho</span>
    </div>
    <div class="ad-stat-chip chip-orders">
        <span class="ad-stat-lbl">Đơn hàng</span>
        <span class="ad-stat-val">{{ number_format($stats['orders']) }}</span>
        <span class="ad-stat-sub">Tổng đơn</span>
    </div>
    <div class="ad-stat-chip chip-revenue">
        <span class="ad-stat-lbl">Doanh thu</span>
        <span class="ad-stat-val green">{{ number_format($stats['revenue'], 0, ',', '.') }}đ</span>
        <span class="ad-stat-sub">Tổng cộng</span>
    </div>
</div>

{{-- ── Module cards ──────────────────────────────────────── --}}
<p class="ad-section-title">Quản lý hệ thống</p>

<div class="ad-grid">

    {{-- Users --}}
    <a href="{{ route('admin.users.index') }}" class="ad-card ad-card-users">
        <div class="ad-card-top">
            <div class="ad-card-icon">👥</div>
            <span class="ad-card-badge">{{ number_format($stats['users']) }}</span>
        </div>
        <div>
            <p class="ad-card-name">Quản lý người dùng</p>
            <p class="ad-card-desc">Phân quyền và kiểm soát trạng thái tài khoản.</p>
        </div>
        <div class="ad-card-cta">
            Mở quản lý
            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <path d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
        </div>
    </a>

    {{-- Books --}}
    <a href="{{ route('admin.books.index') }}" class="ad-card ad-card-books">
        <div class="ad-card-top">
            <div class="ad-card-icon">📚</div>
            <span class="ad-card-badge">{{ number_format($stats['books']) }}</span>
        </div>
        <div>
            <p class="ad-card-name">Quản lý sách</p>
            <p class="ad-card-desc">Cập nhật giá, tồn kho và trạng thái hiển thị.</p>
        </div>
        <div class="ad-card-cta">
            Mở quản lý
            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <path d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
        </div>
    </a>

    {{-- Authors --}}
    <a href="{{ route('admin.authors.index') }}" class="ad-card ad-card-authors">
        <div class="ad-card-top">
            <div class="ad-card-icon">✍️</div>
            <span class="ad-card-badge">{{ number_format($stats['authors']) }}</span>
        </div>
        <div>
            <p class="ad-card-name">Quản lý tác giả</p>
            <p class="ad-card-desc">Cập nhật hồ sơ tác giả và kiểm soát dữ liệu liên quan.</p>
        </div>
        <div class="ad-card-cta">
            Mở quản lý
            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <path d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
        </div>
    </a>

    {{-- Categories --}}
    <a href="{{ route('admin.categories.index') }}" class="ad-card ad-card-cats">
        <div class="ad-card-top">
            <div class="ad-card-icon">🗂️</div>
            <span class="ad-card-badge">{{ number_format($stats['categories']) }}</span>
        </div>
        <div>
            <p class="ad-card-name">Quản lý danh mục</p>
            <p class="ad-card-desc">Tổ chức cây danh mục và phân loại sản phẩm.</p>
        </div>
        <div class="ad-card-cta">
            Mở quản lý
            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <path d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
        </div>
    </a>

    {{-- Publishers --}}
    <a href="{{ route('admin.publishers.index') }}" class="ad-card ad-card-pubs">
        <div class="ad-card-top">
            <div class="ad-card-icon">🏢</div>
            <span class="ad-card-badge">{{ number_format($stats['publishers']) }}</span>
        </div>
        <div>
            <p class="ad-card-name">Nhà xuất bản</p>
            <p class="ad-card-desc">Quản lý thông tin liên hệ và hệ thống đối tác.</p>
        </div>
        <div class="ad-card-cta">
            Mở quản lý
            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <path d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
        </div>
    </a>

    {{-- Orders --}}
    <a href="{{ route('admin.orders.index') }}" class="ad-card ad-card-orders">
        <div class="ad-card-top">
            <div class="ad-card-icon">📦</div>
            <span class="ad-card-badge">{{ number_format($stats['orders']) }}</span>
        </div>
        <div>
            <p class="ad-card-name">Quản lý đơn hàng</p>
            <p class="ad-card-desc">Theo dõi tiến độ xử lý và trạng thái thanh toán.</p>
        </div>
        <div class="ad-card-cta">
            Mở quản lý
            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <path d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
        </div>
    </a>

    {{-- Revenue --}}
    <a href="{{ route('admin.revenue.index') }}" class="ad-card ad-card-revenue">
        <div class="ad-card-top">
            <div class="ad-card-icon">📊</div>
            <span class="ad-card-badge">{{ number_format($stats['revenue'], 0, ',', '.') }}đ</span>
        </div>
        <div>
            <p class="ad-card-name">Thống kê doanh thu</p>
            <p class="ad-card-desc">Xem báo cáo doanh thu theo năm, tháng và phương thức.</p>
        </div>
        <div class="ad-card-cta">
            Xem thống kê
            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <path d="M5 12h14M12 5l7 7-7 7"/>
            </svg>
        </div>
    </a>

</div>

@endsection