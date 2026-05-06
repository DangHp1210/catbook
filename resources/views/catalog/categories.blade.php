@extends('layouts.app')

@section('title', $heading ?? 'Danh mục sách')

@section('styles')
    <style>
    /* ─── Design tokens (aligned with navbar component) ─────── */
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
        --cb-radius-sm:    8px;
        --cb-radius-md:    12px;
        --cb-radius-lg:    16px;
        --cb-radius-xl:    20px;
        --cb-shadow-sm:    0 1px 4px rgba(0,0,0,0.06);
        --cb-shadow-md:    0 4px 16px rgba(0,0,0,0.08);
        --cb-shadow-lg:    0 12px 36px rgba(0,0,0,0.10);
    }

    body {
        font-family: var(--cb-sans, 'DM Sans', system-ui, sans-serif);
        background: var(--cb-bg);
        color: var(--cb-text);
        margin: 0;
    }

    /* ─── Page wrapper ───────────────────────────────────────── */
    .cat-wrap {
        max-width: 1200px;
        margin: 0 auto;
        padding: 36px 32px 80px;
    }

    /* ─── HERO ───────────────────────────────────────────────── */
    .cat-hero {
        background: var(--cb-white);
        border: 1px solid var(--cb-border);
        border-radius: var(--cb-radius-xl);
        padding: 36px 40px;
        margin-bottom: 24px;
        display: flex;
        align-items: flex-end;
        justify-content: space-between;
        gap: 32px;
        flex-wrap: wrap;
        position: relative;
        overflow: hidden;
    }
    /* subtle texture */
    .cat-hero::after {
        content: '';
        position: absolute;
        top: -60px; right: -60px;
        width: 240px; height: 240px;
        border-radius: 50%;
        background: radial-gradient(circle, rgba(45,106,79,0.07) 0%, transparent 70%);
        pointer-events: none;
    }

    .cat-eyebrow {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        font-size: 11px;
        font-weight: 600;
        letter-spacing: 1.8px;
        text-transform: uppercase;
        color: var(--cb-accent);
        background: var(--cb-accent-light);
        padding: 4px 13px;
        border-radius: 999px;
        margin-bottom: 12px;
    }
    .cat-eyebrow::before {
        content: '';
        width: 6px; height: 6px;
        border-radius: 50%;
        background: var(--cb-accent);
        animation: dot-pulse 2s ease-in-out infinite;
    }
    @keyframes dot-pulse {
        0%,100% { opacity:1; transform:scale(1); }
        50%      { opacity:.5; transform:scale(1.5); }
    }

    .cat-hero h1 {
        font-family: var(--cb-serif);
        font-size: 40px;
        font-weight: 900;
        color: #0d1b10;
        letter-spacing: -1.5px;
        line-height: 1.08;
        margin-bottom: 16px;
    }
    .cat-hero h1 em { font-style: italic; color: var(--cb-accent); }

    .cat-breadcrumb {
        display: flex;
        align-items: center;
        gap: 6px;
        font-size: 12px;
        color: var(--cb-muted);
        margin-bottom: 16px;
        flex-wrap: wrap;
    }
    .cat-breadcrumb a { color: var(--cb-muted); text-decoration: none; transition: color .15s; }
    .cat-breadcrumb a:hover { color: var(--cb-accent); }
    .cat-breadcrumb-sep { opacity: .4; }

    .cat-stats { display: flex; gap: 10px; flex-wrap: wrap; }
    .cat-stat-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        font-size: 13px;
        color: var(--cb-muted);
        background: var(--cb-bg);
        border: 1px solid var(--cb-border);
        padding: 6px 16px;
        border-radius: 999px;
    }
    .cat-stat-chip strong { color: var(--cb-text); font-weight: 600; }

    /* Hero search */
    .cat-hero-search {
        flex: 1;
        min-width: 260px;
        max-width: 400px;
        display: flex;
        border: 2px solid var(--cb-border);
        border-radius: var(--cb-radius-md);
        overflow: hidden;
        background: var(--cb-white);
        transition: border-color .2s, box-shadow .2s;
    }
    .cat-hero-search:focus-within {
        border-color: var(--cb-accent);
        box-shadow: 0 0 0 3px rgba(45,106,79,.09);
    }
    .cat-hero-search input {
        flex: 1;
        font-family: var(--cb-sans);
        font-size: 14px;
        padding: 13px 18px;
        border: none;
        outline: none;
        background: transparent;
        color: var(--cb-text);
        min-width: 0;
    }
    .cat-hero-search input::placeholder { color: #c0bbb2; }
    .cat-hero-search button {
        font-family: var(--cb-sans);
        font-size: 13px;
        font-weight: 600;
        padding: 13px 22px;
        border: none;
        background: var(--cb-text);
        color: #fff;
        cursor: pointer;
        white-space: nowrap;
        transition: background .2s;
    }
    .cat-hero-search button:hover { background: var(--cb-accent); }

    /* ─── Parent pills ───────────────────────────────────────── */
    .cat-parents {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 24px;
        padding-bottom: 24px;
        border-bottom: 1px solid var(--cb-border);
    }
    .cat-pill {
        display: inline-flex;
        align-items: center;
        gap: 7px;
        font-family: var(--cb-sans);
        font-size: 13px;
        font-weight: 500;
        padding: 8px 18px;
        border-radius: 999px;
        border: 1.5px solid var(--cb-border);
        background: var(--cb-white);
        color: var(--cb-muted);
        text-decoration: none;
        transition: all .2s;
        white-space: nowrap;
    }
    .cat-pill:hover { border-color: var(--cb-accent); color: var(--cb-accent); }
    .cat-pill.active { background: var(--cb-accent); border-color: var(--cb-accent); color: #fff; }
    .cat-pill .n {
        font-size: 11px; font-weight: 700;
        padding: 1px 7px; border-radius: 999px;
        background: rgba(0,0,0,.1);
    }
    .cat-pill.active .n { background: rgba(255,255,255,.25); }

    /* ─── 2-col layout ───────────────────────────────────────── */
    .cat-layout {
        display: grid;
        grid-template-columns: 256px 1fr;
        gap: 22px;
        align-items: start;
    }
    @media (max-width: 920px) { .cat-layout { grid-template-columns: 1fr; } }

    /* ─── Sidebar ────────────────────────────────────────────── */
    .cat-sidebar { display: flex; flex-direction: column; gap: 14px; position: sticky; top: 80px; }

    .cat-card {
        background: var(--cb-white);
        border: 1px solid var(--cb-border);
        border-radius: var(--cb-radius-lg);
        overflow: hidden;
    }
    .cat-card-head {
        padding: 14px 18px 10px;
        font-size: 10px;
        font-weight: 700;
        letter-spacing: 1.6px;
        text-transform: uppercase;
        color: #b0a898;
        border-bottom: 1px solid var(--cb-border);
    }
    .cat-card-body { padding: 10px; }

    /* child list */
    .cat-child-link {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 9px 10px;
        border-radius: var(--cb-radius-sm);
        font-size: 13px;
        font-weight: 500;
        color: #555;
        text-decoration: none;
        transition: background .15s, color .15s;
        gap: 8px;
    }
    .cat-child-link:hover { background: var(--cb-bg); color: var(--cb-accent); }
    .cat-child-link.active {
        background: var(--cb-accent-light);
        color: var(--cb-accent);
        font-weight: 600;
    }
    .cat-child-badge {
        font-size: 10px;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 999px;
        background: var(--cb-bg);
        color: #999;
        border: 1px solid var(--cb-border);
        flex-shrink: 0;
    }
    .cat-child-link.active .cat-child-badge {
        background: rgba(45,106,79,.12);
        color: var(--cb-accent);
        border-color: transparent;
    }

    /* filter form */
    .cat-filter-form { padding: 14px 16px; }
    .cat-filter-sec { margin-bottom: 18px; }
    .cat-filter-sec:last-child { margin-bottom: 0; }
    .cat-filter-lbl {
        display: block;
        font-size: 12px;
        font-weight: 600;
        color: var(--cb-text);
        margin-bottom: 9px;
        letter-spacing: .1px;
    }
    .cat-hr { height: 1px; background: var(--cb-border); margin: 16px 0; }

    /* range */
    .cat-range { width: 100%; accent-color: var(--cb-accent); cursor: pointer; margin-bottom: 6px; }
    .cat-range-vals {
        display: flex;
        justify-content: space-between;
        font-size: 12px;
        font-weight: 600;
        color: var(--cb-text);
        background: var(--cb-bg);
        border: 1px solid var(--cb-border);
        border-radius: var(--cb-radius-sm);
        padding: 7px 12px;
    }

    /* select */
    .cat-select {
        width: 100%;
        font-family: var(--cb-sans);
        font-size: 13px;
        padding: 10px 14px;
        border: 1.5px solid var(--cb-border);
        border-radius: var(--cb-radius-sm);
        background: var(--cb-white);
        color: var(--cb-text);
        outline: none;
        cursor: pointer;
        transition: border-color .2s;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='none' viewBox='0 0 24 24'%3E%3Cpolyline points='6 9 12 15 18 9' stroke='%23999' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 12px center;
        padding-right: 36px;
    }
    .cat-select:focus { border-color: var(--cb-accent); }

    /* radio */
    .cat-radio { display: flex; flex-direction: column; gap: 8px; }
    .cat-radio label {
        display: flex;
        align-items: center;
        gap: 9px;
        font-size: 13px;
        color: #555;
        cursor: pointer;
    }
    .cat-radio input[type="radio"] { accent-color: var(--cb-accent); width: 14px; height: 14px; }

    /* filter buttons */
    .cat-filter-btns { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; margin-top: 18px; }
    .cat-filter-submit {
        font-family: var(--cb-sans);
        font-size: 13px; font-weight: 600;
        padding: 10px; border-radius: var(--cb-radius-sm);
        border: none; background: var(--cb-text); color: #fff;
        cursor: pointer; transition: background .2s;
        display: flex; align-items: center; justify-content: center; gap: 6px;
    }
    .cat-filter-submit:hover { background: var(--cb-accent); }
    .cat-filter-reset {
        font-family: var(--cb-sans);
        font-size: 13px; font-weight: 500;
        padding: 10px; border-radius: var(--cb-radius-sm);
        border: 1.5px solid var(--cb-border);
        background: transparent; color: var(--cb-muted);
        cursor: pointer; transition: all .2s;
        display: flex; align-items: center; justify-content: center;
        text-decoration: none;
    }
    .cat-filter-reset:hover { border-color: var(--cb-text); color: var(--cb-text); }

    /* ─── Mobile sidebar toggle ──────────────────────────────── */
    .cat-toggle {
        display: none;
        width: 100%;
        font-family: var(--cb-sans);
        font-size: 14px; font-weight: 600;
        padding: 12px 18px;
        border-radius: var(--cb-radius-md);
        border: 1.5px solid var(--cb-border);
        background: var(--cb-white);
        color: var(--cb-text);
        cursor: pointer;
        margin-bottom: 14px;
        text-align: left;
        justify-content: space-between;
        align-items: center;
        transition: border-color .2s;
    }
    .cat-toggle svg { transition: transform .25s; flex-shrink: 0; }
    .cat-toggle.open svg { transform: rotate(180deg); }
    @media (max-width: 920px) {
        .cat-toggle  { display: flex; }
        .cat-sidebar { display: none; position: static; }
        .cat-sidebar.open { display: flex; }
    }

    /* ─── Toolbar ────────────────────────────────────────────── */
    .cat-toolbar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 12px;
        background: var(--cb-white);
        border: 1px solid var(--cb-border);
        border-radius: var(--cb-radius-md);
        padding: 12px 18px;
        margin-bottom: 16px;
    }
    .cat-count {
        font-size: 13px;
        color: var(--cb-muted);
    }
    .cat-count strong { color: var(--cb-text); font-weight: 600; }
    .cat-count em { font-style: normal; color: var(--cb-accent); font-weight: 500; }

    .cat-toolbar-right { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
    .cat-sort {
        font-family: var(--cb-sans);
        font-size: 13px;
        padding: 8px 14px;
        border: 1.5px solid var(--cb-border);
        border-radius: var(--cb-radius-sm);
        background: var(--cb-white);
        color: var(--cb-text);
        outline: none; cursor: pointer;
        transition: border-color .2s;
        appearance: none;
        background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='none' viewBox='0 0 24 24'%3E%3Cpolyline points='6 9 12 15 18 9' stroke='%23999' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
        background-repeat: no-repeat;
        background-position: right 10px center;
        padding-right: 30px;
    }
    .cat-sort:focus { border-color: var(--cb-accent); }

    .cat-view {
        display: flex;
        border: 1.5px solid var(--cb-border);
        border-radius: var(--cb-radius-sm);
        overflow: hidden;
    }
    .cat-view a {
        padding: 7px 13px;
        font-size: 14px;
        color: var(--cb-muted);
        text-decoration: none;
        background: transparent;
        transition: all .15s;
        line-height: 1;
        display: flex; align-items: center;
    }
    .cat-view a:hover { background: var(--cb-bg); color: var(--cb-text); }
    .cat-view a.active { background: var(--cb-text); color: #fff; }

    /* ─── Empty state ────────────────────────────────────────── */
    .cat-empty {
        background: var(--cb-white);
        border: 2px dashed var(--cb-border);
        border-radius: var(--cb-radius-xl);
        padding: 72px 32px;
        text-align: center;
    }
    .cat-empty svg { color: #d0c8be; margin-bottom: 16px; }
    .cat-empty h3 {
        font-family: var(--cb-serif);
        font-size: 22px; font-weight: 700;
        color: var(--cb-text);
        margin-bottom: 8px;
    }
    .cat-empty p { font-size: 14px; color: var(--cb-muted); margin-bottom: 24px; }
    .cat-empty-btn {
        display: inline-flex; align-items: center; gap: 6px;
        font-family: var(--cb-sans);
        font-size: 13px; font-weight: 600;
        padding: 10px 24px;
        border-radius: var(--cb-radius-sm);
        background: var(--cb-text); color: #fff;
        text-decoration: none;
        transition: background .2s;
    }
    .cat-empty-btn:hover { background: var(--cb-accent); }

    /* ─── GRID view ──────────────────────────────────────────── */
    .cat-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 14px;
    }
    @media (max-width: 1080px) { .cat-grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 560px)  { .cat-grid { grid-template-columns: 1fr; } }

    .cat-gcard {
        background: var(--cb-white);
        border: 1px solid var(--cb-border);
        border-radius: var(--cb-radius-lg);
        overflow: hidden;
        display: block;
        text-decoration: none;
        transition: transform .22s ease, box-shadow .22s ease;
    }
    .cat-gcard:hover {
        transform: translateY(-4px);
        box-shadow: var(--cb-shadow-lg);
    }

    .cat-gcard-img {
        height: 196px;
        background: #ede9e1;
        position: relative;
        overflow: hidden;
        display: flex; align-items: center; justify-content: center;
    }
    .cat-gcard-img img {
        width: 100%; height: 100%;
        object-fit: cover;
        transition: transform .4s ease;
    }
    .cat-gcard:hover .cat-gcard-img img { transform: scale(1.04); }
    .cat-gcard-placeholder {
        font-family: var(--cb-serif);
        font-size: 52px; font-weight: 900;
        color: #c5bdb0;
    }

    /* overlaid badges */
    .cat-gcard-badges {
        position: absolute;
        top: 10px; left: 10px;
        display: flex; flex-direction: column; gap: 4px;
    }
    .cb-badge {
        font-size: 10px; font-weight: 700;
        padding: 3px 10px; border-radius: 999px;
        letter-spacing: .4px; line-height: 1.4;
        backdrop-filter: blur(4px);
    }
    .cb-badge-discount { background: rgba(220,38,38,.88); color: #fff; }
    .cb-badge-stock-in  { background: rgba(45,106,79,.88); color: #fff; }
    .cb-badge-stock-out { background: rgba(146,64,14,.88); color: #fff; }

    .cat-gcard-body { padding: 14px 16px 16px; }
    .cat-gcard-title {
        font-size: 14px; font-weight: 600;
        color: var(--cb-text);
        line-height: 1.4;
        display: -webkit-box;
        -webkit-line-clamp: 2; -webkit-box-orient: vertical;
        line-clamp: 2;
        overflow: hidden;
        margin-bottom: 4px;
        transition: color .15s;
    }
    .cat-gcard:hover .cat-gcard-title { color: var(--cb-accent); }
    .cat-gcard-author {
        font-size: 12px; color: #aaa;
        white-space: nowrap; overflow: hidden; text-overflow: ellipsis;
        margin-bottom: 12px;
    }
    .cat-gcard-foot {
        display: flex; align-items: flex-end; justify-content: space-between; gap: 8px;
        padding-top: 12px;
        border-top: 1px solid var(--cb-border);
    }
    .cat-price {
        font-size: 17px; font-weight: 700;
        color: var(--cb-accent); line-height: 1;
    }
    .cat-price-orig {
        font-size: 11px; color: #c0b8b0;
        text-decoration: line-through; margin-top: 3px;
    }
    .cat-detail-btn {
        font-family: var(--cb-sans);
        font-size: 12px; font-weight: 600;
        padding: 7px 14px; border-radius: var(--cb-radius-sm);
        background: var(--cb-text); color: #fff;
        text-decoration: none; border: none;
        cursor: pointer; flex-shrink: 0;
        transition: background .2s;
        white-space: nowrap;
    }
    .cat-detail-btn:hover { background: var(--cb-accent); }

    /* ─── LIST view ──────────────────────────────────────────── */
    .cat-list { display: flex; flex-direction: column; gap: 10px; }

    .cat-lcard {
        background: var(--cb-white);
        border: 1px solid var(--cb-border);
        border-radius: var(--cb-radius-lg);
        overflow: hidden;
        display: grid;
        grid-template-columns: 120px 1fr;
        text-decoration: none;
        transition: box-shadow .22s, border-color .22s;
    }
    .cat-lcard:hover {
        box-shadow: var(--cb-shadow-md);
        border-color: #d8d2c8;
    }
    .cat-lcard-img {
        background: #ede9e1;
        display: flex; align-items: center; justify-content: center;
        overflow: hidden; position: relative;
        min-height: 148px;
    }
    .cat-lcard-img img { width: 100%; height: 100%; object-fit: cover; }
    .cat-lcard-placeholder {
        font-family: var(--cb-serif);
        font-size: 38px; font-weight: 900; color: #c5bdb0;
    }
    .cat-lcard-body {
        padding: 16px 20px;
        display: flex; flex-direction: column; justify-content: space-between;
    }
    .cat-lcard-title {
        font-size: 15px; font-weight: 600;
        color: var(--cb-text);
        line-height: 1.4; margin-bottom: 4px;
        transition: color .15s;
    }
    .cat-lcard:hover .cat-lcard-title { color: var(--cb-accent); }
    .cat-lcard-author { font-size: 12px; color: #aaa; margin-bottom: 8px; }
    .cat-lcard-desc {
        font-size: 13px; color: var(--cb-muted);
        line-height: 1.65;
        max-height: 3.3em;
        overflow: hidden;
        margin-bottom: 14px;
        flex: 1;
    }
    .cat-lcard-foot {
        display: flex; align-items: center; justify-content: space-between; gap: 10px;
    }

    /* ─── Pagination ─────────────────────────────────────────── */
    .cat-pagination {
        margin-top: 28px;
        display: flex; justify-content: center;
    }
    /* Override Laravel pagination */
    .cat-pagination nav {
        display: flex; align-items: center; gap: 5px; flex-wrap: wrap; justify-content: center;
    }
    .cat-pagination nav span,
    .cat-pagination nav a {
        font-family: var(--cb-sans);
        font-size: 13px; font-weight: 500;
        padding: 7px 13px;
        border-radius: 9px;
        border: 1.5px solid var(--cb-border);
        background: var(--cb-white);
        color: var(--cb-muted);
        text-decoration: none;
        transition: all .15s;
        display: inline-flex; align-items: center; justify-content: center;
        min-width: 36px;
    }
    .cat-pagination nav a:hover {
        border-color: var(--cb-accent); color: var(--cb-accent);
    }
    .cat-pagination nav [aria-current="page"] > span,
    .cat-pagination nav span[aria-current="page"] {
        background: var(--cb-text); border-color: var(--cb-text); color: #fff;
    }
    .cat-pagination nav span[aria-disabled="true"] { opacity: .4; pointer-events: none; }
    </style>
@endsection

@section('content')

<div class="cat-wrap">

@php
    $parentSlug = $selectedParent?->slug;
    $childSlug  = $selectedChild?->slug;
    $queryBase  = [
        'q'         => $keyword,
        'sort'      => $sortBy,
        'view'      => $viewMode,
        'min_price' => $minPrice  ?? null,
        'max_price' => $maxPrice  ?? null,
        'language'  => $languageFilter ?? null,
        'stock'     => $stockFilter    ?? null,
    ];
    $clearUrl = route('catalog.categories', []);
@endphp

    {{-- ── HERO ─────────────────────────────────────────────── --}}
    <div class="cat-hero">
        <div style="flex:1; min-width:260px">
            {{-- Breadcrumb --}}
            <nav class="cat-breadcrumb" aria-label="breadcrumb">
                <a href="{{ route('home') }}">Trang chủ</a>
                <span class="cat-breadcrumb-sep">/</span>
                @if($selectedParent)
                    <a href="{{ route('catalog.categories') }}">Danh mục</a>
                    <span class="cat-breadcrumb-sep">/</span>
                    @if($selectedChild)
                        @php $ppQ = array_filter(array_merge($queryBase,['parent'=>$parentSlug,'child'=>null,'page'=>null]),fn($v)=>$v!==null&&$v!=='') @endphp
                        <a href="{{ route('catalog.categories', $ppQ) }}">{{ $selectedParent->name }}</a>
                        <span class="cat-breadcrumb-sep">/</span>
                        <span style="color:var(--cb-text)">{{ $selectedChild->name }}</span>
                    @else
                        <span style="color:var(--cb-text)">{{ $selectedParent->name }}</span>
                    @endif
                @else
                    <span style="color:var(--cb-text)">Danh mục</span>
                @endif
            </nav>

            <div class="cat-eyebrow">Danh mục sách</div>

            <h1>
                @if($selectedChild)
                    <em>{{ $selectedChild->name }}</em>
                @elseif($selectedParent)
                    {{ $selectedParent->name }}
                @else
                    Khám phá <em>sách hay</em>
                @endif
            </h1>

            <div class="cat-stats">
                <span class="cat-stat-chip">
                    <strong>{{ number_format($totalCategories) }}</strong> danh mục
                </span>
                <span class="cat-stat-chip">
                    <strong>{{ number_format($totalBooks) }}</strong> đầu sách
                </span>
                @if($keyword)
                    <span class="cat-stat-chip" style="background:#fff8e6; border-color:#f5d87a; color:#92400e">
                        Kết quả cho: <strong>{{ $keyword }}</strong>
                    </span>
                @endif
            </div>
        </div>

        {{-- Search --}}
        <form method="GET" action="{{ route('catalog.categories') }}" class="cat-hero-search">
            @if($parentSlug) <input type="hidden" name="parent" value="{{ $parentSlug }}"> @endif
            @if($childSlug)  <input type="hidden" name="child"  value="{{ $childSlug }}">  @endif
            <input type="search" name="q" value="{{ $keyword }}"
                   placeholder="Tìm sách, tác giả, ISBN..."
                   autocomplete="off">
            <button type="submit">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24" style="margin-right:6px"><circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/></svg>
                Tìm kiếm
            </button>
        </form>
    </div>

    {{-- ── PARENT PILLS ─────────────────────────────────────── --}}
    <div class="cat-parents">
        @php
            $allQ = array_filter(array_merge($queryBase,['parent'=>null,'child'=>null,'page'=>null]),fn($v)=>$v!==null&&$v!=='');
        @endphp
        <a href="{{ route('catalog.categories', $allQ) }}"
           class="cat-pill {{ !$selectedParent ? 'active' : '' }}">
            Tất cả
            <span class="n">{{ $parentCategories->sum('children_count') }}</span>
        </a>
        @foreach($parentCategories as $par)
            @php
                $pQ = array_filter(array_merge($queryBase,['parent'=>$par->slug,'child'=>null,'page'=>null]),fn($v)=>$v!==null&&$v!=='');
            @endphp
            <a href="{{ route('catalog.categories', $pQ) }}"
               class="cat-pill {{ $selectedParent?->id===$par->id ? 'active' : '' }}">
                {{ $par->name }}
                <span class="n">{{ $par->children_count }}</span>
            </a>
        @endforeach
    </div>

    {{-- ── MOBILE TOGGLE ───────────────────────────────────── --}}
    <button class="cat-toggle" id="cat-toggle" type="button" aria-expanded="false">
        <span style="display:flex;align-items:center;gap:8px">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="4" y1="6" x2="20" y2="6"/><line x1="4" y1="12" x2="14" y2="12"/><line x1="4" y1="18" x2="18" y2="18"/></svg>
            Bộ lọc &amp; danh mục con
        </span>
        <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="6 9 12 15 18 9"/></svg>
    </button>

    {{-- ── 2-COL LAYOUT ────────────────────────────────────── --}}
    <div class="cat-layout">

        {{-- SIDEBAR --}}
        <aside class="cat-sidebar" id="cat-sidebar">

            {{-- Danh mục con --}}
            @if($selectedParent)
                <div class="cat-card">
                    <div class="cat-card-head">Danh mục con</div>
                    <div class="cat-card-body">
                        @php
                            $acQ = array_filter(array_merge($queryBase,['parent'=>$selectedParent->slug,'child'=>null,'page'=>null]),fn($v)=>$v!==null&&$v!=='');
                        @endphp
                        <a href="{{ route('catalog.categories', $acQ) }}"
                           class="cat-child-link {{ !$selectedChild ? 'active' : '' }}">
                            <span> {{ $selectedParent->name }}</span>
                        </a>
                        @forelse($childCategories as $child)
                            @php
                                $cQ = array_filter(array_merge($queryBase,['parent'=>$selectedParent->slug,'child'=>$child->slug,'page'=>null]),fn($v)=>$v!==null&&$v!=='');
                            @endphp
                            <a href="{{ route('catalog.categories', $cQ) }}"
                               class="cat-child-link {{ $selectedChild?->id===$child->id ? 'active' : '' }}">
                                <span>&raquo; {{ $child->name }}</span>
                                <span class="cat-child-badge">{{ $child->books_count }}</span>
                            </a>
                        @empty
                            <p style="font-size:13px;color:#bbb;padding:6px 10px">Không có danh mục con.</p>
                        @endforelse
                    </div>
                </div>
            @endif

            {{-- Bộ lọc --}}
            <div class="cat-card">
                <div class="cat-card-head">Bộ lọc</div>
                <form method="GET" action="{{ route('catalog.categories') }}" class="cat-filter-form">
                    @if($parentSlug) <input type="hidden" name="parent" value="{{ $parentSlug }}"> @endif
                    @if($childSlug)  <input type="hidden" name="child"  value="{{ $childSlug }}">  @endif
                    <input type="hidden" name="q"    value="{{ $keyword }}">
                    <input type="hidden" name="sort" value="{{ $sortBy }}">
                    <input type="hidden" name="view" value="{{ $viewMode }}">

                    {{-- Khoảng giá --}}
                    @if(isset($minPossiblePrice, $maxPossiblePrice))
                        <div class="cat-filter-sec">
                            <span class="cat-filter-lbl">Khoảng giá</span>
                            <div style="display:flex;justify-content:space-between;font-size:11px;color:#aaa;margin-bottom:4px">
                                <span>Từ</span><span>Đến</span>
                            </div>
                            <input id="minRange" type="range" name="min_price" class="cat-range"
                                   min="{{ $minPossiblePrice }}" max="{{ $maxPossiblePrice }}"
                                   value="{{ $minPrice ?? $minPossiblePrice }}">
                            <input id="maxRange" type="range" name="max_price" class="cat-range"
                                   min="{{ $minPossiblePrice }}" max="{{ $maxPossiblePrice }}"
                                   value="{{ $maxPrice ?? $maxPossiblePrice }}">
                            <div class="cat-range-vals">
                                <span id="minRangeLabel">{{ number_format((float)($minPrice ?? $minPossiblePrice), 0, ',', '.') }}đ</span>
                                <span id="maxRangeLabel">{{ number_format((float)($maxPrice ?? $maxPossiblePrice), 0, ',', '.') }}đ</span>
                            </div>
                        </div>
                        <div class="cat-hr"></div>
                    @endif

                    {{-- Ngôn ngữ --}}
                    @if(isset($availableLanguages) && $availableLanguages->count())
                        <div class="cat-filter-sec">
                            <span class="cat-filter-lbl">Ngôn ngữ</span>
                            <select name="language" class="cat-select">
                                <option value="">Tất cả ngôn ngữ</option>
                                @foreach($availableLanguages as $lang)
                                    <option value="{{ $lang }}" {{ ($languageFilter??'')===$lang ? 'selected' : '' }}>{{ $lang }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="cat-hr"></div>
                    @endif

                    {{-- Tình trạng --}}
                    <div class="cat-filter-sec">
                        <span class="cat-filter-lbl">Tình trạng</span>
                        <div class="cat-radio">
                            <label><input type="radio" name="stock" value="all"          {{ ($stockFilter??'all')==='all'          ? 'checked' : '' }}> Tất cả</label>
                            <label><input type="radio" name="stock" value="in_stock"     {{ ($stockFilter??'')==='in_stock'         ? 'checked' : '' }}> Còn hàng</label>
                            <label><input type="radio" name="stock" value="out_of_stock" {{ ($stockFilter??'')==='out_of_stock'     ? 'checked' : '' }}> Tạm hết hàng</label>
                        </div>
                    </div>

                    <div class="cat-filter-btns">
                        <button type="submit" class="cat-filter-submit">
                            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                            Áp dụng
                        </button>
                        <a href="{{ $clearUrl }}" class="cat-filter-reset">Xoá lọc</a>
                    </div>
                </form>
            </div>

        </aside>

        {{-- MAIN CONTENT --}}
        <section>

            {{-- Toolbar --}}
            <div class="cat-toolbar">
                <p class="cat-count">
                    Tìm thấy <strong>{{ number_format($books->total()) }}</strong> đầu sách
                    @if($keyword)
                        &nbsp;cho <em>"{{ $keyword }}"</em>
                    @endif
                </p>
                <div class="cat-toolbar-right">
                    {{-- Sort --}}
                    <form method="GET" action="{{ route('catalog.categories') }}">
                        @if($parentSlug) <input type="hidden" name="parent" value="{{ $parentSlug }}"> @endif
                        @if($childSlug)  <input type="hidden" name="child"  value="{{ $childSlug }}">  @endif
                        <input type="hidden" name="q"         value="{{ $keyword }}">
                        <input type="hidden" name="view"      value="{{ $viewMode }}">
                        <input type="hidden" name="min_price" value="{{ $minPrice ?? '' }}">
                        <input type="hidden" name="max_price" value="{{ $maxPrice ?? '' }}">
                        <input type="hidden" name="language"  value="{{ $languageFilter ?? '' }}">
                        <input type="hidden" name="stock"     value="{{ $stockFilter ?? '' }}">
                        <select name="sort" class="cat-sort" onchange="this.form.submit()">
                            <option value="newest"     {{ $sortBy==='newest'     ? 'selected':'' }}>Mới nhất</option>
                            <option value="popular"    {{ $sortBy==='popular'    ? 'selected':'' }}>Phổ biến</option>
                            <option value="price_asc"  {{ $sortBy==='price_asc'  ? 'selected':'' }}>Giá tăng dần</option>
                            <option value="price_desc" {{ $sortBy==='price_desc' ? 'selected':'' }}>Giá giảm dần</option>
                            <option value="title_asc"  {{ $sortBy==='title_asc'  ? 'selected':'' }}>Tên A–Z</option>
                        </select>
                    </form>

                    {{-- View toggle --}}
                    @php
                        $gQ = array_filter(array_merge($queryBase,['parent'=>$parentSlug,'child'=>$childSlug,'view'=>'grid','page'=>null]),fn($v)=>$v!==null&&$v!=='');
                        $lQ = array_filter(array_merge($queryBase,['parent'=>$parentSlug,'child'=>$childSlug,'view'=>'list','page'=>null]),fn($v)=>$v!==null&&$v!=='');
                    @endphp
                    <div class="cat-view">
                        <a href="{{ route('catalog.categories', $gQ) }}"
                           class="{{ $viewMode==='grid' ? 'active':'' }}"
                           title="Dạng lưới">
                            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/></svg>
                        </a>
                        <a href="{{ route('catalog.categories', $lQ) }}"
                           class="{{ $viewMode==='list' ? 'active':'' }}"
                           title="Dạng danh sách">
                            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><line x1="8" y1="6" x2="21" y2="6"/><line x1="8" y1="12" x2="21" y2="12"/><line x1="8" y1="18" x2="21" y2="18"/><line x1="3" y1="6" x2="3.01" y2="6"/><line x1="3" y1="12" x2="3.01" y2="12"/><line x1="3" y1="18" x2="3.01" y2="18"/></svg>
                        </a>
                    </div>
                </div>
            </div>

            {{-- Books --}}
            @if($books->count() === 0)
                <div class="cat-empty">
                    <svg width="52" height="52" fill="none" stroke="currentColor" stroke-width="1.4" viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                    <h3>Không tìm thấy sách phù hợp</h3>
                    <p>Thử thay đổi bộ lọc hoặc dùng từ khoá khác.</p>
                    <a href="{{ $clearUrl }}" class="cat-empty-btn">Xoá bộ lọc</a>
                </div>

            @elseif($viewMode === 'list')
                {{-- LIST ──────────────────────────────────── --}}
                <div class="cat-list">
                    @foreach($books as $book)
                        @php
                            $cover = null;
                            if(!empty($book->cover_image))
                                $cover = str_starts_with($book->cover_image,'http') ? $book->cover_image : asset('storage/'.$book->cover_image);
                            $hasDis = $book->discount_price && $book->discount_price < $book->price;
                            $disPct = $hasDis ? round(((float)$book->price - (float)$book->discount_price)/(float)$book->price*100) : 0;
                            $init   = mb_strtoupper(mb_substr($book->title,0,1));
                        @endphp
                        <article class="cat-lcard">
                            <div class="cat-lcard-img">
                                @if($cover)<img src="{{ $cover }}" alt="{{ $book->title }}" loading="lazy">
                                @else<span class="cat-lcard-placeholder">{{ $init }}</span>@endif
                                <div class="cat-gcard-badges">
                                    @if($hasDis) <span class="cb-badge cb-badge-discount">-{{ $disPct }}%</span> @endif
                                    <span class="cb-badge {{ $book->stock_quantity>0 ? 'cb-badge-stock-in':'cb-badge-stock-out' }}">
                                        {{ $book->stock_quantity>0 ? 'Còn hàng':'Tạm hết' }}
                                    </span>
                                </div>
                            </div>
                            <div class="cat-lcard-body">
                                <div>
                                    <a href="{{ route('catalog.book',$book->slug) }}" class="cat-lcard-title">{{ $book->title }}</a>
                                    <p class="cat-lcard-author">{{ $book->authors->pluck('name')->join(', ') ?: 'Đang cập nhật tác giả' }}</p>
                                    <p class="cat-lcard-desc">{{ $book->description ?: 'Chưa có mô tả cho đầu sách này.' }}</p>
                                </div>
                                <div class="cat-lcard-foot">
                                    <div>
                                        <div class="cat-price">{{ number_format((float)($book->discount_price??$book->price),0,',','.') }}đ</div>
                                        @if($hasDis)<div class="cat-price-orig">{{ number_format((float)$book->price,0,',','.') }}đ</div>@endif
                                    </div>
                                    <a href="{{ route('catalog.book',$book->slug) }}" class="cat-detail-btn">Xem chi tiết</a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

            @else
                {{-- GRID ──────────────────────────────────── --}}
                <div class="cat-grid">
                    @foreach($books as $book)
                        @php
                            $cover = null;
                            if(!empty($book->cover_image))
                                $cover = str_starts_with($book->cover_image,'http') ? $book->cover_image : asset('storage/'.$book->cover_image);
                            $hasDis = $book->discount_price && $book->discount_price < $book->price;
                            $disPct = $hasDis ? round(((float)$book->price - (float)$book->discount_price)/(float)$book->price*100) : 0;
                            $init   = mb_strtoupper(mb_substr($book->title,0,1));
                        @endphp
                        <article class="cat-gcard">
                            <div class="cat-gcard-img">
                                @if($cover)<img src="{{ $cover }}" alt="{{ $book->title }}" loading="lazy">
                                @else<span class="cat-gcard-placeholder">{{ $init }}</span>@endif
                                <div class="cat-gcard-badges">
                                    @if($hasDis) <span class="cb-badge cb-badge-discount">-{{ $disPct }}%</span> @endif
                                    <span class="cb-badge {{ $book->stock_quantity>0 ? 'cb-badge-stock-in':'cb-badge-stock-out' }}">
                                        {{ $book->stock_quantity>0 ? 'Còn hàng':'Tạm hết' }}
                                    </span>
                                </div>
                            </div>
                            <div class="cat-gcard-body">
                                <a href="{{ route('catalog.book',$book->slug) }}" style="text-decoration:none">
                                    <p class="cat-gcard-title">{{ $book->title }}</p>
                                </a>
                                <p class="cat-gcard-author">{{ $book->authors->pluck('name')->first() ?: 'Đang cập nhật' }}</p>
                                <div class="cat-gcard-foot">
                                    <div>
                                        <div class="cat-price">{{ number_format((float)($book->discount_price??$book->price),0,',','.') }}đ</div>
                                        @if($hasDis)<div class="cat-price-orig">{{ number_format((float)$book->price,0,',','.') }}đ</div>@endif
                                    </div>
                                    <a href="{{ route('catalog.book',$book->slug) }}" class="cat-detail-btn">Chi tiết</a>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            @endif

            {{-- Pagination --}}
            @if($books->hasPages())
                <div class="cat-pagination">
                    {{ $books->links() }}
                </div>
            @endif

        </section>
    </div>{{-- /.cat-layout --}}
</div>{{-- /.cat-wrap --}}

<script>
/* ── Price range ──────────────────────────────────────────── */
(function(){
    const min = document.getElementById('minRange');
    const max = document.getElementById('maxRange');
    const minL = document.getElementById('minRangeLabel');
    const maxL = document.getElementById('maxRangeLabel');
    if(!min||!max) return;
    const fmt = v => Number(v).toLocaleString('vi-VN')+'đ';
    const sync = () => {
        let a = parseInt(min.value,10), b = parseInt(max.value,10);
        if(a>b){ if(document.activeElement===min){b=a;max.value=b;}else{a=b;min.value=a;} }
        minL.textContent = fmt(a); maxL.textContent = fmt(b);
    };
    min.addEventListener('input',sync);
    max.addEventListener('input',sync);
    sync();
})();

/* ── Mobile sidebar toggle ────────────────────────────────── */
document.addEventListener('DOMContentLoaded',function(){
    const btn  = document.getElementById('cat-toggle');
    const side = document.getElementById('cat-sidebar');
    if(!btn||!side) return;
    btn.addEventListener('click',function(){
        const open = side.classList.toggle('open');
        btn.classList.toggle('open',open);
        btn.setAttribute('aria-expanded',String(open));
    });
});
</script>

@endsection
