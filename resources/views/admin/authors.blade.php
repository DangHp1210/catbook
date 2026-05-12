@extends('layouts.admin', ['title' => 'Quản lý tác giả'])
 
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
.au-header {
    background: var(--cb-white); border: 1px solid var(--cb-border);
    border-radius: 18px; padding: 22px 26px;
    display: flex; align-items: flex-end; justify-content: space-between;
    gap: 20px; flex-wrap: wrap; margin-bottom: 16px;
    position: relative; overflow: hidden;
    max-width: 1300px;
    margin: 0 auto 16px;
}
.au-header::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
    background: linear-gradient(90deg, #8b5cf6, var(--cb-accent));
}
.au-header-title {
    font-family: var(--cb-serif);
    font-size: 24px; font-weight: 900; color: #0d1b10;
    letter-spacing: -.5px; margin: 0 0 4px;
}
.au-header-sub {
    font-family: var(--cb-sans); font-size: 13px; color: var(--cb-muted);
}
 
.au-header-right { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
 
/* Search input */
.au-search-wrap {
    display: flex; border: 1.5px solid var(--cb-border); border-radius: 10px;
    overflow: hidden; background: var(--cb-white); transition: border-color .2s;
}
.au-search-wrap:focus-within { border-color: var(--cb-accent); }
.au-search-icon {
    padding: 0 10px 0 12px; display: flex; align-items: center; color: var(--cb-muted);
}
.au-search-input {
    font-family: var(--cb-sans); font-size: 13px;
    border: none; outline: none; background: transparent;
    color: var(--cb-text); padding: 9px 14px 9px 0;
    width: 240px;
}
.au-search-input::placeholder { color: #c0b8b0; }
 
/* Add button */
.au-btn-add {
    font-family: var(--cb-sans); font-size: 13px; font-weight: 600;
    padding: 10px 20px; border-radius: 10px; border: none;
    background: var(--cb-text); color: #fff; cursor: pointer;
    display: inline-flex; align-items: center; gap: 7px;
    transition: background .2s; white-space: nowrap;
}
.au-btn-add:hover { background: var(--cb-accent); }
 
/* ─── Table card ──────────────────────────────────────── */
.au-table-card {
    background: var(--cb-white); border: 1px solid var(--cb-border);
    border-radius: 18px; overflow: hidden; margin-bottom: 16px;
    max-width: 1300px;
    margin: 0 auto 16px;
}
 
.au-table { width: 100%; border-collapse: collapse; font-family: var(--cb-sans); }
.au-table thead tr {
    border-bottom: 1px solid var(--cb-border);
}
.au-table th {
    padding: 12px 18px; font-size: 11px; font-weight: 700;
    letter-spacing: 1.2px; text-transform: uppercase;
    color: #b0a898; text-align: left; white-space: nowrap;
}
.au-table tbody tr {
    border-bottom: 1px solid var(--cb-border);
    transition: background .15s;
}
.au-table tbody tr:last-child { border-bottom: none; }
.au-table tbody tr:hover { background: #fdfcfa; }
.au-table td { padding: 14px 18px; vertical-align: top; }
 
/* Author cell */
.au-author-cell { display: flex; align-items: flex-start; gap: 12px; }
.au-avatar {
    width: 38px; height: 38px; border-radius: 10px; flex-shrink: 0;
    background: #f0ede6; overflow: hidden;
    display: flex; align-items: center; justify-content: center;
    border: 1px solid var(--cb-border);
    font-family: var(--cb-serif); font-size: 14px; font-weight: 900; color: #c5bdb0;
}
.au-avatar img { width: 100%; height: 100%; object-fit: cover; }
.au-name {
    font-size: 14px; font-weight: 600; color: var(--cb-text); margin-bottom: 3px;
}
.au-bio {
    font-size: 12px; color: var(--cb-muted); line-height: 1.5;
    display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
    line-clamp: 2;
    max-width: 380px;
}
 
/* Book count badge */
.au-count-badge {
    display: inline-flex; align-items: center;
    font-size: 12px; font-weight: 600;
    padding: 3px 12px; border-radius: 999px;
    background: var(--cb-accent-light); color: var(--cb-accent);
}
 
/* Action buttons */
.au-action-group { display: flex; flex-direction: column; gap: 6px; }
.au-btn-edit {
    font-family: var(--cb-sans); font-size: 12px; font-weight: 600;
    padding: 7px 14px; border-radius: 8px;
    border: 1.5px solid var(--cb-border); background: var(--cb-white);
    color: var(--cb-text); cursor: pointer;
    transition: all .18s; white-space: nowrap;
    display: inline-flex; align-items: center; gap: 5px;
}
.au-btn-edit:hover { border-color: var(--cb-accent); color: var(--cb-accent); }
.au-btn-del {
    font-family: var(--cb-sans); font-size: 12px; font-weight: 600;
    padding: 7px 14px; border-radius: 8px;
    border: 1.5px solid #fecdd3; background: transparent;
    color: #dc2626; cursor: pointer;
    transition: background .18s; white-space: nowrap;
    display: inline-flex; align-items: center; gap: 5px;
}
.au-btn-del:hover { background: #fff1f2; }
 
/* Empty state */
.au-empty {
    padding: 56px 32px; text-align: center;
}
.au-empty-icon { color: #c9bfa8; margin-bottom: 14px; }
.au-empty h3 {
    font-family: var(--cb-serif); font-size: 20px; font-weight: 700;
    color: var(--cb-text); margin-bottom: 6px;
}
.au-empty p { font-family: var(--cb-sans); font-size: 13px; color: var(--cb-muted); }
 
/* ─── Modal shared ────────────────────────────────────── */
.au-modal-wrap {
    position: fixed; inset: 0; z-index: 60;
    display: none; /* hidden by default — JS adds .is-open */
    align-items: center; justify-content: center; padding: 16px;
    background: rgba(13,27,16,.55);
    backdrop-filter: blur(3px);
}
.au-modal-wrap.is-open { display: flex; }
.au-modal {
    background: var(--cb-white); border-radius: 20px;
    width: 100%; max-width: 520px;
    box-shadow: 0 24px 60px rgba(0,0,0,.18);
    overflow: hidden; position: relative;
}
.au-modal-head {
    padding: 22px 26px 16px;
    border-bottom: 1px solid var(--cb-border);
    display: flex; align-items: center; justify-content: space-between;
    position: relative;
}
.au-modal-head::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
    background: linear-gradient(90deg, #8b5cf6, var(--cb-accent));
}
.au-modal-title {
    font-family: var(--cb-serif); font-size: 20px; font-weight: 700; color: var(--cb-text);
}
.au-modal-close {
    width: 30px; height: 30px; border-radius: 8px;
    border: 1.5px solid var(--cb-border); background: transparent;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer; color: var(--cb-muted); transition: all .18s;
}
.au-modal-close:hover { border-color: var(--cb-text); color: var(--cb-text); }
.au-modal-body { padding: 20px 26px; display: flex; flex-direction: column; gap: 14px; }
.au-modal-foot {
    padding: 0 26px 22px;
    display: flex; justify-content: flex-end; gap: 10px;
}
 
/* Form fields inside modal */
.au-field { display: flex; flex-direction: column; gap: 6px; }
.au-field-label {
    font-family: var(--cb-sans); font-size: 12px; font-weight: 600; color: var(--cb-text);
}
.au-field-input {
    font-family: var(--cb-sans); font-size: 13px;
    padding: 10px 14px; border: 1.5px solid var(--cb-border);
    border-radius: 9px; background: var(--cb-white); color: var(--cb-text);
    outline: none; transition: border-color .2s, box-shadow .2s;
    width: 100%; box-sizing: border-box;
}
.au-field-input:focus {
    border-color: var(--cb-accent);
    box-shadow: 0 0 0 3px rgba(45,106,79,.09);
}
.au-field-input::placeholder { color: #c0b8b0; }
.au-field-hint {
    font-family: var(--cb-sans); font-size: 11px; color: var(--cb-muted);
}
 
/* Modal buttons */
.au-modal-submit {
    font-family: var(--cb-sans); font-size: 13px; font-weight: 600;
    padding: 10px 22px; border-radius: 9px; border: none;
    background: var(--cb-text); color: #fff; cursor: pointer;
    transition: background .2s; display: inline-flex; align-items: center; gap: 7px;
}
.au-modal-submit:hover { background: var(--cb-accent); }
.au-modal-cancel {
    font-family: var(--cb-sans); font-size: 13px; font-weight: 500;
    padding: 10px 18px; border-radius: 9px;
    border: 1.5px solid var(--cb-border); background: transparent;
    color: var(--cb-muted); cursor: pointer; transition: all .18s;
}
.au-modal-cancel:hover { border-color: var(--cb-text); color: var(--cb-text); }
</style>
 
@php
    $openCreateModal  = old('_form') === 'create-author';
    $openEditAuthorId = old('_form') === 'update-author' ? (int) old('_author_id') : null;
@endphp
 
{{-- ── Page header ──────────────────────────────────────── --}}
<div class="au-header">
    <div>
        <h1 class="au-header-title">Quản lý tác giả</h1>
        <p class="au-header-sub">Thêm mới, chỉnh sửa và quản lý thông tin tác giả.</p>
    </div>
    <div class="au-header-right">
        {{-- Search --}}
        <form method="GET">
            <div class="au-search-wrap">
                <span class="au-search-icon">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                </span>
                <input name="q" value="{{ $q }}"
                       placeholder="Tìm theo tên hoặc tiểu sử..."
                       class="au-search-input">
            </div>
        </form>
 
        {{-- Add button --}}
        <button type="button" id="openCreateAuthorModal" class="au-btn-add">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Thêm tác giả
        </button>
    </div>
</div>
 
{{-- ── Authors table ─────────────────────────────────────── --}}
<div class="au-table-card">
    <div style="overflow-x:auto">
        <table class="au-table">
            <thead>
                <tr>
                    <th>Tác giả</th>
                    <th>Số sách</th>
                    <th>Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($authors as $author)
                    @php
                        $avatarPath = trim((string) $author->avatar_url);
                        $avatarSrc  = null;
                        if ($avatarPath !== '') {
                            $avatarSrc = \Illuminate\Support\Str::startsWith($avatarPath, ['http://', 'https://', '/'])
                                ? $avatarPath
                                : asset('storage/' . ltrim($avatarPath, '/'));
                        }
                        $authorInitial = mb_strtoupper(mb_substr($author->name, 0, 1));
                    @endphp
                    <tr>
                        {{-- Author info --}}
                        <td>
                            <div class="au-author-cell">
                                <div class="au-avatar">
                                    @if($avatarSrc)
                                        <img src="{{ $avatarSrc }}" alt="{{ $author->name }}">
                                    @else
                                        {{ $authorInitial }}
                                    @endif
                                </div>
                                <div>
                                    <p class="au-name">{{ $author->name }}</p>
                                    <p class="au-bio">{{ $author->bio ?: 'Chưa có tiểu sử.' }}</p>
                                </div>
                            </div>
                        </td>
 
                        {{-- Book count --}}
                        <td>
                            <span class="au-count-badge">{{ $author->books_count }} cuốn</span>
                        </td>
 
                        {{-- Actions --}}
                        <td>
                            <div class="au-action-group">
                                <button type="button"
                                        data-edit-open="{{ $author->id }}"
                                        class="au-btn-edit">
                                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                                        <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                    </svg>
                                    Chỉnh sửa
                                </button>
 
                                <form method="POST"
                                      action="{{ route('admin.authors.destroy', $author) }}"
                                      onsubmit="return confirm('Bạn chắc chắn muốn xóa tác giả này?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="au-btn-del">
                                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <polyline points="3 6 5 6 21 6"/>
                                            <path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/>
                                            <path d="M10 11v6M14 11v6M9 6V4h6v2"/>
                                        </svg>
                                        Xoá
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
 
                @empty
                    <tr>
                        <td colspan="3">
                            <div class="au-empty">
                                <div class="au-empty-icon">
                                    <svg width="48" height="48" fill="none" stroke="currentColor" stroke-width="1.4" viewBox="0 0 24 24">
                                        <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/>
                                        <circle cx="12" cy="7" r="4"/>
                                    </svg>
                                </div>
                                <h3>Không có tác giả phù hợp</h3>
                                <p>Thử thay đổi từ khoá tìm kiếm hoặc thêm tác giả mới.</p>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@foreach($authors as $author)
    {{-- Edit modal (per author) --}}
    <div id="editAuthorModal-{{ $author->id }}"
         class="au-modal-wrap {{ $openEditAuthorId === $author->id ? 'is-open' : '' }}">
        <div class="au-modal">
            <div class="au-modal-head">
                <span class="au-modal-title">Chỉnh sửa tác giả</span>
                <button type="button" data-edit-close="{{ $author->id }}" class="au-modal-close">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                    </svg>
                </button>
            </div>

            <form method="POST"
                  action="{{ route('admin.authors.update', $author) }}"
                  enctype="multipart/form-data">
                @csrf
                @method('PATCH')
                <input type="hidden" name="_form"      value="update-author">
                <input type="hidden" name="_author_id" value="{{ $author->id }}">

                <div class="au-modal-body">
                    <div class="au-field">
                        <label class="au-field-label">Tên tác giả</label>
                        <input name="name" type="text" required
                               class="au-field-input"
                               placeholder="Họ tên đầy đủ"
                               value="{{ $openEditAuthorId === $author->id ? old('name') : $author->name }}">
                    </div>
                    <div class="au-field">
                        <label class="au-field-label">Ảnh đại diện</label>
                        <input name="avatar_file" type="file" accept="image/*" class="au-field-input">
                        <span class="au-field-hint">Để trống nếu muốn giữ ảnh hiện tại.</span>
                    </div>
                    <div class="au-field">
                        <label class="au-field-label">Tiểu sử</label>
                        <textarea name="bio" rows="4"
                                  class="au-field-input"
                                  placeholder="Mô tả ngắn về tác giả (tuỳ chọn)">{{ $openEditAuthorId === $author->id ? old('bio') : $author->bio }}</textarea>
                    </div>
                </div>

                <div class="au-modal-foot">
                    <button type="button" data-edit-close="{{ $author->id }}" class="au-modal-cancel">Huỷ</button>
                    <button type="submit" class="au-modal-submit">
                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        Lưu thay đổi
                    </button>
                </div>
            </form>
        </div>
    </div>
@endforeach
 
{{-- Pagination --}}
<div style="display:flex;justify-content:center">
    {{ $authors->links() }}
</div>
 
{{-- ════════════════════════════════════════════════════════
     CREATE MODAL
════════════════════════════════════════════════════════ --}}
<div id="createAuthorModal"
     class="au-modal-wrap {{ $openCreateModal ? 'is-open' : '' }}">
    <div class="au-modal">
        <div class="au-modal-head">
            <span class="au-modal-title">Thêm tác giả mới</span>
            <button type="button" id="closeCreateAuthorModal" class="au-modal-close">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>
 
        <form method="POST" action="{{ route('admin.authors.store') }}" enctype="multipart/form-data">
            @csrf
            <input type="hidden" name="_form" value="create-author">
 
            <div class="au-modal-body">
                <div class="au-field">
                    <label class="au-field-label">Tên tác giả</label>
                    <input name="name" type="text" required
                           value="{{ old('name') }}"
                           placeholder="Họ tên đầy đủ"
                           class="au-field-input">
                </div>
                <div class="au-field">
                    <label class="au-field-label">Ảnh đại diện</label>
                    <input name="avatar_file" type="file" accept="image/*" class="au-field-input">
                </div>
                <div class="au-field">
                    <label class="au-field-label">Tiểu sử</label>
                    <textarea name="bio" rows="4"
                              class="au-field-input"
                              placeholder="Mô tả ngắn về tác giả (tuỳ chọn)">{{ old('bio') }}</textarea>
                </div>
            </div>
 
            <div class="au-modal-foot">
                <button type="button" id="cancelCreateAuthorModal" class="au-modal-cancel">Huỷ</button>
                <button type="submit" class="au-modal-submit">
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                    Thêm tác giả
                </button>
            </div>
        </form>
    </div>
</div>
 
{{-- ── JavaScript (logic giữ nguyên) ──────────────────────── --}}
<script>
(function () {
    /* Create modal */
    const modal     = document.getElementById('createAuthorModal');
    const openBtn   = document.getElementById('openCreateAuthorModal');
    const closeBtn  = document.getElementById('closeCreateAuthorModal');
    const cancelBtn = document.getElementById('cancelCreateAuthorModal');
 
    if (!modal || !openBtn || !closeBtn || !cancelBtn) return;
 
    const openModal  = () => modal.classList.add('is-open');
    const closeModal = () => modal.classList.remove('is-open');
 
    openBtn.addEventListener('click', openModal);
    closeBtn.addEventListener('click', closeModal);
    cancelBtn.addEventListener('click', closeModal);
    modal.addEventListener('click', e => { if (e.target === modal) closeModal(); });
 
    /* Edit modals */
    document.querySelectorAll('[data-edit-open]').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.getAttribute('data-edit-open');
            document.getElementById(`editAuthorModal-${id}`)?.classList.add('is-open');
        });
    });
 
    document.querySelectorAll('[data-edit-close]').forEach(btn => {
        btn.addEventListener('click', () => {
            const id = btn.getAttribute('data-edit-close');
            document.getElementById(`editAuthorModal-${id}`)?.classList.remove('is-open');
        });
    });
 
    document.querySelectorAll('[id^="editAuthorModal-"]').forEach(m => {
        m.addEventListener('click', e => { if (e.target === m) m.classList.remove('is-open'); });
    });
 
    /* ESC key closes any open modal */
    document.addEventListener('keydown', e => {
        if (e.key !== 'Escape') return;
        closeModal();
        document.querySelectorAll('[id^="editAuthorModal-"]').forEach(m => m.classList.remove('is-open'));
    });
})();
</script>
 
@endsection
 