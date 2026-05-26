@extends('layouts.admin', ['title' => 'Quản lý nhà xuất bản'])

@section('content')

@php
    $openCreateModal  = old('_form') === 'create-publisher';
    $openEditModal    = old('_form') === 'edit-publisher';
    $editingPublisher = $openEditModal ? $publishers->firstWhere('id', (int) old('_publisher_id')) : null;
@endphp

<style>
/* ─── Design tokens ───────────────────────────────────── */
:root {
    --cb-bg:           var(--cb-brand-bg);
    --cb-border:       var(--cb-brand-border);
    --cb-text:         var(--cb-brand-text);
    --cb-muted:        var(--cb-brand-muted);
    --cb-white:        var(--cb-brand-white);
    --cb-accent:       var(--cb-brand-accent);
    --cb-accent-dark:  var(--cb-brand-accent-dark);
    --cb-accent-light: var(--cb-brand-accent-light);
    --cb-serif:        'Playfair Display', Georgia, serif;
    --cb-sans:         'DM Sans', system-ui, sans-serif;
}
html, body {
    background: var(--cb-bg);
    margin: 0;
}
/* ─── Page header ─────────────────────────────────────── */
.pb-header {
    background: var(--cb-white); border: 1px solid var(--cb-border);
    border-radius: 18px; padding: 20px 26px;
    display: flex; align-items: flex-end; justify-content: space-between;
    gap: 20px; flex-wrap: wrap; margin-bottom: 16px;
    position: relative; overflow: hidden;
    max-width: 1300px;
    margin: 0 auto 16px;
}
.pb-header::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
    background: linear-gradient(90deg, #0ea5e9, var(--cb-accent));
}
.pb-header-title {
    font-family: var(--cb-serif); font-size: 22px; font-weight: 900;
    color: #0d1b10; letter-spacing: -.5px; margin: 0 0 3px;
}
.pb-header-sub { font-family: var(--cb-sans); font-size: 13px; color: var(--cb-muted); }
.pb-header-right { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }

/* Search */
.pb-search-wrap {
    display: flex; border: 1.5px solid var(--cb-border); border-radius: 10px;
    overflow: hidden; background: var(--cb-white); transition: border-color .2s;
}
.pb-search-wrap:focus-within { border-color: var(--cb-accent); }
.pb-search-icon { padding: 0 10px 0 12px; display: flex; align-items: center; color: var(--cb-muted); }
.pb-search-input {
    font-family: var(--cb-sans); font-size: 13px; border: none; outline: none;
    background: transparent; color: var(--cb-text); padding: 9px 14px 9px 0; width: 220px;
}
.pb-search-input::placeholder { color: #c0b8b0; }
.pb-btn-add {
    font-family: var(--cb-sans); font-size: 13px; font-weight: 600;
    padding: 10px 18px; border-radius: 10px; border: none;
    background: var(--cb-text); color: #fff; cursor: pointer;
    display: inline-flex; align-items: center; gap: 7px;
    transition: background .2s; white-space: nowrap;
}
.pb-btn-add:hover { background: var(--cb-accent); }

/* ─── Table card ──────────────────────────────────────── */
.pb-table-card {
    background: var(--cb-white); border: 1px solid var(--cb-border);
    border-radius: 18px; overflow: hidden; margin-bottom: 16px;
    max-width: 1300px;
    margin: 0 auto 16px;
}
.pb-table { width: 100%; border-collapse: collapse; font-family: var(--cb-sans); }
.pb-table thead tr { border-bottom: 1px solid var(--cb-border); }
.pb-table th {
    padding: 11px 18px; font-size: 11px; font-weight: 700;
    letter-spacing: 1.2px; text-transform: uppercase; color: #b0a898; text-align: left;
}
.pb-table tbody tr { border-bottom: 1px solid var(--cb-border); transition: background .15s; }
.pb-table tbody tr:last-child { border-bottom: none; }
.pb-table tbody tr:hover { background: #fdfcfa; }
.pb-table td { padding: 14px 18px; vertical-align: top; }

/* Publisher name */
.pb-name { font-size: 14px; font-weight: 600; color: var(--cb-text); margin-bottom: 4px; }

/* Contact info */
.pb-contact-row {
    display: flex; align-items: center; gap: 6px;
    font-size: 12px; color: var(--cb-muted); margin-bottom: 4px;
}
.pb-contact-row:last-child { margin-bottom: 0; }
.pb-contact-row svg { flex-shrink: 0; }
.pb-contact-link { color: var(--cb-accent); text-decoration: none; }
.pb-contact-link:hover { text-decoration: underline; }

/* Book count badge */
.pb-count-badge {
    display: inline-flex; align-items: center;
    font-size: 12px; font-weight: 600;
    padding: 3px 12px; border-radius: 999px;
    background: #e0f2fe; color: #0284c7;
}

/* Action buttons */
.pb-btn-edit {
    font-family: var(--cb-sans); font-size: 12px; font-weight: 600;
    padding: 7px 13px; border-radius: 8px;
    border: 1.5px solid var(--cb-border); background: var(--cb-white);
    color: var(--cb-text); cursor: pointer; transition: all .18s;
    display: inline-flex; align-items: center; gap: 4px; white-space: nowrap;
}
.pb-btn-edit:hover { border-color: var(--cb-accent); color: var(--cb-accent); }
.pb-btn-del {
    font-family: var(--cb-sans); font-size: 12px; font-weight: 600;
    padding: 7px 13px; border-radius: 8px;
    border: 1.5px solid #fecdd3; background: transparent;
    color: #dc2626; cursor: pointer; transition: background .18s;
    display: inline-flex; align-items: center; gap: 4px; white-space: nowrap;
}
.pb-btn-del:hover { background: #fff1f2; }

/* Empty state */
.pb-empty { padding: 56px 32px; text-align: center; }
.pb-empty h3 {
    font-family: var(--cb-serif); font-size: 20px; font-weight: 700;
    color: var(--cb-text); margin-bottom: 6px;
}
.pb-empty p { font-family: var(--cb-sans); font-size: 13px; color: var(--cb-muted); }

/* ─── Modal shared ────────────────────────────────────── */
.pb-modal-wrap {
    position: fixed; inset: 0; z-index: 60;
    display: none;
    align-items: center; justify-content: center; padding: 16px;
    background: rgba(13,27,16,.52);
    backdrop-filter: blur(3px);
}
.pb-modal-wrap.is-open { display: flex; }

.pb-modal {
    background: var(--cb-white); border-radius: 20px;
    width: 100%; max-width: 520px;
    box-shadow: 0 24px 60px rgba(0,0,0,.16);
    overflow: hidden; position: relative;
}
.pb-modal-head {
    padding: 20px 26px 16px;
    border-bottom: 1px solid var(--cb-border);
    display: flex; align-items: flex-start; justify-content: space-between; gap: 12px;
    position: relative;
}
.pb-modal-head::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
    background: linear-gradient(90deg, #0ea5e9, var(--cb-accent));
}
.pb-modal-title { font-family: var(--cb-serif); font-size: 20px; font-weight: 700; color: var(--cb-text); margin: 0; }
.pb-modal-sub   { font-family: var(--cb-sans); font-size: 13px; color: var(--cb-muted); margin: 4px 0 0; }
.pb-modal-close {
    width: 30px; height: 30px; border-radius: 8px;
    border: 1.5px solid var(--cb-border); background: transparent;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer; color: var(--cb-muted); transition: all .18s; flex-shrink: 0;
}
.pb-modal-close:hover { border-color: var(--cb-text); color: var(--cb-text); }
.pb-modal-body { padding: 20px 26px; display: flex; flex-direction: column; gap: 14px; }
.pb-modal-foot {
    padding: 0 26px 22px;
    display: flex; justify-content: flex-end; gap: 10px;
}

/* Form fields */
.pb-field { display: flex; flex-direction: column; gap: 6px; }
.pb-label {
    font-family: var(--cb-sans); font-size: 12px; font-weight: 600; color: var(--cb-text);
}
.pb-req { color: #dc2626; }
.pb-input {
    font-family: var(--cb-sans); font-size: 13px;
    padding: 10px 14px; border: 1.5px solid var(--cb-border);
    border-radius: 9px; background: var(--cb-white); color: var(--cb-text);
    outline: none; transition: border-color .2s, box-shadow .2s;
    width: 100%; box-sizing: border-box;
}
.pb-input:focus {
    border-color: var(--cb-accent);
    box-shadow: 0 0 0 3px rgba(45,106,79,.09);
}
.pb-input::placeholder { color: #c0b8b0; }

/* Modal buttons */
.pb-modal-submit {
    font-family: var(--cb-sans); font-size: 13px; font-weight: 600;
    padding: 10px 22px; border-radius: 9px; border: none;
    background: var(--cb-text); color: #fff; cursor: pointer;
    transition: background .2s; display: inline-flex; align-items: center; gap: 7px;
}
.pb-modal-submit:hover { background: var(--cb-accent); }
.pb-modal-cancel {
    font-family: var(--cb-sans); font-size: 13px; font-weight: 500;
    padding: 10px 18px; border-radius: 9px;
    border: 1.5px solid var(--cb-border); background: transparent;
    color: var(--cb-muted); cursor: pointer; transition: all .18s;
}
.pb-modal-cancel:hover { border-color: var(--cb-text); color: var(--cb-text); }
</style>

{{-- ── Page header ──────────────────────────────────────── --}}
<div class="pb-header">
    <div>
        <h1 class="pb-header-title">Quản lý nhà xuất bản</h1>
        <p class="pb-header-sub">Thêm mới, chỉnh sửa và xoá nhà xuất bản.</p>
    </div>
    <div class="pb-header-right">
        <form method="GET">
            <div class="pb-search-wrap">
                <span class="pb-search-icon">
                    <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                    </svg>
                </span>
                <input name="q" value="{{ $q }}"
                       placeholder="Tìm tên, website, SĐT..."
                       class="pb-search-input">
            </div>
        </form>
        <button type="button" id="openCreatePublisherModal" class="pb-btn-add">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Thêm nhà xuất bản
        </button>
    </div>
</div>

{{-- ── Publishers table ──────────────────────────────────── --}}
<div class="pb-table-card">
    <div style="overflow-x:auto">
        <table class="pb-table" style="min-width:700px">
            <thead>
                <tr>
                    <th>Nhà xuất bản</th>
                    <th>Thông tin liên hệ</th>
                    <th>Số sách</th>
                    <th style="text-align:right">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                @forelse($publishers as $publisher)
                    <tr>
                        {{-- Name --}}
                        <td>
                            <p class="pb-name">{{ $publisher->name }}</p>
                        </td>

                        {{-- Contact --}}
                        <td>
                            @if($publisher->phone)
                                <div class="pb-contact-row">
                                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 8.81 19.79 19.79 0 01.0 2.18 2 2 0 012 0h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.91 7.91a16 16 0 006.16 6.16l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/>
                                    </svg>
                                    {{ $publisher->phone }}
                                </div>
                            @endif
                            @if($publisher->website)
                                <div class="pb-contact-row">
                                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/>
                                        <path d="M12 2a15.3 15.3 0 014 10 15.3 15.3 0 01-4 10 15.3 15.3 0 01-4-10 15.3 15.3 0 014-10z"/>
                                    </svg>
                                    <a href="{{ $publisher->website }}" target="_blank" rel="noopener" class="pb-contact-link">
                                        {{ $publisher->website }}
                                    </a>
                                </div>
                            @endif
                            @if($publisher->address)
                                <div class="pb-contact-row">
                                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/>
                                        <circle cx="12" cy="9" r="2.5"/>
                                    </svg>
                                    {{ $publisher->address }}
                                </div>
                            @endif
                            @if(!$publisher->phone && !$publisher->website && !$publisher->address)
                                <span style="font-size:12px;color:#c0b8b0">Chưa có thông tin</span>
                            @endif
                        </td>

                        {{-- Book count --}}
                        <td>
                            <span class="pb-count-badge">{{ $publisher->books_count }} cuốn</span>
                        </td>

                        {{-- Actions --}}
                        <td style="text-align:right">
                            <div style="display:flex;align-items:center;justify-content:flex-end;gap:8px">
                                <button type="button"
                                        class="pb-btn-edit openEditPublisherModal"
                                        data-id="{{ $publisher->id }}"
                                        data-name="{{ $publisher->name }}"
                                        data-phone="{{ $publisher->phone }}"
                                        data-website="{{ $publisher->website }}"
                                        data-address="{{ $publisher->address }}"
                                        data-update-url="{{ route('admin.publishers.update', $publisher) }}">
                                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                                        <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                                    </svg>
                                    Sửa
                                </button>

                                <form method="POST"
                                      action="{{ route('admin.publishers.destroy', $publisher) }}"
                                      style="margin:0"
                                      onsubmit="return confirm('Xoá nhà xuất bản này?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="pb-btn-del">
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
                            <div class="pb-empty">
                                <svg width="48" height="48" fill="none" stroke="var(--cb-border)" stroke-width="1.4" viewBox="0 0 24 24" style="margin:0 auto 14px">
                                    <path d="M3 9l9-7 9 7v11a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                                    <polyline points="9 22 9 12 15 12 15 22"/>
                                </svg>
                                <h3>Không có nhà xuất bản</h3>
                                <p>Thử thay đổi từ khoá hoặc thêm nhà xuất bản mới.</p>
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
    {{ $publishers->links() }}
</div>

{{-- ════════════════════════════════════════════════════════
     CREATE MODAL
════════════════════════════════════════════════════════ --}}
<div id="createPublisherModal"
     class="pb-modal-wrap {{ $openCreateModal ? 'is-open' : '' }}">
    <div class="pb-modal">
        <div class="pb-modal-head">
            <div>
                <h2 class="pb-modal-title">Thêm nhà xuất bản</h2>
                <p class="pb-modal-sub">Nhập thông tin và lưu vào hệ thống.</p>
            </div>
            <button type="button" id="closeCreatePublisherModal" class="pb-modal-close">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>

        <form method="POST" action="{{ route('admin.publishers.store') }}">
            @csrf
            <input type="hidden" name="_form" value="create-publisher">

            <div class="pb-modal-body">
                <div class="pb-field">
                    <label class="pb-label">Tên nhà xuất bản <span class="pb-req">*</span></label>
                    <input name="name" type="text" required
                           value="{{ old('name') }}"
                           placeholder="Tên đầy đủ nhà xuất bản"
                           class="pb-input">
                </div>
                <div class="pb-field">
                    <label class="pb-label">Số điện thoại</label>
                    <input name="phone" type="tel"
                           value="{{ old('phone') }}"
                           placeholder="0xxxxxxxxx"
                           class="pb-input">
                </div>
                <div class="pb-field">
                    <label class="pb-label">Website</label>
                    <input name="website" type="url"
                           value="{{ old('website') }}"
                           placeholder="https://..."
                           class="pb-input">
                </div>
                <div class="pb-field">
                    <label class="pb-label">Địa chỉ</label>
                    <textarea name="address" rows="3"
                              placeholder="Địa chỉ văn phòng"
                              class="pb-input">{{ old('address') }}</textarea>
                </div>
            </div>

            <div class="pb-modal-foot">
                <button type="button" id="cancelCreatePublisherModal" class="pb-modal-cancel">Huỷ</button>
                <button type="submit" class="pb-modal-submit">
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                        <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                    </svg>
                    Tạo nhà xuất bản
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ════════════════════════════════════════════════════════
     EDIT MODAL
════════════════════════════════════════════════════════ --}}
<div id="editPublisherModal"
     class="pb-modal-wrap {{ $openEditModal ? 'is-open' : '' }}">
    <div class="pb-modal">
        <div class="pb-modal-head">
            <div>
                <h2 class="pb-modal-title">Chỉnh sửa nhà xuất bản</h2>
                <p class="pb-modal-sub">Cập nhật thông tin nhà xuất bản.</p>
            </div>
            <button type="button" id="closeEditPublisherModal" class="pb-modal-close">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>

        <form id="editPublisherForm"
              method="POST"
              action="{{ $editingPublisher ? route('admin.publishers.update', $editingPublisher) : '#' }}">
            @csrf
            @method('PATCH')
            <input type="hidden" name="_form"         value="edit-publisher">
            <input type="hidden" name="_publisher_id" id="editPublisherId" value="{{ old('_publisher_id') }}">

            <div class="pb-modal-body">
                <div class="pb-field">
                    <label class="pb-label">Tên nhà xuất bản <span class="pb-req">*</span></label>
                    <input id="editPublisherName" name="name" type="text" required
                           value="{{ old('name', $editingPublisher?->name) }}"
                           placeholder="Tên đầy đủ nhà xuất bản"
                           class="pb-input">
                </div>
                <div class="pb-field">
                    <label class="pb-label">Số điện thoại</label>
                    <input id="editPublisherPhone" name="phone" type="tel"
                           value="{{ old('phone', $editingPublisher?->phone) }}"
                           placeholder="0xxxxxxxxx"
                           class="pb-input">
                </div>
                <div class="pb-field">
                    <label class="pb-label">Website</label>
                    <input id="editPublisherWebsite" name="website" type="url"
                           value="{{ old('website', $editingPublisher?->website) }}"
                           placeholder="https://..."
                           class="pb-input">
                </div>
                <div class="pb-field">
                    <label class="pb-label">Địa chỉ</label>
                    <textarea id="editPublisherAddress" name="address" rows="3"
                              placeholder="Địa chỉ văn phòng"
                              class="pb-input">{{ old('address', $editingPublisher?->address) }}</textarea>
                </div>
            </div>

            <div class="pb-modal-foot">
                <button type="button" id="cancelEditPublisherModal" class="pb-modal-cancel">Huỷ</button>
                <button type="submit" class="pb-modal-submit">
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                    Lưu thay đổi
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ── JavaScript (logic giữ nguyên) ──────────────────────── --}}
<script>
(function () {
    const openModal  = m => m?.classList.add('is-open');
    const closeModal = m => m?.classList.remove('is-open');

    /* ── Create modal ── */
    const createModal = document.getElementById('createPublisherModal');
    document.getElementById('openCreatePublisherModal')
        ?.addEventListener('click', () => openModal(createModal));
    document.getElementById('closeCreatePublisherModal')
        ?.addEventListener('click', () => closeModal(createModal));
    document.getElementById('cancelCreatePublisherModal')
        ?.addEventListener('click', () => closeModal(createModal));
    createModal?.addEventListener('click', e => { if (e.target === createModal) closeModal(createModal); });

    /* ── Edit modal ── */
    const editModal   = document.getElementById('editPublisherModal');
    const editForm    = document.getElementById('editPublisherForm');
    const editId      = document.getElementById('editPublisherId');
    const editName    = document.getElementById('editPublisherName');
    const editPhone   = document.getElementById('editPublisherPhone');
    const editWebsite = document.getElementById('editPublisherWebsite');
    const editAddress = document.getElementById('editPublisherAddress');

    document.querySelectorAll('.openEditPublisherModal').forEach(btn => {
        btn.addEventListener('click', () => {
            if (!editForm || !editId || !editName || !editPhone || !editWebsite || !editAddress) return;
            editId.value      = btn.dataset.id      || '';
            editName.value    = btn.dataset.name     || '';
            editPhone.value   = btn.dataset.phone    || '';
            editWebsite.value = btn.dataset.website  || '';
            editAddress.value = btn.dataset.address  || '';
            editForm.setAttribute('action', btn.dataset.updateUrl || '#');
            openModal(editModal);
        });
    });

    document.getElementById('closeEditPublisherModal')
        ?.addEventListener('click', () => closeModal(editModal));
    document.getElementById('cancelEditPublisherModal')
        ?.addEventListener('click', () => closeModal(editModal));
    editModal?.addEventListener('click', e => { if (e.target === editModal) closeModal(editModal); });

    /* ── ESC ── */
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') { closeModal(createModal); closeModal(editModal); }
    });
})();
</script>

@endsection