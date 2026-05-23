@extends('layouts.admin', ['title' => 'Quản lý sách'])

@section('content')

@php
    $q          = $q          ?? trim((string) request()->query('q', ''));
    $books      = $books      ?? collect();
    $publishers = $publishers ?? collect();
    $categories = $categories ?? collect();
    $authors    = $authors    ?? collect();
    $authorsById = $authors->keyBy('id');

    $openCreateModal  = old('_form') === 'create-book';
    $openEditBookId   = old('_form') === 'update-book' ? (int) old('_book_id') : null;
    $editingBook      = $openEditBookId
        ? \App\Models\Book::query()->with(['publisher','categories:id','authors:id,name'])->find($openEditBookId)
        : null;

    $selectedCreateCategories = collect(old('category_ids', []))->filter(fn($id)=>is_numeric($id))->map(fn($id)=>(int)$id)->all();
    $selectedEditCategories   = collect(old('category_ids', $editingBook ? $editingBook->categories->pluck('id')->all() : []))->filter(fn($id)=>is_numeric($id))->map(fn($id)=>(int)$id)->all();

    $selectedCreateAuthors      = collect(old('author_ids', []))->filter(fn($id)=>is_numeric($id))->map(fn($id)=>(int)$id)->all();
    $selectedCreateAuthorNames  = collect(old('author_names', []))->map(fn($n)=>trim((string)$n))->filter()->values()->all();
    $selectedEditAuthors        = collect(old('author_ids', $editingBook ? $editingBook->authors->pluck('id')->all() : []))->filter(fn($id)=>is_numeric($id))->map(fn($id)=>(int)$id)->all();
    $selectedEditAuthorNames    = collect(old('author_names', $editingBook ? $editingBook->authors->pluck('name')->all() : []))->map(fn($n)=>trim((string)$n))->filter()->values()->all();

    $createAuthorItems = collect($selectedCreateAuthors)->map(fn($id)=>($a=$authorsById->get($id))?['type'=>'existing','id'=>(int)$a->id,'name'=>$a->name]:null)->filter()->values()->all();
    $createAuthorItems = array_merge($createAuthorItems, collect($selectedCreateAuthorNames)->map(fn($n)=>['type'=>'new','id'=>null,'name'=>$n])->values()->all());

    $editAuthorItems = collect($selectedEditAuthors)->map(fn($id)=>($a=$authorsById->get($id))?['type'=>'existing','id'=>(int)$a->id,'name'=>$a->name]:null)->filter()->values()->all();
    $editAuthorItems = array_merge($editAuthorItems, collect($selectedEditAuthorNames)->map(fn($n)=>['type'=>'new','id'=>null,'name'=>$n])->values()->all());

    $statusLabels = ['available'=>'Đang bán','hidden'=>'Đang ẩn','out_of_stock'=>'Hết hàng'];
@endphp

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
html, body {
    background: var(--cb-bg);
    margin: 0;
}
/* ─── Page header ─────────────────────────────────────── */
.bk-header {
    background: var(--cb-white); border: 1px solid var(--cb-border);
    border-radius: 18px; padding: 20px 26px;
    display: flex; align-items: flex-end; justify-content: space-between;
    gap: 20px; flex-wrap: wrap; margin-bottom: 16px;
    position: relative; overflow: hidden;
    max-width: 1315px;
    margin: 0 auto 16px;
}
.bk-header::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
    background: linear-gradient(90deg, var(--cb-accent), #4ade80);
}
.bk-header-title {
    font-family: var(--cb-serif); font-size: 22px; font-weight: 900;
    color: #0d1b10; letter-spacing: -.5px; margin: 0 0 3px;
}
.bk-header-sub { font-family: var(--cb-sans); font-size: 13px; color: var(--cb-muted); }
.bk-header-right { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }

.bk-search-wrap {
    display: flex; border: 1.5px solid var(--cb-border); border-radius: 10px;
    overflow: hidden; background: var(--cb-white); transition: border-color .2s;
}
.bk-search-wrap:focus-within { border-color: var(--cb-accent); }
.bk-search-icon { padding: 0 10px 0 12px; display: flex; align-items: center; color: var(--cb-muted); }
.bk-search-input {
    font-family: var(--cb-sans); font-size: 13px; border: none; outline: none;
    background: transparent; color: var(--cb-text); padding: 9px 14px 9px 0; width: 230px;
}
.bk-search-input::placeholder { color: #c0b8b0; }
.bk-btn-add {
    font-family: var(--cb-sans); font-size: 13px; font-weight: 600;
    padding: 10px 18px; border-radius: 10px; border: none;
    background: var(--cb-text); color: #fff; cursor: pointer;
    display: inline-flex; align-items: center; gap: 7px;
    transition: background .2s; white-space: nowrap;
}
.bk-btn-add:hover { background: var(--cb-accent); }

/* ─── Table card ──────────────────────────────────────── */
.bk-table-card {
    background: var(--cb-white); border: 1px solid var(--cb-border);
    border-radius: 18px; overflow: hidden; margin-bottom: 16px;
    max-width: 1315px;
    margin: 0 auto 16px;
}
.bk-table { width: 100%; border-collapse: collapse; font-family: var(--cb-sans); }
.bk-table thead tr { border-bottom: 1px solid var(--cb-border); }
.bk-table th {
    padding: 11px 18px; font-size: 11px; font-weight: 700;
    letter-spacing: 1.2px; text-transform: uppercase; color: #b0a898; text-align: left;
}
.bk-table tbody tr { border-bottom: 1px solid var(--cb-border); transition: background .15s; }
.bk-table tbody tr:last-child { border-bottom: none; }
.bk-table tbody tr:hover { background: #fdfcfa; }
.bk-table td { padding: 14px 18px; vertical-align: top; }

/* Book info cell */
.bk-info-cell { display: flex; align-items: flex-start; gap: 12px; }
.bk-thumb {
    width: 44px; height: 60px; border-radius: 8px; flex-shrink: 0;
    background: #ede9e1; overflow: hidden;
    display: flex; align-items: center; justify-content: center;
    border: 1px solid var(--cb-border);
    font-family: var(--cb-serif); font-size: 18px; font-weight: 900; color: #c5bdb0;
}
.bk-thumb img { width: 100%; height: 100%; object-fit: cover; }
.bk-title { font-size: 14px; font-weight: 600; color: var(--cb-text); margin-bottom: 4px; line-height: 1.35; }
.bk-isbn  { font-size: 11px; color: var(--cb-muted); margin-bottom: 6px; }
.bk-tags  { display: flex; flex-wrap: wrap; gap: 4px; }
.bk-tag {
    font-size: 11px; font-weight: 500; padding: 2px 8px; border-radius: 999px;
    background: var(--cb-bg); border: 1px solid var(--cb-border); color: var(--cb-muted);
}

/* Price cell */
.bk-price      { font-size: 15px; font-weight: 700; color: var(--cb-accent); }
.bk-price-orig { font-size: 11px; color: #c0b8b0; text-decoration: line-through; margin-top: 2px; }
.bk-stock      { font-size: 12px; margin-top: 6px; }
.bk-stock.ok   { color: var(--cb-accent); }
.bk-stock.low  { color: #f59e0b; }
.bk-stock.out  { color: #dc2626; }

/* Status badge */
.bk-status {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: 11px; font-weight: 600; padding: 4px 10px; border-radius: 999px; border: 1px solid;
}
.bk-status-available  { background: var(--cb-accent-light); color: var(--cb-accent); border-color: #86efac; }
.bk-status-hidden     { background: var(--cb-bg); color: var(--cb-muted); border-color: var(--cb-border); }
.bk-status-out_of_stock { background: #fff1f2; color: #dc2626; border-color: #fecdd3; }

.bk-status-dot {
    width: 6px; height: 6px; border-radius: 50%;
    flex-shrink: 0; display: inline-block;
}
.bk-status-available .bk-status-dot { background: #2d6a4f; }
.bk-status-hidden .bk-status-dot { background: #999; }
.bk-status-out_of_stock .bk-status-dot { background: #dc2626; }

/* Action buttons */
.bk-btn-edit {
    font-family: var(--cb-sans); font-size: 12px; font-weight: 600;
    padding: 7px 13px; border-radius: 8px;
    border: 1.5px solid var(--cb-border); background: var(--cb-white);
    color: var(--cb-text); cursor: pointer; transition: all .18s;
    display: inline-flex; align-items: center; gap: 4px; white-space: nowrap;
}
.bk-btn-edit:hover { border-color: var(--cb-accent); color: var(--cb-accent); }
.bk-btn-del {
    font-family: var(--cb-sans); font-size: 12px; font-weight: 600;
    padding: 7px 13px; border-radius: 8px;
    border: 1.5px solid #fecdd3; background: transparent;
    color: #dc2626; cursor: pointer; transition: background .18s;
    display: inline-flex; align-items: center; gap: 4px; white-space: nowrap;
}
.bk-btn-del:hover { background: #fff1f2; }

/* Empty state */
.bk-empty { padding: 60px 32px; text-align: center; }
.bk-empty h3 {
    font-family: var(--cb-serif); font-size: 20px; font-weight: 700;
    color: var(--cb-text); margin-bottom: 6px;
}
.bk-empty p { font-family: var(--cb-sans); font-size: 13px; color: var(--cb-muted); }

/* ─── Modal ───────────────────────────────────────────── */
.bk-modal-wrap {
    position: fixed; inset: 0; z-index: 60;
    display: none;
    align-items: flex-start; justify-content: center;
    overflow-y: auto; padding: 24px 16px;
    background: rgba(13,27,16,.52);
    backdrop-filter: blur(3px);
}
.bk-modal-wrap.is-open { display: flex; }

.bk-modal {
    background: var(--cb-white); border-radius: 20px;
    width: 100%; max-width: 920px;
    box-shadow: 0 24px 60px rgba(0,0,0,.16);
    overflow: hidden; margin: auto 0;
}
.bk-modal-head {
    padding: 20px 26px 16px;
    border-bottom: 1px solid var(--cb-border);
    display: flex; align-items: flex-start; justify-content: space-between; gap: 12px;
    position: relative;
}
.bk-modal-head::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
    background: linear-gradient(90deg, var(--cb-accent), #4ade80);
}
.bk-modal-title { font-family: var(--cb-serif); font-size: 20px; font-weight: 700; color: var(--cb-text); margin: 0; }
.bk-modal-sub   { font-family: var(--cb-sans); font-size: 13px; color: var(--cb-muted); margin: 4px 0 0; }
.bk-modal-close {
    width: 30px; height: 30px; border-radius: 8px;
    border: 1.5px solid var(--cb-border); background: transparent;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer; color: var(--cb-muted); transition: all .18s; flex-shrink: 0;
}
.bk-modal-close:hover { border-color: var(--cb-text); color: var(--cb-text); }
.bk-modal-body { padding: 22px 26px; }
.bk-modal-foot {
    padding: 14px 26px 22px;
    border-top: 1px solid var(--cb-border);
    display: flex; justify-content: flex-end; gap: 10px;
}

/* 2-col form grid */
.bk-form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 14px; }
@media (max-width: 640px) { .bk-form-grid { grid-template-columns: 1fr; } }
.bk-col-2 { grid-column: 1 / -1; }

/* Form fields */
.bk-field { display: flex; flex-direction: column; gap: 6px; }
.bk-label {
    font-family: var(--cb-sans); font-size: 12px; font-weight: 600;
    color: var(--cb-text); display: flex; align-items: center; gap: 5px;
}
.bk-req { color: #dc2626; font-size: 11px; }
.bk-input {
    font-family: var(--cb-sans); font-size: 13px;
    padding: 10px 14px; border: 1.5px solid var(--cb-border);
    border-radius: 9px; background: var(--cb-white); color: var(--cb-text);
    outline: none; transition: border-color .2s, box-shadow .2s;
    width: 100%; box-sizing: border-box; appearance: none;
}
.bk-input:focus {
    border-color: var(--cb-accent);
    box-shadow: 0 0 0 3px rgba(45,106,79,.09);
}
.bk-input::placeholder { color: #c0b8b0; }
.bk-input:-webkit-autofill,
.bk-input:-webkit-autofill:hover,
.bk-input:-webkit-autofill:focus,
.bk-input:-webkit-autofill:active {
    -webkit-box-shadow: 0 0 0 1000px var(--cb-white) inset !important;
    -webkit-text-fill-color: var(--cb-text) !important;
    caret-color: var(--cb-text);
}
/* Section separator inside form */
.bk-form-sep {
    grid-column: 1 / -1; height: 1px;
    background: var(--cb-border); margin: 4px 0;
}
.bk-form-section-lbl {
    grid-column: 1 / -1;
    font-family: var(--cb-sans); font-size: 11px; font-weight: 700;
    letter-spacing: 1.3px; text-transform: uppercase; color: #b0a898;
}

/* Modal buttons */
.bk-modal-submit {
    font-family: var(--cb-sans); font-size: 13px; font-weight: 600;
    padding: 10px 22px; border-radius: 9px; border: none;
    background: var(--cb-text); color: #fff; cursor: pointer;
    transition: background .2s; display: inline-flex; align-items: center; gap: 7px;
}
.bk-modal-submit:hover { background: var(--cb-accent); }
.bk-modal-cancel {
    font-family: var(--cb-sans); font-size: 13px; font-weight: 500;
    padding: 10px 18px; border-radius: 9px;
    border: 1.5px solid var(--cb-border); background: transparent;
    color: var(--cb-muted); cursor: pointer; transition: all .18s;
}
.bk-modal-cancel:hover { border-color: var(--cb-text); color: var(--cb-text); }

/* ─── Author picker (style only — logic untouched) ────── */
.author-picker { position: relative; display: flex; flex-direction: column; gap: 10px; }
.author-picker-tags { display: flex; flex-wrap: wrap; gap: 6px; }
.author-tag {
    display: inline-flex; align-items: center; gap: 7px;
    font-family: var(--cb-sans); font-size: 12px; font-weight: 600;
    padding: 5px 10px; border-radius: 999px;
    background: var(--cb-accent-light); color: var(--cb-accent);
    border: 1px solid #86efac;
}
.author-tag--new {
    background: #fff7ed; color: #c2410c; border-color: #fed7aa;
}
.author-tag-remove {
    width: 16px; height: 16px; border-radius: 50%; border: none;
    background: rgba(0,0,0,.1); color: inherit;
    font-size: 12px; cursor: pointer; line-height: 1;
    display: inline-flex; align-items: center; justify-content: center;
}
.author-picker-input { min-height: 44px; }
.author-picker-suggestions {
    position: absolute; left: 0; right: 0; top: calc(100% + 6px);
    z-index: 80; max-height: 240px; overflow-y: auto;
    border: 1px solid var(--cb-border); border-radius: 12px;
    background: var(--cb-white);
    box-shadow: 0 12px 32px rgba(0,0,0,.12);
}
.author-suggestion-item {
    display: flex; width: 100%; align-items: center; justify-content: space-between;
    gap: 12px; border: none; border-bottom: 1px solid var(--cb-border);
    background: var(--cb-white); padding: 10px 14px; text-align: left; cursor: pointer;
    font-family: var(--cb-sans);
}
.author-suggestion-item:hover { background: var(--cb-bg); }
.author-suggestion-item:last-child { border-bottom: none; }
.author-suggestion-name { font-size: 13px; font-weight: 600; color: var(--cb-text); }
.author-suggestion-meta { font-size: 11px; color: var(--cb-accent); font-weight: 500; }
</style>

{{-- ── Page header ──────────────────────────────────────── --}}
<div class="bk-header">
    <div>
        <h1 class="bk-header-title">Quản lý sách</h1>
        <p class="bk-header-sub">Thêm mới, chỉnh sửa, xoá và tìm kiếm đầu sách.</p>
    </div>
    <div class="bk-header-right">
        <form method="GET" action="{{ route('admin.books.index') }}">
            <div class="bk-search-wrap">
                <span class="bk-search-icon">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                </span>
                <input name="q" value="{{ $q }}"
                       placeholder="Tìm tên sách hoặc ISBN..."
                       class="bk-search-input">
            </div>
        </form>
        <button type="button" id="openCreateBookModal" class="bk-btn-add">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Thêm sách mới
        </button>
    </div>
</div>

{{-- ── Books table ───────────────────────────────────────── --}}
<div class="bk-table-card">
    <div style="overflow-x:auto">
        <table class="bk-table" style="min-width:960px">
            <thead>
                <tr>
                    <th>Thông tin sách</th>
                    <th>Giá & Kho</th>
                    <th>Trạng thái</th>
                    <th style="text-align:right">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($books as $book)
                    <tr>
                        {{-- Info --}}
                        <td>
                            <div class="bk-info-cell">
                                <div class="bk-thumb">
                                    @if($book->cover_image)
                                        <img src="{{ asset('storage/'.$book->cover_image) }}" alt="{{ $book->title }}">
                                    @else
                                        {{ mb_strtoupper(mb_substr($book->title, 0, 1)) }}
                                    @endif
                                </div>
                                <div>
                                    <p class="bk-title">{{ $book->title }}</p>
                                    <p class="bk-isbn">ISBN: {{ $book->isbn ?: 'N/A' }}</p>
                                    <div class="bk-tags">
                                        @if($book->publisher)
                                            <span class="bk-tag">{{ $book->publisher->name }}</span>
                                        @endif
                                        <span class="bk-tag">{{ $book->categories_count }} danh mục</span>
                                        <span class="bk-tag">{{ $book->authors->count() }} tác giả</span>
                                    </div>
                                </div>
                            </div>
                        </td>

                        {{-- Price & stock --}}
                        <td>
                            @if($book->discount_price)
                                <p class="bk-price">{{ number_format($book->discount_price, 0, ',', '.') }}đ</p>
                                <p class="bk-price-orig">{{ number_format($book->price, 0, ',', '.') }}đ</p>
                            @else
                                <p class="bk-price">{{ number_format($book->price, 0, ',', '.') }}đ</p>
                            @endif
                            @php $sq = $book->stock_quantity; @endphp
                            <p class="bk-stock {{ $sq > 10 ? 'ok' : ($sq > 0 ? 'low' : 'out') }}">
                                Tồn kho: {{ $sq }}
                            </p>
                        </td>

                        {{-- Status --}}
                        <td>
                            <span class="bk-status bk-status-{{ $book->status }}">
                                <span class="bk-status-dot"></span>
                                {{ $statusLabels[$book->status] ?? $book->status }}
                            </span>
                        </td>

                        {{-- Actions --}}
                        <td style="text-align:right">
                            <div style="display:flex;align-items:center;justify-content:flex-end;gap:8px">
                                <button type="button"
                                        class="bk-btn-edit"
                                        data-edit-book="true"
                                        data-book-id="{{ $book->id }}"
                                        data-book-title="{{ e($book->title) }}"
                                        data-book-isbn="{{ e($book->isbn ?? '') }}"
                                        data-book-description="{{ e($book->description ?? '') }}"
                                        data-book-price="{{ $book->price }}"
                                        data-book-purchase-price="{{ $book->purchase_price ?? '' }}"
                                        data-book-discount-price="{{ $book->discount_price ?? '' }}"
                                        data-book-stock-quantity="{{ $book->stock_quantity }}"
                                        data-book-page-count="{{ $book->page_count ?? '' }}"
                                        data-book-language="{{ e($book->language ?? '') }}"
                                        data-book-format="{{ e($book->format ?? '') }}"
                                        data-book-publication-year="{{ $book->publication_year ?? '' }}"
                                        data-book-status="{{ $book->status }}"
                                        data-book-publisher-id="{{ $book->publisher_id ?? '' }}"
                                        data-book-category-ids='@json($book->categories->pluck("id")->map(fn($id)=>(int)$id)->values()->all())'
                                        data-book-author-items='@json($book->authors->map(fn($author)=>["type"=>"existing","id"=>(int)$author->id,"name"=>$author->name])->values()->all())'>
                                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                                        <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                    </svg>
                                    Sửa
                                </button>

                                <form method="POST"
                                      action="{{ route('admin.books.destroy', $book) }}"
                                      class="inline-block"
                                      onsubmit="return confirm('Xoá sách này?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="bk-btn-del">
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
                            <div class="bk-empty">
                                <svg width="48" height="48" fill="none" stroke="var(--cb-border)" stroke-width="1.4" viewBox="0 0 24 24" style="margin:0 auto 14px">
                                    <path d="M4 19.5A2.5 2.5 0 016.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 014 19.5v-15A2.5 2.5 0 016.5 2z"/>
                                </svg>
                                <h3>Không có sách phù hợp</h3>
                                <p>Thử thay đổi từ khoá hoặc thêm sách mới.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Pagination --}}
<div style="display:flex;justify-content:center">
    {{ $books->links() }}
</div>

{{-- ══ HELPER MACRO: form fields (giảm lặp lại) ══ --}}
@php
    /* dùng chung cho cả create và edit */
    function bkField(string $label, string $slot, bool $required = false): string {
        $req = $required ? '<span class="bk-req">*</span>' : '';
        return "<div class=\"bk-field\"><label class=\"bk-label\">{$label}{$req}</label>{$slot}</div>";
    }
@endphp

{{-- ════════════════════════════════════════════════════════
     CREATE MODAL
════════════════════════════════════════════════════════ --}}
<div id="createBookModal" class="bk-modal-wrap {{ $openCreateModal ? 'is-open' : '' }}">
    <div class="bk-modal">
        <div class="bk-modal-head">
            <div>
                <h2 class="bk-modal-title">Thêm sách mới</h2>
                <p class="bk-modal-sub">Điền thông tin và lưu vào database.</p>
            </div>
            <button type="button" id="closeCreateBookModal" class="bk-modal-close">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>

        <form method="POST" action="{{ route('admin.books.store') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="_form" value="create-book">
            <div class="bk-modal-body">
                <div class="bk-form-grid">

                    <div class="bk-col-2 bk-field">
                        <label class="bk-label">Tên sách <span class="bk-req">*</span></label>
                        <input type="text" name="title" value="{{ old('title') }}" required class="bk-input" placeholder="Tiêu đề đầy đủ">
                    </div>

                    <div class="bk-field">
                        <label class="bk-label">ISBN<span class="bk-req">*</span></label>
                        <input type="text" name="isbn" value="{{ old('isbn') }}" class="bk-input" placeholder="Tuỳ chọn">
                    </div>
                    <div class="bk-field">
                        <label class="bk-label">Nhà xuất bản</label>
                        <select name="publisher_id" class="bk-input">
                            <option value="">Chọn nhà xuất bản</option>
                            @foreach($publishers as $pub)
                                <option value="{{ $pub->id }}" @selected((string)old('publisher_id')===(string)$pub->id)>{{ $pub->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="bk-col-2 bk-field">
                        <label class="bk-label">Mô tả</label>
                        <textarea name="description" rows="3" class="bk-input">{{ old('description') }}</textarea>
                    </div>

                    <div class="bk-col-2 bk-form-sep"></div>
                    <div class="bk-form-section-lbl">Giá & Kho</div>

                    <div class="bk-field">
                        <label class="bk-label">Giá bán <span class="bk-req">*</span></label>
                        <input type="number" name="price" min="0" step="1000" value="{{ old('price') }}" required class="bk-input" placeholder="VND">
                    </div>
                    <div class="bk-field">
                        <label class="bk-label">Giá nhập<span class="bk-req">*</span></label>
                        <input type="number" name="purchase_price" min="0" step="0.01" value="{{ old('purchase_price') }}" class="bk-input" placeholder="VND">
                    </div>
                    <div class="bk-field">
                        <label class="bk-label">Giá khuyến mãi</label>
                        <input type="number" name="discount_price" min="0" step="1000" value="{{ old('discount_price') }}" class="bk-input" placeholder="Để trống nếu không có">
                    </div>
                    <div class="bk-field">
                        <label class="bk-label">Tồn kho <span class="bk-req">*</span></label>
                        <input type="number" name="stock_quantity" min="0" value="{{ old('stock_quantity', 0) }}" required class="bk-input">
                    </div>
                    <div class="bk-field">
                        <label class="bk-label">Trạng thái <span class="bk-req">*</span></label>
                        <select name="status" required class="bk-input">
                            <option value="available" @selected(old('status','available')==='available')>Đang bán</option>
                            <option value="hidden"    @selected(old('status')==='hidden')>Đang ẩn</option>
                            <option value="out_of_stock" @selected(old('status')==='out_of_stock')>Hết hàng</option>
                        </select>
                    </div>

                    <div class="bk-col-2 bk-form-sep"></div>
                    <div class="bk-form-section-lbl">Chi tiết xuất bản</div>

                    <div class="bk-field">
                        <label class="bk-label">Số trang</label>
                        <input type="number" name="page_count" min="1" value="{{ old('page_count') }}" class="bk-input">
                    </div>
                    <div class="bk-field">
                        <label class="bk-label">Ngôn ngữ</label>
                        <input type="text" name="language" value="{{ old('language') }}" class="bk-input" placeholder="Tiếng Việt / English...">
                    </div>
                    <div class="bk-field">
                        <label class="bk-label">Hình thức</label>
                        <input type="text" name="format" value="{{ old('format') }}" class="bk-input" placeholder="Bìa cứng / Bìa mềm...">
                    </div>
                    <div class="bk-field">
                        <label class="bk-label">Năm xuất bản</label>
                        <input type="number" name="publication_year" min="1900" max="{{ now()->year + 1 }}" value="{{ old('publication_year') }}" class="bk-input">
                    </div>

                    <div class="bk-col-2 bk-form-sep"></div>
                    <div class="bk-form-section-lbl">Phân loại & Tác giả</div>

                    <div class="bk-col-2 bk-field">
                        <label class="bk-label">Danh mục <span style="font-weight:400;color:var(--cb-muted)">(giữ Ctrl/Cmd để chọn nhiều)</span></label>
                        <select name="category_ids[]" multiple class="bk-input" size="6">
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" @selected(in_array((int)$cat->id,$selectedCreateCategories,true))>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="bk-col-2 bk-field">
                        <label class="bk-label">Tác giả</label>
                        <div class="author-picker"
                             data-author-picker
                             data-search-url="{{ route('admin.books.authors.search') }}"
                             data-author-items='@json($createAuthorItems)'>
                            <div class="author-picker-tags" data-author-tags></div>
                            <input type="text" class="bk-input author-picker-input" data-author-input
                                   placeholder="Gõ tên tác giả để tìm hoặc thêm mới..." autocomplete="off">
                            <div class="author-picker-suggestions hidden" data-author-suggestions></div>
                        </div>
                    </div>

                    <div class="bk-col-2 bk-field">
                        <label class="bk-label">Ảnh bìa</label>
                        <input type="file" name="cover_image_file" class="bk-input">
                    </div>
                </div>
            </div>
            <div class="bk-modal-foot">
                <button type="button" id="cancelCreateBookModal" class="bk-modal-cancel">Huỷ</button>
                <button type="submit" class="bk-modal-submit">
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                    Tạo sách
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ════════════════════════════════════════════════════════
     EDIT MODAL
════════════════════════════════════════════════════════ --}}
<div id="editBookModal" class="bk-modal-wrap {{ $editingBook ? 'is-open' : '' }}">
    <div class="bk-modal">
        <div class="bk-modal-head">
            <div>
                <h2 class="bk-modal-title">Chỉnh sửa sách</h2>
                <p class="bk-modal-sub">Cập nhật thông tin đầu sách đã có.</p>
            </div>
            <button type="button" id="closeEditBookModal" class="bk-modal-close">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>

        <form method="POST"
              action="{{ $editingBook ? route('admin.books.update', $editingBook) : '#' }}"
              enctype="multipart/form-data"
              id="editBookForm">
            @csrf
            @method('PATCH')
            <input type="hidden" name="_form"    value="update-book">
            <input type="hidden" name="_book_id" value="{{ $editingBook?->id }}">

            <div class="bk-modal-body">
                <div class="bk-form-grid">

                    <div class="bk-col-2 bk-field">
                        <label class="bk-label">Tên sách <span class="bk-req">*</span></label>
                        <input type="text" name="title" required class="bk-input"
                               value="{{ $editingBook ? old('title', $editingBook->title) : '' }}">
                    </div>

                    <div class="bk-field">
                        <label class="bk-label">ISBN</label>
                        <input type="text" name="isbn" class="bk-input"
                               value="{{ $editingBook ? old('isbn', $editingBook->isbn) : '' }}">
                    </div>
                    <div class="bk-field">
                        <label class="bk-label">Nhà xuất bản</label>
                        <select name="publisher_id" class="bk-input">
                            <option value="">Chọn nhà xuất bản</option>
                            @foreach($publishers as $pub)
                                <option value="{{ $pub->id }}"
                                    @selected((string)old('publisher_id',$editingBook?->publisher_id)===(string)$pub->id)>
                                    {{ $pub->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="bk-col-2 bk-field">
                        <label class="bk-label">Mô tả</label>
                        <textarea name="description" rows="3" class="bk-input">{{ $editingBook ? old('description', $editingBook->description) : '' }}</textarea>
                    </div>

                    <div class="bk-col-2 bk-form-sep"></div>
                    <div class="bk-form-section-lbl">Giá & Kho</div>

                    <div class="bk-field">
                        <label class="bk-label">Giá bán <span class="bk-req">*</span></label>
                        <input type="number" name="price" min="0" step="1000" required class="bk-input"
                               value="{{ $editingBook ? old('price', $editingBook->price) : '' }}">
                    </div>
                    <div class="bk-field">
                        <label class="bk-label">Giá nhập</label>
                        <input type="number" name="purchase_price" min="0" step="0.01" class="bk-input"
                               value="{{ $editingBook ? old('purchase_price', $editingBook->purchase_price) : '' }}">
                    </div>
                    <div class="bk-field">
                        <label class="bk-label">Giá khuyến mãi</label>
                        <input type="number" name="discount_price" min="0" step="1000" class="bk-input"
                               value="{{ $editingBook ? old('discount_price', $editingBook->discount_price) : '' }}">
                    </div>
                    <div class="bk-field">
                        <label class="bk-label">Tồn kho <span class="bk-req">*</span></label>
                        <input type="number" name="stock_quantity" min="0" required class="bk-input"
                               value="{{ $editingBook ? old('stock_quantity', $editingBook->stock_quantity) : 0 }}">
                    </div>
                    <div class="bk-field">
                        <label class="bk-label">Trạng thái <span class="bk-req">*</span></label>
                        <select name="status" required class="bk-input">
                            @php $es = $editingBook ? old('status',$editingBook->status) : 'available'; @endphp
                            <option value="available"    @selected($es==='available')>Đang bán</option>
                            <option value="hidden"       @selected($es==='hidden')>Đang ẩn</option>
                            <option value="out_of_stock" @selected($es==='out_of_stock')>Hết hàng</option>
                        </select>
                    </div>

                    <div class="bk-col-2 bk-form-sep"></div>
                    <div class="bk-form-section-lbl">Chi tiết xuất bản</div>

                    <div class="bk-field">
                        <label class="bk-label">Số trang</label>
                        <input type="number" name="page_count" min="1" class="bk-input"
                               value="{{ $editingBook ? old('page_count', $editingBook->page_count) : '' }}">
                    </div>
                    <div class="bk-field">
                        <label class="bk-label">Ngôn ngữ</label>
                        <input type="text" name="language" class="bk-input"
                               value="{{ $editingBook ? old('language', $editingBook->language) : '' }}">
                    </div>
                    <div class="bk-field">
                        <label class="bk-label">Hình thức</label>
                           <input type="text" name="format" class="bk-input"
                               value="{{ $editingBook ? old('format', $editingBook->format) : '' }}"
                               placeholder="Bìa cứng / Bìa mềm...">
                    </div>
                    <div class="bk-field">
                        <label class="bk-label">Năm xuất bản</label>
                        <input type="number" name="publication_year" min="1900" max="{{ now()->year + 1 }}" class="bk-input"
                               value="{{ $editingBook ? old('publication_year', $editingBook->publication_year) : '' }}">
                    </div>

                    <div class="bk-col-2 bk-form-sep"></div>
                    <div class="bk-form-section-lbl">Phân loại & Tác giả</div>

                    <div class="bk-col-2 bk-field">
                        <label class="bk-label">Danh mục <span style="font-weight:400;color:var(--cb-muted)">(giữ Ctrl/Cmd để chọn nhiều)</span></label>
                        <select name="category_ids[]" multiple class="bk-input" size="6" data-edit-categories-select>
                            @foreach($categories as $cat)
                                <option value="{{ $cat->id }}" @selected(in_array((int)$cat->id,$selectedEditCategories,true))>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="bk-col-2 bk-field">
                        <label class="bk-label">Tác giả</label>
                        <div class="author-picker"
                             data-author-picker
                             data-edit-author-picker
                             data-search-url="{{ route('admin.books.authors.search') }}"
                             data-author-items='@json($editAuthorItems)'>
                            <div class="author-picker-tags" data-author-tags></div>
                            <input type="text" class="bk-input author-picker-input" data-author-input
                                   placeholder="Gõ tên tác giả để tìm hoặc thêm mới..." autocomplete="off">
                            <div class="author-picker-suggestions hidden" data-author-suggestions></div>
                        </div>
                    </div>

                    <div class="bk-col-2 bk-field">
                        <label class="bk-label">Ảnh bìa mới</label>
                        <input type="file" name="cover_image_file" class="bk-input">
                        <span style="font-size:11px;color:var(--cb-muted)">Để trống nếu muốn giữ ảnh hiện tại.</span>
                    </div>
                </div>
            </div>

            <div class="bk-modal-foot">
                <button type="button" id="cancelEditBookModal" class="bk-modal-cancel">Huỷ</button>
                <button type="submit" class="bk-modal-submit">
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                    Lưu thay đổi
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ════════════════════════════════════════════════════════
     JAVASCRIPT — logic giữ nguyên từ file gốc
     Chỉ đổi hidden/show dùng .is-open thay vì .hidden
════════════════════════════════════════════════════════ --}}
<script>
(function () {
    /* ── helpers ── */
    const normalize = v => String(v ?? '').replace(/\s+/g,' ').trim();
    const lower     = v => normalize(v).toLowerCase();

    const openModal  = m => m?.classList.add('is-open');
    const closeModal = m => m?.classList.remove('is-open');

    /* ── modal elements ── */
    const createModal = document.getElementById('createBookModal');
    const editModal   = document.getElementById('editBookModal');
    const editForm    = document.getElementById('editBookForm');

    document.getElementById('openCreateBookModal')  ?.addEventListener('click', () => openModal(createModal));
    document.getElementById('closeCreateBookModal') ?.addEventListener('click', () => closeModal(createModal));
    document.getElementById('cancelCreateBookModal')?.addEventListener('click', () => closeModal(createModal));
    document.getElementById('closeEditBookModal')   ?.addEventListener('click', () => closeModal(editModal));
    document.getElementById('cancelEditBookModal')  ?.addEventListener('click', () => closeModal(editModal));

    [createModal, editModal].forEach(m => {
        m?.addEventListener('click', e => { if (e.target === m) closeModal(m); });
    });

    /* ── edit button populate ── */
    document.querySelectorAll('[data-edit-book="true"]').forEach(btn => {
        btn.addEventListener('click', () => {
            if (!editForm) return;
            const d = btn.dataset;
            editForm.action = `/admin/books/${d.bookId}`;
            editForm.querySelector('input[name="_book_id"]').value         = d.bookId        || '';
            editForm.querySelector('input[name="title"]').value            = d.bookTitle      || '';
            editForm.querySelector('input[name="isbn"]').value             = d.bookIsbn       || '';
            editForm.querySelector('textarea[name="description"]').value   = d.bookDescription|| '';
            editForm.querySelector('input[name="price"]').value            = d.bookPrice      || '';
            editForm.querySelector('input[name="purchase_price"]').value   = d.bookPurchasePrice || '';
            editForm.querySelector('input[name="discount_price"]').value   = d.bookDiscountPrice || '';
            editForm.querySelector('input[name="stock_quantity"]').value   = d.bookStockQuantity || '';
            editForm.querySelector('input[name="page_count"]').value       = d.bookPageCount  || '';
            editForm.querySelector('input[name="language"]').value         = d.bookLanguage   || '';
            editForm.querySelector('input[name="format"]').value     = d.bookFormat     || '';
            editForm.querySelector('input[name="publication_year"]').value = d.bookPublicationYear || '';
            editForm.querySelector('select[name="status"]').value          = d.bookStatus     || 'available';
            editForm.querySelector('select[name="publisher_id"]').value    = d.bookPublisherId|| '';

            const selectedCategoryIds = (() => {
                try {
                    const arr = JSON.parse(d.bookCategoryIds || '[]');
                    return Array.isArray(arr) ? arr.map(v => String(v)) : [];
                } catch {
                    return [];
                }
            })();
            const categorySelect = editForm.querySelector('[data-edit-categories-select]');
            if (categorySelect) {
                Array.from(categorySelect.options).forEach(option => {
                    option.selected = selectedCategoryIds.includes(String(option.value));
                });
            }

            const authorItems = (() => {
                try {
                    const arr = JSON.parse(d.bookAuthorItems || '[]');
                    return Array.isArray(arr) ? arr : [];
                } catch {
                    return [];
                }
            })();
            const editPicker = editForm.querySelector('[data-edit-author-picker]');
            const editTags = editPicker?.querySelector('[data-author-tags]');
            if (editTags) {
                editTags.innerHTML = '';
                authorItems.forEach(item => renderTag(editPicker, item));
            }

            openModal(editModal);
        });
    });

    /* ── ESC key ── */
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') { closeModal(createModal); closeModal(editModal); }
    });

    /* ════════════════════════════════════════════════
       AUTHOR PICKER — giữ nguyên hoàn toàn từ file gốc
    ════════════════════════════════════════════════ */
    const renderTag = (picker, item) => {
        const tags = picker.querySelector('[data-author-tags]');
        if (!tags) return;
        const normalizedName = normalize(item.name);
        if (!normalizedName) return;
        const currentNames = Array.from(tags.querySelectorAll('[data-author-tag-name]')).map(el => lower(el.textContent));
        const existingSameId   = item.type === 'existing' ? tags.querySelector(`input[name="author_ids[]"][value="${item.id}"]`) : null;
        const existingSameName = currentNames.includes(lower(normalizedName));
        if (existingSameId || existingSameName) return;

        const tag    = document.createElement('span');
        tag.className = `author-tag ${item.type === 'new' ? 'author-tag--new' : ''}`;
        tag.dataset.authorTag = 'true';

        const label = document.createElement('span');
        label.dataset.authorTagName = 'true';
        label.textContent = normalizedName;

        const hidden = document.createElement('input');
        hidden.type  = 'hidden';
        hidden.name  = item.type === 'existing' ? 'author_ids[]' : 'author_names[]';
        hidden.value = item.type === 'existing' ? String(item.id) : normalizedName;

        const remove = document.createElement('button');
        remove.type = 'button'; remove.className = 'author-tag-remove'; remove.textContent = '×';
        remove.addEventListener('click', () => tag.remove());

        tag.appendChild(label); tag.appendChild(hidden); tag.appendChild(remove);
        tags.appendChild(tag);
    };

    const renderSuggestions = async (picker, query) => {
        const suggestions = picker.querySelector('[data-author-suggestions]');
        if (!suggestions) return;
        const searchUrl = picker.dataset.searchUrl || '';
        const keyword   = normalize(query);
        if (keyword.length < 1) { suggestions.classList.add('hidden'); suggestions.innerHTML = ''; return; }

        const resp = await fetch(`${searchUrl}?q=${encodeURIComponent(keyword)}`, { headers: { Accept: 'application/json' } });
        if (!resp.ok) { suggestions.classList.add('hidden'); suggestions.innerHTML = ''; return; }

        const payload   = await resp.json();
        const authorsArr = Array.isArray(payload.data) ? payload.data : [];
        const canCreate  = Boolean(payload.can_create);

        suggestions.innerHTML = '';

        authorsArr.forEach(author => {
            const btn = document.createElement('button');
            btn.type = 'button'; btn.className = 'author-suggestion-item';
            btn.innerHTML = `<span class="author-suggestion-name">${author.name}</span><span class="author-suggestion-meta">Chọn</span>`;
            btn.addEventListener('click', () => {
                renderTag(picker, { type:'existing', id:author.id, name:author.name });
                const inp = picker.querySelector('[data-author-input]');
                if (inp) inp.value = '';
                suggestions.classList.add('hidden'); suggestions.innerHTML = '';
                inp?.focus();
            });
            suggestions.appendChild(btn);
        });

        if (canCreate) {
            const btn = document.createElement('button');
            btn.type = 'button'; btn.className = 'author-suggestion-item';
            btn.innerHTML = `<span class="author-suggestion-name">Thêm mới: ${keyword}</span><span class="author-suggestion-meta">Tạo tác giả</span>`;
            btn.addEventListener('click', () => {
                renderTag(picker, { type:'new', id:null, name:keyword });
                const inp = picker.querySelector('[data-author-input]');
                if (inp) inp.value = '';
                suggestions.classList.add('hidden'); suggestions.innerHTML = '';
                inp?.focus();
            });
            suggestions.appendChild(btn);
        }

        if (authorsArr.length === 0 && !canCreate) {
            const empty = document.createElement('div');
            empty.className = 'author-suggestion-item'; empty.style.cursor = 'default';
            empty.innerHTML = '<span class="author-suggestion-name">Không tìm thấy tác giả</span><span class="author-suggestion-meta">Nhập tên khác</span>';
            suggestions.appendChild(empty);
        }

        suggestions.classList.remove('hidden');
    };

    document.querySelectorAll('[data-author-picker]').forEach(picker => {
        const inp         = picker.querySelector('[data-author-input]');
        const suggestions = picker.querySelector('[data-author-suggestions]');
        const initialItems = JSON.parse(picker.dataset.authorItems || '[]');
        initialItems.forEach(item => renderTag(picker, item));
        if (!inp || !suggestions) return;

        let debounce = null;
        inp.addEventListener('input', () => { clearTimeout(debounce); debounce = setTimeout(() => renderSuggestions(picker, inp.value), 180); });
        inp.addEventListener('focus', () => { if (normalize(inp.value)) renderSuggestions(picker, inp.value); });
        inp.addEventListener('keydown', e => {
            if (e.key !== 'Enter') return;
            e.preventDefault();
            const first = suggestions.querySelector('.author-suggestion-item');
            if (first) { first.click(); return; }
            const kw = normalize(inp.value);
            if (kw) { renderTag(picker, { type:'new', id:null, name:kw }); inp.value = ''; suggestions.classList.add('hidden'); suggestions.innerHTML = ''; }
        });
        document.addEventListener('click', e => { if (!picker.contains(e.target)) suggestions.classList.add('hidden'); });
    });
})();
</script>

@endsection