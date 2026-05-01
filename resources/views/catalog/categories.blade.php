<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $heading ?? 'Danh mục sách' }} — CatBook</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
    *, *::before, *::after { box-sizing: border-box; }

    body {
        font-family: var(--cb-font-sans);
        margin: 0;
        min-height: 100vh;
    }

    .cat-page {
        padding: 40px 0 80px;
    }

    @media (max-width: 768px) {
        .cat-page { padding: 24px 16px 60px; }
    }

    .cat-hero {
        background: var(--cb-brand-white);
        border: 1px solid var(--cb-brand-border);
        border-radius: 20px;
        padding: 32px 36px;
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 28px;
        flex-wrap: wrap;
        margin-bottom: 28px;
    }

    .cat-breadcrumb {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        color: var(--cb-brand-muted);
        margin-bottom: 10px;
        flex-wrap: wrap;
    }

    .cat-breadcrumb a { color: var(--cb-brand-muted); text-decoration: none; }
    .cat-breadcrumb a:hover { color: var(--cb-brand-accent); }
    .cat-breadcrumb span { opacity: 0.5; }

    .cat-hero-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        font-size: 11px;
        font-weight: 600;
        letter-spacing: 1.6px;
        text-transform: uppercase;
        color: var(--cb-brand-accent);
        background: var(--cb-brand-accent-light);
        padding: 4px 12px;
        border-radius: 999px;
        margin-bottom: 10px;
    }

    .cat-hero h1 {
        font-family: var(--cb-font-serif);
        font-size: 36px;
        font-weight: 900;
        color: #0d1b10;
        letter-spacing: -1px;
        line-height: 1.1;
        margin: 0 0 14px;
    }

    .cat-hero-meta {
        display: flex;
        align-items: center;
        gap: 10px;
        flex-wrap: wrap;
    }

    .cat-meta-pill {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
        color: var(--cb-brand-muted);
        background: var(--cb-brand-bg);
        border: 1px solid var(--cb-brand-border);
        padding: 5px 14px;
        border-radius: 999px;
    }

    .cat-meta-pill strong { color: var(--cb-brand-text); font-weight: 600; }

    .cat-search-form {
        display: flex;
        gap: 0;
        border: 2px solid var(--cb-brand-border);
        border-radius: 12px;
        overflow: hidden;
        background: var(--cb-brand-white);
        transition: border-color 0.2s;
        min-width: 300px;
        flex: 1;
        max-width: 420px;
    }

    .cat-search-form:focus-within { border-color: var(--cb-brand-accent); }

    .cat-search-input {
        flex: 1;
        font-family: var(--cb-font-sans);
        font-size: 14px;
        padding: 12px 18px;
        border: none;
        outline: none;
        background: transparent;
        color: var(--cb-brand-text);
        min-width: 0;
    }

    .cat-search-input::placeholder { color: #b5b0a8; }

    .cat-search-btn {
        font-family: var(--cb-font-sans);
        font-size: 13px;
        font-weight: 600;
        padding: 12px 22px;
        border: none;
        background: var(--cb-brand-text);
        color: #fff;
        cursor: pointer;
        white-space: nowrap;
        transition: background 0.2s;
    }

    .cat-search-btn:hover { background: var(--cb-brand-accent); }

    .cat-parents {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 28px;
    }

    .cat-parent-pill {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        font-family: var(--cb-font-sans);
        font-size: 13px;
        font-weight: 500;
        padding: 8px 18px;
        border-radius: 999px;
        border: 1.5px solid var(--cb-brand-border);
        background: var(--cb-brand-white);
        color: var(--cb-brand-muted);
        text-decoration: none;
        transition: all 0.2s;
        white-space: nowrap;
    }

    .cat-parent-pill:hover {
        border-color: var(--cb-brand-accent);
        color: var(--cb-brand-accent);
    }

    .cat-parent-pill.active {
        background: var(--cb-brand-accent);
        border-color: var(--cb-brand-accent);
        color: #fff;
    }

    .cat-parent-pill .cnt {
        font-size: 11px;
        font-weight: 600;
        padding: 1px 7px;
        border-radius: 999px;
        background: rgba(0,0,0,0.12);
        color: inherit;
    }

    .cat-parent-pill.active .cnt { background: rgba(255,255,255,0.25); }

    .cat-layout {
        display: grid;
        grid-template-columns: 260px 1fr;
        gap: 24px;
        align-items: start;
    }

    @media (max-width: 900px) { .cat-layout { grid-template-columns: 1fr; } }

    .cat-sidebar { display: flex; flex-direction: column; gap: 16px; }

    .cat-card {
        background: var(--cb-brand-white);
        border: 1px solid var(--cb-brand-border);
        border-radius: 16px;
        padding: 20px;
    }

    .cat-card-title {
        font-size: 11px;
        font-weight: 600;
        letter-spacing: 1.4px;
        text-transform: uppercase;
        color: #a09890;
        margin: 0 0 14px;
    }

    .cat-child-list { display: flex; flex-direction: column; gap: 2px; }

    .cat-child-link {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 9px 12px;
        border-radius: 10px;
        font-size: 13px;
        font-weight: 500;
        color: #444;
        text-decoration: none;
        transition: background 0.15s, color 0.15s;
    }

    .cat-child-link:hover { background: var(--cb-brand-bg); color: var(--cb-brand-accent); }

    .cat-child-link.active {
        background: var(--cb-brand-accent-light);
        color: var(--cb-brand-accent);
        font-weight: 600;
    }

    .cat-child-cnt {
        font-size: 11px;
        font-weight: 600;
        padding: 2px 8px;
        border-radius: 999px;
        background: var(--cb-brand-bg);
        color: #888;
        border: 1px solid var(--cb-brand-border);
        flex-shrink: 0;
    }

    .cat-child-link.active .cat-child-cnt {
        background: rgba(45,106,79,0.12);
        color: var(--cb-brand-accent);
        border-color: transparent;
    }

    .cat-filter-section { margin-bottom: 20px; }
    .cat-filter-section:last-child { margin-bottom: 0; }

    .cat-filter-label {
        font-size: 13px;
        font-weight: 600;
        color: var(--cb-brand-text);
        margin-bottom: 10px;
        display: block;
    }

    .cat-range-wrap { display: flex; flex-direction: column; gap: 8px; }

    .cat-range-input {
        width: 100%;
        accent-color: var(--cb-brand-accent);
        cursor: pointer;
    }

    .cat-range-display {
        display: flex;
        justify-content: space-between;
        font-size: 12px;
        font-weight: 600;
        color: var(--cb-brand-text);
        background: var(--cb-brand-bg);
        border: 1px solid var(--cb-brand-border);
        border-radius: 8px;
        padding: 7px 12px;
    }

    .cat-select {
        width: 100%;
        font-family: var(--cb-font-sans);
        font-size: 13px;
        padding: 10px 14px;
        border: 1.5px solid var(--cb-brand-border);
        border-radius: 10px;
        background: var(--cb-brand-white);
        color: var(--cb-brand-text);
        outline: none;
        cursor: pointer;
        transition: border-color 0.2s;
    }

    .cat-select:focus { border-color: var(--cb-brand-accent); }

    .cat-radio-group { display: flex; flex-direction: column; gap: 8px; }

    .cat-radio-label {
        display: flex;
        align-items: center;
        gap: 9px;
        font-size: 13px;
        color: #444;
        cursor: pointer;
    }

    .cat-radio-label input { accent-color: var(--cb-brand-accent); }

    .cat-filter-divider {
        height: 1px;
        background: var(--cb-brand-border);
        margin: 18px 0;
    }

    .cat-filter-actions { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-top: 20px; }

    .cat-btn-primary {
        font-family: var(--cb-font-sans);
        font-size: 13px;
        font-weight: 600;
        padding: 10px;
        border-radius: 10px;
        border: none;
        background: var(--cb-brand-text);
        color: #fff;
        cursor: pointer;
        transition: background 0.2s;
        text-align: center;
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .cat-btn-primary:hover { background: var(--cb-brand-accent); }

    .cat-btn-ghost {
        font-family: var(--cb-font-sans);
        font-size: 13px;
        font-weight: 500;
        padding: 10px;
        border-radius: 10px;
        border: 1.5px solid var(--cb-brand-border);
        background: transparent;
        color: var(--cb-brand-muted);
        cursor: pointer;
        transition: all 0.2s;
        text-align: center;
        text-decoration: none;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .cat-btn-ghost:hover { border-color: var(--cb-brand-text); color: var(--cb-brand-text); }

    .cat-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
        background: var(--cb-brand-white);
        border: 1px solid var(--cb-brand-border);
        border-radius: 14px;
        padding: 12px 18px;
        margin-bottom: 18px;
    }

    .cat-toolbar-count {
        font-size: 13px;
        color: var(--cb-brand-muted);
    }

    .cat-toolbar-count strong { color: var(--cb-brand-text); font-weight: 600; }

    .cat-toolbar-right { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }

    .cat-sort-select {
        font-family: var(--cb-font-sans);
        font-size: 13px;
        padding: 7px 12px;
        border: 1.5px solid var(--cb-brand-border);
        border-radius: 10px;
        background: var(--cb-brand-white);
        color: var(--cb-brand-text);
        outline: none;
        cursor: pointer;
        transition: border-color 0.2s;
    }

    .cat-sort-select:focus { border-color: var(--cb-brand-accent); }

    .cat-view-toggle {
        display: flex;
        border: 1.5px solid var(--cb-brand-border);
        border-radius: 10px;
        overflow: hidden;
    }

    .cat-view-btn {
        padding: 7px 13px;
        font-size: 12px;
        font-weight: 600;
        color: var(--cb-brand-muted);
        text-decoration: none;
        background: transparent;
        border: none;
        transition: all 0.15s;
        cursor: pointer;
        line-height: 1;
    }

    .cat-view-btn:hover { color: var(--cb-brand-text); background: var(--cb-brand-bg); }

    .cat-view-btn.active {
        background: var(--cb-brand-text);
        color: #fff;
    }

    .cat-empty {
        background: var(--cb-brand-white);
        border: 1.5px dashed var(--cb-brand-border);
        border-radius: 20px;
        padding: 64px 32px;
        text-align: center;
    }

    .cat-empty-icon {
        font-size: 40px;
        color: #c9bfa8;
        margin-bottom: 16px;
        line-height: 1;
    }

    .cat-empty h3 {
        font-family: var(--cb-font-serif);
        font-size: 22px;
        font-weight: 700;
        color: var(--cb-brand-text);
        margin: 0 0 8px;
    }

    .cat-empty p {
        font-size: 14px;
        color: var(--cb-brand-muted);
        margin: 0 0 24px;
    }

    .cat-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 16px;
    }

    @media (max-width: 1100px) { .cat-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 600px) { .cat-grid { grid-template-columns: 1fr; } }

    .cat-book-card {
        background: var(--cb-brand-white);
        border: 1px solid var(--cb-brand-border);
        border-radius: 16px;
        overflow: hidden;
        text-decoration: none;
        display: block;
        transition: transform 0.22s, box-shadow 0.22s;
    }

    .cat-book-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 36px rgba(0,0,0,0.09);
    }

    .cat-book-thumb {
        height: 200px;
        background: #f0ede6;
        overflow: hidden;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .cat-book-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: block;
    }

    .cat-book-thumb-placeholder {
        font-family: var(--cb-font-serif);
        font-size: 48px;
        font-weight: 900;
        color: #c9bfa8;
    }

    .cat-stock-badge {
        position: absolute;
        top: 10px;
        left: 10px;
        font-size: 10px;
        font-weight: 700;
        padding: 3px 10px;
        border-radius: 999px;
        letter-spacing: 0.4px;
    }

    .cat-stock-badge.in { background: var(--cb-brand-accent-light); color: var(--cb-brand-accent); }
    .cat-stock-badge.out { background: #fef3c7; color: #92400e; }

    .cat-book-body { padding: 16px; }

    .cat-book-title {
        font-size: 14px;
        font-weight: 600;
        color: var(--cb-brand-text);
        line-height: 1.4;
        display: -webkit-box;
        line-clamp: 2;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        margin: 0 0 5px;
    }

    .cat-book-card:hover .cat-book-title { color: var(--cb-brand-accent); }

    .cat-book-author {
        font-size: 12px;
        color: #aaa;
        margin: 0 0 12px;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .cat-book-footer {
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        padding-top: 12px;
        border-top: 1px solid var(--cb-brand-border);
        gap: 8px;
    }

    .cat-book-price {
        font-size: 17px;
        font-weight: 700;
        color: var(--cb-brand-accent);
        line-height: 1;
    }

    .cat-book-orig {
        font-size: 11px;
        color: #bbb;
        text-decoration: line-through;
        margin-top: 3px;
    }

    .cat-detail-btn {
        font-family: var(--cb-font-sans);
        font-size: 12px;
        font-weight: 600;
        padding: 7px 14px;
        border-radius: 8px;
        background: var(--cb-brand-text);
        color: #fff;
        text-decoration: none;
        border: none;
        cursor: pointer;
        transition: background 0.2s;
        white-space: nowrap;
        flex-shrink: 0;
    }

    .cat-detail-btn:hover { background: var(--cb-brand-accent); }

    .cat-list { display: flex; flex-direction: column; gap: 12px; }

    .cat-list-card {
        background: var(--cb-brand-white);
        border: 1px solid var(--cb-brand-border);
        border-radius: 16px;
        overflow: hidden;
        display: grid;
        grid-template-columns: 130px 1fr;
        gap: 0;
        text-decoration: none;
        transition: box-shadow 0.22s;
    }

    .cat-list-card:hover { box-shadow: 0 8px 28px rgba(0,0,0,0.08); }

    .cat-list-thumb {
        height: 150px;
        overflow: hidden;
        background: #f0ede6;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
    }

    .cat-list-thumb img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .cat-list-thumb-placeholder {
        font-family: var(--cb-font-serif);
        font-size: 36px;
        font-weight: 900;
        color: #c9bfa8;
    }

    .cat-list-body {
        padding: 16px 20px;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
    }

    .cat-list-title {
        font-size: 16px;
        font-weight: 600;
        color: var(--cb-brand-text);
        margin: 0 0 5px;
        line-height: 1.4;
        text-decoration: none;
    }

    .cat-list-card:hover .cat-list-title { color: var(--cb-brand-accent); }

    .cat-list-author { font-size: 13px; color: #999; margin: 0 0 8px; }

    .cat-list-desc {
        font-size: 13px;
        color: var(--cb-brand-muted);
        line-height: 1.6;
        display: -webkit-box;
        line-clamp: 2;
        -webkit-line-clamp: 2;
        -webkit-box-orient: vertical;
        overflow: hidden;
        margin: 0 0 14px;
    }

    .cat-list-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        gap: 12px;
    }

    .cat-pagination {
        margin-top: 28px;
        display: flex;
        justify-content: center;
    }

    .cat-pagination nav { display: flex; align-items: center; gap: 6px; }
    .cat-pagination span,
    .cat-pagination a {
        font-family: var(--cb-font-sans);
        font-size: 13px;
        font-weight: 500;
        padding: 7px 14px;
        border-radius: 9px;
        border: 1.5px solid var(--cb-brand-border);
        background: var(--cb-brand-white);
        color: var(--cb-brand-muted);
        text-decoration: none;
        transition: all 0.15s;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        min-width: 38px;
    }

    .cat-pagination a:hover { border-color: var(--cb-brand-accent); color: var(--cb-brand-accent); }
    .cat-pagination [aria-current="page"] span,
    .cat-pagination span[aria-current="page"] {
        background: var(--cb-brand-text);
        border-color: var(--cb-brand-text);
        color: #fff;
    }

    .cat-sidebar-toggle {
        display: none;
        width: 100%;
        font-family: var(--cb-font-sans);
        font-size: 14px;
        font-weight: 600;
        padding: 12px 18px;
        border-radius: 12px;
        border: 1.5px solid var(--cb-brand-border);
        background: var(--cb-brand-white);
        color: var(--cb-brand-text);
        cursor: pointer;
        margin-bottom: 12px;
        text-align: left;
        justify-content: space-between;
        align-items: center;
        transition: border-color 0.2s;
    }

    @media (max-width: 900px) {
        .cat-sidebar-toggle { display: flex; }
        .cat-sidebar { display: none; }
        .cat-sidebar.open { display: flex; }
    }
    </style>
</head>
<body class="cb-site text-slate-800">

    <x-navbar />

    @php
        $parentSlug = $selectedParent?->slug;
        $childSlug = $selectedChild?->slug;
        $queryBase = [
            'q' => $keyword,
            'sort' => $sortBy,
            'view' => $viewMode,
        ];
        $clearFilterUrl = route('catalog.categories', []);
        $isViewingAll = $selectedParent === null;
    @endphp

    <main class="cb-page cat-page">
        <section class="mb-6 flex flex-col gap-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm lg:flex-row lg:items-end lg:justify-between lg:p-5">
            <div>
                <div class="mb-2 text-sm text-slate-500">
                    <a href="{{ route('home') }}" class="hover:text-orange-600">Trang chủ</a>
                    <span class="mx-1">/</span>
                    <span class="text-slate-700">Danh mục</span>
                    @if($selectedParent)
                        <span class="mx-1">/</span>
                        <span class="text-slate-700">{{ $selectedParent->name }}</span>
                    @endif
                    @if($selectedChild)
                        <span class="mx-1">/</span>
                        <span class="text-slate-700">{{ $selectedChild->name }}</span>
                    @endif
                </div>

                <h1 class="text-3xl font-black text-slate-900">{{ $heading ?? 'Danh mục sách' }}</h1>
                <p class="mt-2 max-w-2xl text-sm text-slate-600">
                    Khám phá sách theo danh mục, bộ lọc và sắp xếp giống các trang nội dung khác trong hệ thống.
                </p>

                <div class="mt-4 flex flex-wrap gap-2">
                    <span class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-sm text-slate-600">
                        <strong class="text-slate-900">{{ $totalCategories }}</strong> danh mục
                    </span>
                    <span class="inline-flex items-center gap-2 rounded-full border border-slate-200 bg-slate-50 px-3 py-1 text-sm text-slate-600">
                        <strong class="text-slate-900">{{ number_format($totalBooks) }}</strong> đầu sách
                    </span>
                </div>
            </div>

            <form method="GET" action="{{ route('catalog.categories') }}" class="w-full max-w-xl">
                @if($parentSlug) <input type="hidden" name="parent" value="{{ $parentSlug }}"> @endif
                @if($childSlug) <input type="hidden" name="child" value="{{ $childSlug }}"> @endif
                <div class="flex overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                    <input
                        type="search"
                        name="q"
                        value="{{ $keyword }}"
                        placeholder="Tìm sách, tác giả, ISBN..."
                        class="w-full border-0 bg-transparent px-4 py-3 text-sm text-slate-700 outline-none"
                    >
                    <button type="submit" class="bg-slate-900 px-5 text-sm font-semibold text-white transition hover:bg-emerald-700">
                        Tìm nhanh
                    </button>
                </div>
            </form>
        </section>

        <div class="cat-parents">
            @php
                $allQuery = array_filter(array_merge($queryBase, [
                    'parent' => null,
                    'child' => null,
                    'page' => null,
                ]), fn ($v) => $v !== null && $v !== '');
            @endphp

            <a href="{{ route('catalog.categories', $allQuery) }}" class="cat-parent-pill {{ $isViewingAll ? 'active' : '' }}">
                Tất cả
                <span class="cnt">{{ $parentCategories->sum('children_count') }}</span>
            </a>

            @foreach($parentCategories as $parent)
                @php
                    $parentQuery = array_filter(array_merge($queryBase, [
                        'parent' => $parent->slug,
                        'child' => null,
                        'page' => null,
                    ]), fn ($v) => $v !== null && $v !== '');
                @endphp
                <a href="{{ route('catalog.categories', $parentQuery) }}" class="cat-parent-pill {{ $selectedParent?->id === $parent->id ? 'active' : '' }}">
                    {{ $parent->name }}
                    <span class="cnt">{{ $parent->children_count }}</span>
                </a>
            @endforeach
        </div>

        <button class="cat-sidebar-toggle" id="cat-sidebar-toggle" type="button" aria-expanded="false">
            <span>Bộ lọc &amp; danh mục con</span>
            <svg width="16" height="16" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                <polyline points="6 9 12 15 18 9"></polyline>
            </svg>
        </button>

        <div class="cat-layout">
            <aside class="cat-sidebar" id="cat-sidebar">
                @if($selectedParent)
                    <div class="cat-card">
                        <p class="cat-card-title">Danh mục con</p>
                        <div class="cat-child-list">
                            @php
                                $allChildQuery = array_filter(array_merge($queryBase, [
                                    'parent' => $selectedParent->slug,
                                    'child' => null,
                                    'page' => null,
                                ]), fn ($v) => $v !== null && $v !== '');
                            @endphp
                            <a href="{{ route('catalog.categories', $allChildQuery) }}" class="cat-child-link {{ !$selectedChild ? 'active' : '' }}">
                                <span>Tất cả trong {{ $selectedParent->name }}</span>
                            </a>

                            @forelse($childCategories as $child)
                                @php
                                    $childQuery = array_filter(array_merge($queryBase, [
                                        'parent' => $selectedParent->slug,
                                        'child' => $child->slug,
                                        'page' => null,
                                    ]), fn ($v) => $v !== null && $v !== '');
                                @endphp
                                <a href="{{ route('catalog.categories', $childQuery) }}" class="cat-child-link {{ $selectedChild?->id === $child->id ? 'active' : '' }}">
                                    <span>{{ $child->name }}</span>
                                    <span class="cat-child-cnt">{{ $child->books_count }}</span>
                                </a>
                            @empty
                                <p style="font-size:13px; color:#aaa; padding: 8px 12px; margin:0">
                                    Không có danh mục con.
                                </p>
                            @endforelse
                        </div>
                    </div>
                @endif
            </aside>

            <section>
                <div class="cat-toolbar">
                    <p class="cat-toolbar-count">
                        Tìm thấy <strong>{{ $books->total() }}</strong> sách
                        @if($keyword)
                            <span> cho từ khóa <strong>{{ $keyword }}</strong></span>
                        @endif
                    </p>

                    <div class="cat-toolbar-right">
                        <form method="GET" action="{{ route('catalog.categories') }}" class="flex items-center gap-2">
                            @if($parentSlug) <input type="hidden" name="parent" value="{{ $parentSlug }}"> @endif
                            @if($childSlug) <input type="hidden" name="child" value="{{ $childSlug }}"> @endif
                            <input type="hidden" name="q" value="{{ $keyword }}">
                            <input type="hidden" name="view" value="{{ $viewMode }}">
                            <select name="sort" class="cat-sort-select" onchange="this.form.submit()">
                                <option value="newest" {{ $sortBy === 'newest' ? 'selected' : '' }}>Mới nhất</option>
                                <option value="price_asc" {{ $sortBy === 'price_asc' ? 'selected' : '' }}>Giá tăng dần</option>
                                <option value="price_desc" {{ $sortBy === 'price_desc' ? 'selected' : '' }}>Giá giảm dần</option>
                                <option value="title_asc" {{ $sortBy === 'title_asc' ? 'selected' : '' }}>Tên A–Z</option>
                            </select>
                        </form>

                        @php
                            $gridQuery = array_filter(array_merge($queryBase, [
                                'parent' => $parentSlug,
                                'child' => $childSlug,
                                'view' => 'grid',
                                'page' => null,
                            ]), fn ($v) => $v !== null && $v !== '');

                            $listQuery = array_filter(array_merge($queryBase, [
                                'parent' => $parentSlug,
                                'child' => $childSlug,
                                'view' => 'list',
                                'page' => null,
                            ]), fn ($v) => $v !== null && $v !== '');
                        @endphp

                        <div class="cat-view-toggle">
                            <a href="{{ route('catalog.categories', $gridQuery) }}" class="cat-view-btn {{ $viewMode === 'grid' ? 'active' : '' }}" title="Dạng lưới">▦</a>
                            <a href="{{ route('catalog.categories', $listQuery) }}" class="cat-view-btn {{ $viewMode === 'list' ? 'active' : '' }}" title="Dạng danh sách">☰</a>
                        </div>
                    </div>
                </div>

                @if($books->count() === 0)
                    <div class="cat-empty">
                        <div class="cat-empty-icon">
                            <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24" style="color:#c9bfa8">
                                <circle cx="11" cy="11" r="8"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                            </svg>
                        </div>
                        <h3>Không tìm thấy sách phù hợp</h3>
                        <p>Thử thay đổi bộ lọc hoặc tìm với từ khoá khác.</p>
                        <a href="{{ $clearFilterUrl }}" class="cat-btn-primary" style="display:inline-flex;width:auto;padding:10px 28px">Xoá bộ lọc</a>
                    </div>
                @elseif($viewMode === 'list')
                    <div class="cat-list">
                        @foreach($books as $book)
                            @php
                                $cover = null;
                                if (! empty($book->cover_image)) {
                                    $cover = str_starts_with($book->cover_image, 'http://') || str_starts_with($book->cover_image, 'https://')
                                        ? $book->cover_image
                                        : asset('storage/'.$book->cover_image);
                                }
                                $hasDiscount = $book->discount_price && $book->discount_price < $book->price;
                                $discountPercent = $hasDiscount ? round(((float) $book->price - (float) $book->discount_price) / (float) $book->price * 100) : 0;
                                $initial = mb_strtoupper(mb_substr($book->title, 0, 1));
                            @endphp

                            <article class="cat-list-card">
                                <div class="cat-list-thumb">
                                    @if($cover)
                                        <img src="{{ $cover }}" alt="{{ $book->title }}">
                                    @else
                                        <span class="cat-list-thumb-placeholder">{{ $initial }}</span>
                                    @endif
                                    @if($hasDiscount)
                                        <span class="cat-stock-badge out">-{{ $discountPercent }}%</span>
                                    @endif
                                </div>

                                <div class="cat-list-body">
                                    <div>
                                        <a href="{{ route('catalog.book', $book->slug) }}" class="cat-list-title">{{ $book->title }}</a>
                                        <p class="cat-list-author">{{ $book->authors->pluck('name')->join(', ') ?: 'Đang cập nhật tác giả' }}</p>
                                        <p class="cat-list-desc">{{ $book->description ?: 'Chưa có mô tả cho đầu sách này.' }}</p>
                                    </div>

                                    <div class="cat-list-footer">
                                        <div>
                                            <div class="cat-book-price">{{ number_format((float) ($book->discount_price ?? $book->price), 0, ',', '.') }}đ</div>
                                            @if($hasDiscount)
                                                <div class="cat-book-orig">{{ number_format((float) $book->price, 0, ',', '.') }}đ</div>
                                            @endif
                                        </div>
                                        <a href="{{ route('catalog.book', $book->slug) }}" class="cat-detail-btn">Xem chi tiết</a>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @else
                    <div class="cat-grid">
                        @foreach($books as $book)
                            @php
                                $cover = null;
                                if (! empty($book->cover_image)) {
                                    $cover = str_starts_with($book->cover_image, 'http://') || str_starts_with($book->cover_image, 'https://')
                                        ? $book->cover_image
                                        : asset('storage/'.$book->cover_image);
                                }
                                $hasDiscount = $book->discount_price && $book->discount_price < $book->price;
                                $discountPercent = $hasDiscount ? round(((float) $book->price - (float) $book->discount_price) / (float) $book->price * 100) : 0;
                                $initial = mb_strtoupper(mb_substr($book->title, 0, 1));
                            @endphp

                            <article class="cat-book-card">
                                <div class="cat-book-thumb">
                                    @if($cover)
                                        <img src="{{ $cover }}" alt="{{ $book->title }}">
                                    @else
                                        <span class="cat-book-thumb-placeholder">{{ $initial }}</span>
                                    @endif
                                    @if($hasDiscount)
                                        <span class="cat-stock-badge out">-{{ $discountPercent }}%</span>
                                    @endif
                                    <span class="cat-stock-badge {{ $book->stock_quantity > 0 ? 'in' : 'out' }}" style="top:40px;">
                                        {{ $book->stock_quantity > 0 ? 'Còn hàng' : 'Tạm hết' }}
                                    </span>
                                </div>

                                <div class="cat-book-body">
                                    <a href="{{ route('catalog.book', $book->slug) }}" style="text-decoration:none">
                                        <p class="cat-book-title">{{ $book->title }}</p>
                                    </a>
                                    <p class="cat-book-author">{{ $book->authors->pluck('name')->first() ?: 'Đang cập nhật' }}</p>
                                    <div class="cat-book-footer">
                                        <div>
                                            <div class="cat-book-price">{{ number_format((float) ($book->discount_price ?? $book->price), 0, ',', '.') }}đ</div>
                                            @if($hasDiscount)
                                                <div class="cat-book-orig">{{ number_format((float) $book->price, 0, ',', '.') }}đ</div>
                                            @endif
                                        </div>
                                        <a href="{{ route('catalog.book', $book->slug) }}" class="cat-detail-btn">Chi tiết</a>
                                    </div>
                                </div>
                            </article>
                        @endforeach
                    </div>
                @endif

                @if($books->hasPages())
                    <div class="cat-pagination">
                        {{ $books->links('pagination::tailwind') }}
                    </div>
                @endif
            </section>
        </div>
    </main>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        const btn = document.getElementById('cat-sidebar-toggle');
        const sidebar = document.getElementById('cat-sidebar');
        if (!btn || !sidebar) return;

        btn.addEventListener('click', function () {
            const open = sidebar.classList.toggle('open');
            btn.setAttribute('aria-expanded', String(open));
            const icon = btn.querySelector('svg');
            if (icon) icon.style.transform = open ? 'rotate(180deg)' : '';
        });
    });
    </script>

</body>
</html>
