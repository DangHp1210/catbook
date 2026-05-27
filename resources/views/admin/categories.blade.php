@extends('layouts.admin', ['title' => 'Quản lý danh mục'])

@php
    $q = $q ?? trim((string) request()->query('q', ''));
    $categories = $categories ?? collect();
    $allCategories = $allCategories ?? collect();
    $categoryOptions = $categoryOptions ?? collect();

    $totalCategories     = $allCategories->count();
    $rootCategories      = $allCategories->whereNull('parent_id')->count();
    $leafCategories      = $allCategories->filter(fn($c) => !(bool)($c->has_children ?? false))->count();
    $categoriesWithBooks = $allCategories->filter(fn($c) => (int)($c->books_count ?? 0) > 0)->count();

    $openCreateModal    = old('_form') === 'create-category';
    $openEditCategoryId = old('_form') === 'update-category' ? (int) old('_category_id') : null;
    $editingCategory    = $openEditCategoryId ? $allCategories->firstWhere('id', $openEditCategoryId) : null;

    $createSelectedParentId = old('parent_id') !== null && old('parent_id') !== '' ? (int) old('parent_id') : null;
    $createSelectedParent   = $createSelectedParentId ? $allCategories->firstWhere('id', $createSelectedParentId) : null;

    $editSelectedParentId = old('parent_id') !== null && old('parent_id') !== ''
        ? (int) old('parent_id')
        : ($editingCategory?->parent_id ? (int) $editingCategory->parent_id : null);
    $editSelectedParent = $editSelectedParentId ? $allCategories->firstWhere('id', $editSelectedParentId) : null;
@endphp

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
.ca-header {
    background: var(--cb-white); border: 1px solid var(--cb-border);
    border-radius: 18px; padding: 20px 26px;
    display: flex; align-items: flex-end; justify-content: space-between;
    gap: 20px; flex-wrap: wrap; margin-bottom: 16px;
    position: relative; overflow: hidden;
    max-width: 1300px;
    margin: 0 auto 16px;
}
.ca-header::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
    background: linear-gradient(90deg, #f59e0b, var(--cb-accent));
}
.ca-header-title {
    font-family: var(--cb-serif); font-size: 22px; font-weight: 900;
    color: #0d1b10; letter-spacing: -.5px; margin: 0 0 3px;
}
.ca-header-sub { font-family: var(--cb-sans); font-size: 13px; color: var(--cb-muted); }
.ca-header-right { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }

/* Search */
.ca-search-wrap {
    display: flex; border: 1.5px solid var(--cb-border); border-radius: 10px;
    overflow: hidden; background: var(--cb-white); transition: border-color .2s;
}
.ca-search-wrap:focus-within { border-color: var(--cb-accent); }
.ca-search-icon { padding: 0 10px 0 12px; display: flex; align-items: center; color: var(--cb-muted); }
.ca-search-input {
    font-family: var(--cb-sans); font-size: 13px; border: none; outline: none;
    background: transparent; color: var(--cb-text); padding: 9px 14px 9px 0; width: 220px;
}
.ca-search-input::placeholder { color: #c0b8b0; }

/* Add button */
.ca-btn-add {
    font-family: var(--cb-sans); font-size: 13px; font-weight: 600;
    padding: 10px 18px; border-radius: 10px; border: none;
    background: var(--cb-text); color: #fff; cursor: pointer;
    display: inline-flex; align-items: center; gap: 7px;
    transition: background .2s; white-space: nowrap;
}
.ca-btn-add:hover { background: var(--cb-accent); }

/* ─── Stats strip ─────────────────────────────────────── */
.ca-stats {
    display: grid; grid-template-columns: repeat(4, 1fr); gap: 12px; margin-bottom: 16px;
    max-width: 1300px;
    margin: 0 auto 16px;
}
@media (max-width: 800px) { .ca-stats { grid-template-columns: repeat(2, 1fr); } }

.ca-stat {
    background: var(--cb-white); border: 1px solid var(--cb-border);
    border-radius: 14px; padding: 14px 18px; position: relative; overflow: hidden;
}
.ca-stat::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 2px;
}
.ca-stat-total::before   { background: #f59e0b; }
.ca-stat-root::before    { background: var(--cb-accent); }
.ca-stat-leaf::before    { background: #8b5cf6; }
.ca-stat-books::before   { background: #0ea5e9; }
.ca-stat-lbl {
    font-family: var(--cb-sans); font-size: 11px; font-weight: 600;
    letter-spacing: 1px; text-transform: uppercase; color: #b0a898; margin-bottom: 6px;
}
.ca-stat-val {
    font-family: var(--cb-serif); font-size: 26px; font-weight: 900;
    color: var(--cb-text); line-height: 1;
}

/* ─── Tree card ───────────────────────────────────────── */
.ca-tree-card {
    background: var(--cb-white); border: 1px solid var(--cb-border);
    border-radius: 18px; overflow: hidden; margin-bottom: 16px;
    max-width: 1300px;
    margin: 0 auto 16px;
}
.ca-tree-toolbar {
    display: flex; align-items: center; justify-content: space-between;
    padding: 12px 20px; border-bottom: 1px solid var(--cb-border);
    flex-wrap: wrap; gap: 8px;
}
.ca-tree-toolbar-title {
    font-family: var(--cb-sans); font-size: 11px; font-weight: 700;
    letter-spacing: 1.3px; text-transform: uppercase; color: #b0a898;
}
.ca-tree-legend {
    display: flex; align-items: center; gap: 16px;
    font-family: var(--cb-sans); font-size: 11px; color: var(--cb-muted);
}
.ca-legend-item { display: flex; align-items: center; gap: 5px; }
.ca-legend-dot {
    width: 8px; height: 8px; border-radius: 50%; flex-shrink: 0;
}

/* ─── Tree table ──────────────────────────────────────── */
.ca-table { width: 100%; border-collapse: collapse; font-family: var(--cb-sans); }
.ca-table thead tr { border-bottom: 1px solid var(--cb-border); }
.ca-table th {
    padding: 10px 18px; font-size: 11px; font-weight: 700;
    letter-spacing: 1.2px; text-transform: uppercase; color: #b0a898; text-align: left;
}
.ca-table tbody tr {
    border-bottom: 1px solid var(--cb-border); transition: background .15s;
}
.ca-table tbody tr:last-child { border-bottom: none; }
.ca-table tbody tr:hover { background: #fdfcfa; }
.ca-table td { padding: 12px 18px; vertical-align: middle; }

/* Tree indent visuals */
.ca-row-wrap { display: flex; align-items: center; gap: 0; }
.ca-indent-unit { width: 24px; flex-shrink: 0; display: flex; align-items: center; justify-content: center; }
.ca-tree-line-v {
    width: 1px; height: 100%; min-height: 20px;
    background: var(--cb-border); margin: 0 auto;
}
.ca-tree-branch {
    position: relative; width: 24px; height: 20px; flex-shrink: 0;
    display: flex; align-items: center;
}
.ca-tree-branch::before {
    content: ''; position: absolute;
    left: 50%; top: 0; width: 1px; height: 50%;
    background: var(--cb-border);
}
.ca-tree-branch::after {
    content: ''; position: absolute;
    left: 50%; top: 50%; width: 50%; height: 1px;
    background: var(--cb-border);
}

/* Node dot */
.ca-node-dot {
    width: 10px; height: 10px; border-radius: 50%; flex-shrink: 0;
    border: 2px solid;
}
.ca-node-dot.root { background: #f59e0b; border-color: #d97706; }
.ca-node-dot.parent { background: var(--cb-accent-light); border-color: var(--cb-accent); }
.ca-node-dot.leaf { background: var(--cb-bg); border-color: var(--cb-border); }

/* Name + meta */
.ca-name-wrap { margin-left: 10px; }
.ca-name {
    font-size: 14px; font-weight: 600; color: var(--cb-text);
}
.ca-name.root-name { font-family: 'Roboto', sans-serif;font-size: 16px; }
.ca-meta { font-size: 10px; color: var(--cb-muted); margin-top: 2px; }
.ca-meta code { display: none; }

/* Badges */
.ca-badge {
    display: inline-flex; align-items: center;
    font-size: 11px; font-weight: 600;
    padding: 2px 9px; border-radius: 999px;
}
.ca-badge-children { background: var(--cb-accent-light); color: var(--cb-accent); }
.ca-badge-leaf     { background: var(--cb-bg); color: var(--cb-muted); border: 1px solid var(--cb-border); }
.ca-badge-books    { background: #e0f2fe; color: #0284c7; }
.ca-badge-no-books { background: var(--cb-bg); color: var(--cb-muted); border: 1px solid var(--cb-border); }

/* Action buttons */
.ca-btn-edit {
    font-family: var(--cb-sans); font-size: 12px; font-weight: 600;
    padding: 6px 13px; border-radius: 8px;
    border: 1.5px solid var(--cb-border); background: var(--cb-white);
    color: var(--cb-text); cursor: pointer; transition: all .18s;
    display: inline-flex; align-items: center; gap: 4px; white-space: nowrap;
}
.ca-btn-edit:hover { border-color: var(--cb-accent); color: var(--cb-accent); }
.ca-btn-del {
    font-family: var(--cb-sans); font-size: 12px; font-weight: 600;
    padding: 6px 13px; border-radius: 8px;
    border: 1.5px solid #fecdd3; background: transparent;
    color: #dc2626; cursor: pointer; transition: background .18s;
    display: inline-flex; align-items: center; gap: 4px; white-space: nowrap;
}
.ca-btn-del:hover { background: #fff1f2; }

/* Empty state */
.ca-empty {
    padding: 56px 32px; text-align: center;
}
.ca-empty h3 {
    font-family: var(--cb-serif); font-size: 20px; font-weight: 700;
    color: var(--cb-text); margin-bottom: 6px;
}
.ca-empty p { font-family: var(--cb-sans); font-size: 13px; color: var(--cb-muted); }

/* ─── Modal shared ────────────────────────────────────── */
.ca-modal-wrap {
    position: fixed; inset: 0; z-index: 60;
    display: none; /* default hidden — .is-open shows it */
    align-items: flex-start; justify-content: center;
    padding: 32px 16px; overflow-y: auto;
    background: rgba(13,27,16,.52);
    backdrop-filter: blur(3px);
}
.ca-modal-wrap.is-open { display: flex; }

.ca-modal {
    background: var(--cb-white); border-radius: 20px;
    width: 100%; max-width: 540px;
    box-shadow: 0 24px 60px rgba(0,0,0,.16);
    overflow: hidden; position: relative;
    margin: auto 0; /* vertical centering fallback */
}
.ca-modal-head {
    padding: 20px 26px 16px;
    border-bottom: 1px solid var(--cb-border);
    display: flex; align-items: flex-start; justify-content: space-between; gap: 12px;
    position: relative;
}
.ca-modal-head::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
    background: linear-gradient(90deg, #f59e0b, var(--cb-accent));
}
.ca-modal-title { font-family: var(--cb-serif); font-size: 20px; font-weight: 700; color: var(--cb-text); margin: 0; }
.ca-modal-sub   { font-family: var(--cb-sans); font-size: 13px; color: var(--cb-muted); margin: 4px 0 0; }
.ca-modal-close {
    width: 30px; height: 30px; border-radius: 8px;
    border: 1.5px solid var(--cb-border); background: transparent;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer; color: var(--cb-muted); transition: all .18s; flex-shrink: 0;
}
.ca-modal-close:hover { border-color: var(--cb-text); color: var(--cb-text); }

.ca-modal-body { padding: 20px 26px; display: flex; flex-direction: column; gap: 16px; }
.ca-modal-foot {
    padding: 0 26px 22px;
    display: flex; justify-content: flex-end; gap: 10px;
}

/* Form fields */
.ca-field { display: flex; flex-direction: column; gap: 6px; }
.ca-field-label {
    font-family: var(--cb-sans); font-size: 12px; font-weight: 600; color: var(--cb-text);
}
.ca-field-input {
    font-family: var(--cb-sans); font-size: 13px;
    padding: 10px 14px; border: 1.5px solid var(--cb-border);
    border-radius: 9px; background: var(--cb-white); color: var(--cb-text);
    outline: none; transition: border-color .2s, box-shadow .2s;
    width: 100%; box-sizing: border-box; appearance: none;
}
.ca-field-input:focus {
    border-color: var(--cb-accent);
    box-shadow: 0 0 0 3px rgba(45,106,79,.09);
}

/* Modal buttons */
.ca-modal-submit {
    font-family: var(--cb-sans); font-size: 13px; font-weight: 600;
    padding: 10px 22px; border-radius: 9px; border: none;
    background: var(--cb-text); color: #fff; cursor: pointer;
    transition: background .2s; display: inline-flex; align-items: center; gap: 7px;
}
.ca-modal-submit:hover { background: var(--cb-accent); }
.ca-modal-cancel {
    font-family: var(--cb-sans); font-size: 13px; font-weight: 500;
    padding: 10px 18px; border-radius: 9px;
    border: 1.5px solid var(--cb-border); background: transparent;
    color: var(--cb-muted); cursor: pointer; transition: all .18s;
}
.ca-modal-cancel:hover { border-color: var(--cb-text); color: var(--cb-text); }

/* Category type tag in select */
.ca-parent-hint {
    font-family: var(--cb-sans); font-size: 11px; color: var(--cb-muted); margin-top: 4px;
}
</style>
@endsection

@section('content')
{{-- ── Page header ──────────────────────────────────────── --}}
<div class="ca-header">
    <div>
        <h1 class="ca-header-title">Quản lý danh mục</h1>
        <p class="ca-header-sub">Xem cây phân cấp, thêm mới và chỉnh sửa danh mục.</p>
    </div>
    <div class="ca-header-right">
        <form method="GET" action="{{ route('admin.categories.index') }}">
            <div class="ca-search-wrap">
                <span class="ca-search-icon">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                </span>
                <input name="q" value="{{ $q }}"
                       placeholder="Tìm danh mục..."
                       class="ca-search-input">
            </div>
        </form>
        <button type="button" id="openCreateCategoryModal" class="ca-btn-add">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Thêm danh mục
        </button>
    </div>
</div>

{{-- ── Stats ─────────────────────────────────────────────── --}}
<div class="ca-stats">
    <div class="ca-stat ca-stat-total">
        <p class="ca-stat-lbl">Tổng danh mục</p>
        <p class="ca-stat-val">{{ $totalCategories }}</p>
    </div>
    <div class="ca-stat ca-stat-root">
        <p class="ca-stat-lbl">Danh mục gốc</p>
        <p class="ca-stat-val">{{ $rootCategories }}</p>
    </div>
    <div class="ca-stat ca-stat-leaf">
        <p class="ca-stat-lbl">Danh mục lá</p>
        <p class="ca-stat-val">{{ $leafCategories }}</p>
    </div>
    <div class="ca-stat ca-stat-books">
        <p class="ca-stat-lbl">Có gắn sách</p>
        <p class="ca-stat-val">{{ $categoriesWithBooks }}</p>
    </div>
</div>

{{-- ── Tree table ────────────────────────────────────────── --}}
<div class="ca-tree-card">
    <div class="ca-tree-toolbar">
        <span class="ca-tree-toolbar-title">Cây danh mục</span>
        <div class="ca-tree-legend">
            <span class="ca-legend-item">
                <span class="ca-legend-dot" style="background:#f59e0b;border:2px solid #d97706"></span>
                Danh mục gốc
            </span>
            <span class="ca-legend-item">
                <span class="ca-legend-dot" style="background:var(--cb-accent-light);border:2px solid var(--cb-accent)"></span>
                Có danh mục con
            </span>
            <span class="ca-legend-item">
                <span class="ca-legend-dot" style="background:var(--cb-bg);border:2px solid var(--cb-border)"></span>
                Danh mục lá
            </span>
        </div>
    </div>

    <div style="overflow-x:auto">
        <table class="ca-table">
            <thead>
                <tr>
                    <th style="min-width:320px">Danh mục</th>
                    <th>Sách</th>
                    <th>Danh mục con</th>
                    <th style="text-align:right">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categories as $category)
                    @php
                        $depth    = (int)($category->depth ?? 0);
                        $hasKids  = (int)($category->children_count ?? 0) > 0;
                        $isRoot   = $depth === 0;
                        $dotClass = $isRoot ? 'root' : ($hasKids ? 'parent' : 'leaf');
                        $nameClass = $isRoot ? 'root-name' : '';
                    @endphp
                    <tr>
                        {{-- Tree cell --}}
                        <td>
                            <div class="ca-row-wrap">
                                {{-- Indent levels --}}
                                @for($d = 0; $d < $depth; $d++)
                                    <div class="ca-indent-unit">
                                        @if($d === $depth - 1)
                                            <div class="ca-tree-branch"></div>
                                        @else
                                            <div style="width:1px;height:100%;min-height:20px;background:var(--cb-border);margin:0 auto"></div>
                                        @endif
                                    </div>
                                @endfor

                                {{-- Node dot --}}
                                <div class="ca-node-dot {{ $dotClass }}"></div>

                                {{-- Name --}}
                                <div class="ca-name-wrap">
                                    <div class="ca-name {{ $nameClass }}">{{ $category->name }}</div>
                                    <div class="ca-meta">
                                        @if($category->parent)
                                            <span>Cha: {{ $category->parent->name }}</span>
                                        @else
                                            <span>Danh mục gốc</span>
                                        @endif
                                        &nbsp;·&nbsp;
                                        <code style="font-size:10px;color:var(--cb-muted);background:var(--cb-bg);padding:1px 5px;border-radius:4px">
                                            {{ $category->slug ?? '—' }}
                                        </code>
                                    </div>
                                </div>
                            </div>
                        </td>

                        {{-- Books count --}}
                        <td>
                            @php $bk = (int)($category->books_count ?? 0); @endphp
                            <span class="ca-badge {{ $bk > 0 ? 'ca-badge-books' : 'ca-badge-no-books' }}">
                                {{ $bk }} sách
                            </span>
                        </td>

                        {{-- Children count --}}
                        <td>
                            @php $ck = (int)($category->children_count ?? 0); @endphp
                            @if($ck > 0)
                                <span class="ca-badge ca-badge-children">{{ $ck }} con</span>
                            @else
                                <span class="ca-badge ca-badge-leaf">Lá</span>
                            @endif
                        </td>

                        {{-- Actions --}}
                        <td style="text-align:right">
                            <div style="display:flex;align-items:center;justify-content:flex-end;gap:8px">
                                <button type="button"
                                        class="ca-btn-edit"
                                        data-edit-category="true"
                                        data-category-id="{{ $category->id }}"
                                        data-category-name="{{ e($category->name) }}"
                                        data-category-parent-id="{{ $category->parent_id ?? '' }}">
                                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                                        <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                    </svg>
                                    Sửa
                                </button>

                                <form method="POST"
                                      action="{{ route('admin.categories.destroy', $category) }}"
                                      class="inline-block"
                                      onsubmit="return confirm('Xoá danh mục này?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="ca-btn-del">
                                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <polyline points="3 6 5 6 21 6"/>
                                            <path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/>
                                        </svg>
                                        Xoá
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4">
                            <div class="ca-empty">
                                <svg width="48" height="48" fill="none" stroke="var(--cb-border)" stroke-width="1.4" viewBox="0 0 24 24" style="margin:0 auto 14px">
                                    <path d="M22 19a2 2 0 01-2 2H4a2 2 0 01-2-2V5a2 2 0 012-2h5l2 3h9a2 2 0 012 2z"/>
                                </svg>
                                <h3>Không có danh mục phù hợp</h3>
                                <p>Thử thay đổi từ khoá hoặc thêm danh mục mới.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- ════════════════════════════════════════════════════════
     CREATE MODAL
════════════════════════════════════════════════════════ --}}
<div id="createCategoryModal" class="ca-modal-wrap {{ $openCreateModal ? 'is-open' : '' }}">
    <div class="ca-modal">
        <div class="ca-modal-head">
            <div>
                <h2 class="ca-modal-title">Thêm danh mục mới</h2>
                <p class="ca-modal-sub">Tạo danh mục gốc hoặc gán vào danh mục cha.</p>
            </div>
            <button type="button" id="closeCreateCategoryModal" class="ca-modal-close">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>

        <form method="POST" action="{{ route('admin.categories.store') }}">
            @csrf
            <input type="hidden" name="_form" value="create-category">

            <div class="ca-modal-body">
                <div class="ca-field">
                    <label class="ca-field-label">Tên danh mục</label>
                    <input type="text" name="name" value="{{ old('name') }}"
                           class="ca-field-input" placeholder="Nhập tên danh mục" required>
                </div>

                <div class="ca-field">
                    <label class="ca-field-label">Danh mục cha</label>
                    <select name="parent_id" class="ca-field-input">
                        <option value="">— Danh mục gốc (không có cha)</option>
                        @foreach($allCategories as $option)
                            <option value="{{ $option->id }}"
                                    @selected((string)$createSelectedParentId === (string)$option->id)>
                                {{ str_repeat('— ', (int)($option->depth ?? 0)) }}{{ $option->name }}
                            </option>
                        @endforeach
                    </select>
                    <span class="ca-parent-hint">Để trống nếu muốn tạo danh mục gốc.</span>
                </div>
            </div>

            <div class="ca-modal-foot">
                <button type="button" id="cancelCreateCategoryModal" class="ca-modal-cancel">Huỷ</button>
                <button type="submit" class="ca-modal-submit">
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                    Tạo danh mục
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ════════════════════════════════════════════════════════
     EDIT MODAL
════════════════════════════════════════════════════════ --}}
<div id="editCategoryModal" class="ca-modal-wrap {{ $editingCategory ? 'is-open' : '' }}">
    <div class="ca-modal">
        <div class="ca-modal-head">
            <div>
                <h2 class="ca-modal-title">Chỉnh sửa danh mục</h2>
                <p class="ca-modal-sub">Cập nhật tên và danh mục cha.</p>
            </div>
            <button type="button" id="closeEditCategoryModal" class="ca-modal-close">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>

        <form method="POST"
              action="{{ $editingCategory ? route('admin.categories.update', $editingCategory) : '#' }}"
              id="editCategoryForm">
            @csrf
            @method('PATCH')
            <input type="hidden" name="_form"        value="update-category">
            <input type="hidden" name="_category_id" value="{{ $editingCategory?->id }}">

            <div class="ca-modal-body">
                <div class="ca-field">
                    <label class="ca-field-label">Tên danh mục</label>
                    <input type="text" name="name"
                           value="{{ $editingCategory ? old('name', $editingCategory->name) : '' }}"
                           class="ca-field-input" placeholder="Nhập tên danh mục" required>
                </div>

                <div class="ca-field">
                    <label class="ca-field-label">Danh mục cha</label>
                    <select name="parent_id" class="ca-field-input">
                        <option value="">— Danh mục gốc (không có cha)</option>
                        @foreach($allCategories as $option)
                            @continue($editingCategory && (int)$option->id === (int)$editingCategory->id)
                            <option value="{{ $option->id }}"
                                    @selected((string)$editSelectedParentId === (string)$option->id)>
                                {{ str_repeat('— ', (int)($option->depth ?? 0)) }}{{ $option->name }}
                            </option>
                        @endforeach
                    </select>
                    <span class="ca-parent-hint">Để trống nếu muốn đặt làm danh mục gốc.</span>
                </div>
            </div>

            <div class="ca-modal-foot">
                <button type="button" id="cancelEditCategoryModal" class="ca-modal-cancel">Huỷ</button>
                <button type="submit" class="ca-modal-submit">
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                    Lưu thay đổi
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@section('scripts')
{{-- ── JavaScript (logic giữ nguyên từ file gốc) ───────────── --}}
<script>
(function () {
    const createModal = document.getElementById('createCategoryModal');
    const editModal   = document.getElementById('editCategoryModal');
    const editForm    = document.getElementById('editCategoryForm');

    const openModal  = m => m?.classList.add('is-open');
    const closeModal = m => m?.classList.remove('is-open');

    document.getElementById('openCreateCategoryModal')
        ?.addEventListener('click', () => openModal(createModal));
    document.getElementById('closeCreateCategoryModal')
        ?.addEventListener('click', () => closeModal(createModal));
    document.getElementById('cancelCreateCategoryModal')
        ?.addEventListener('click', () => closeModal(createModal));

    document.getElementById('closeEditCategoryModal')
        ?.addEventListener('click', () => closeModal(editModal));
    document.getElementById('cancelEditCategoryModal')
        ?.addEventListener('click', () => closeModal(editModal));

    /* Click outside to close */
    [createModal, editModal].forEach(m => {
        m?.addEventListener('click', e => { if (e.target === m) closeModal(m); });
    });

    /* Edit buttons — populate form */
    document.querySelectorAll('[data-edit-category="true"]').forEach(btn => {
        btn.addEventListener('click', () => {
            if (!editForm) return;
            const id       = btn.dataset.categoryId       || '';
            const name     = btn.dataset.categoryName     || '';
            const parentId = btn.dataset.categoryParentId || '';

            editForm.action = `/admin/categories/${id}`;
            editForm.querySelector('input[name="_category_id"]').value = id;
            editForm.querySelector('input[name="name"]').value         = name;
            editForm.querySelector('select[name="parent_id"]').value   = parentId;

            openModal(editModal);
        });
    });

    /* ESC key */
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') { closeModal(createModal); closeModal(editModal); }
    });
})();
</script>

@endsection