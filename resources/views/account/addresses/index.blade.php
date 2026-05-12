@extends('layouts.app')

@section('title', 'Quản lý địa chỉ')

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
/* ─── Page wrapper ────────────────────────────────────── */
.ad-wrap { max-width: 1250px; margin: 0 auto; }

/* ─── Page header ─────────────────────────────────────── */
.ad-header {
    display: flex; align-items: flex-end; justify-content: space-between;
    gap: 16px; flex-wrap: wrap; margin-bottom: 24px;
}
.ad-heading {
    font-family: var(--cb-serif);
    font-size: 30px; font-weight: 900; color: #0d1b10;
    letter-spacing: -.8px; margin: 0 0 4px;
}
.ad-sub {
    font-family: var(--cb-sans); font-size: 13px; color: var(--cb-muted);
}
.ad-btn-add {
    font-family: var(--cb-sans); font-size: 13px; font-weight: 600;
    padding: 10px 20px; border-radius: 10px; border: none;
    background: var(--cb-text); color: #fff; cursor: pointer;
    display: inline-flex; align-items: center; gap: 8px;
    transition: background .2s; white-space: nowrap; flex-shrink: 0;
}
.ad-btn-add:hover { background: var(--cb-accent); }

/* ─── Flash message ───────────────────────────────────── */
.ad-flash {
    display: flex; align-items: flex-start; gap: 10px;
    padding: 13px 18px; border-radius: 12px; border: 1px solid;
    font-family: var(--cb-sans); font-size: 13px; margin-bottom: 20px;
    background: #f0fdf4; border-color: #bbf7d0; color: #166534;
}
.ad-flash svg { flex-shrink: 0; margin-top: 1px; }

/* ─── Empty state ─────────────────────────────────────── */
.ad-empty {
    background: var(--cb-white); border: 2px dashed var(--cb-border);
    border-radius: 18px; padding: 56px 32px; text-align: center;
}
.ad-empty h3 {
    font-family: var(--cb-serif); font-size: 20px; font-weight: 700;
    color: var(--cb-text); margin-bottom: 8px;
}
.ad-empty p { font-family: var(--cb-sans); font-size: 13px; color: var(--cb-muted); margin-bottom: 20px; }
.ad-empty-btn {
    font-family: var(--cb-sans); font-size: 13px; font-weight: 600;
    padding: 10px 24px; border-radius: 10px; border: none;
    background: var(--cb-text); color: #fff; cursor: pointer;
    display: inline-flex; align-items: center; gap: 7px;
    transition: background .2s;
}
.ad-empty-btn:hover { background: var(--cb-accent); }

/* ─── Address card ────────────────────────────────────── */
.ad-list { display: flex; flex-direction: column; gap: 12px; }

.ad-card {
    background: var(--cb-white); border: 1px solid var(--cb-border);
    border-radius: 16px; padding: 20px 22px;
    display: flex; align-items: flex-start; justify-content: space-between;
    gap: 20px; flex-wrap: wrap;
    transition: box-shadow .22s, border-color .22s;
    position: relative; overflow: hidden;
}
.ad-card:hover {
    box-shadow: 0 6px 22px rgba(0,0,0,.07);
    border-color: #d8d2c8;
}

/* Default address accent bar */
.ad-card.is-default::before {
    content: ''; position: absolute; top: 0; left: 0; bottom: 0; width: 3px;
    background: var(--cb-accent); border-radius: 16px 0 0 16px;
}

/* Card info */
.ad-card-info { flex: 1; min-width: 200px; }

.ad-card-name {
    font-family: var(--cb-sans); font-size: 15px; font-weight: 600;
    color: var(--cb-text); display: flex; align-items: center; gap: 8px;
    margin-bottom: 4px;
}
.ad-default-badge {
    display: inline-flex; align-items: center; gap: 4px;
    font-size: 11px; font-weight: 600; padding: 2px 10px; border-radius: 999px;
    background: var(--cb-accent-light); color: var(--cb-accent);
    letter-spacing: .3px;
}
.ad-default-badge svg { flex-shrink: 0; }

.ad-card-phone {
    font-family: var(--cb-sans); font-size: 13px; color: var(--cb-muted); margin-bottom: 10px;
}

.ad-card-addr-wrap {
    display: flex; align-items: flex-start; gap: 8px;
    background: var(--cb-bg); border: 1px solid var(--cb-border);
    border-radius: 10px; padding: 10px 14px;
}
.ad-card-addr-icon { flex-shrink: 0; color: var(--cb-accent); margin-top: 1px; }
.ad-card-addr-line {
    font-family: var(--cb-sans); font-size: 13px; color: var(--cb-text); line-height: 1.55;
}
.ad-card-addr-area {
    font-size: 12px; color: var(--cb-muted); margin-top: 2px;
}

/* Card actions */
.ad-card-actions {
    display: flex; flex-direction: column; align-items: flex-end; gap: 8px;
    flex-shrink: 0;
}

.ad-btn-default {
    font-family: var(--cb-sans); font-size: 12px; font-weight: 500;
    padding: 7px 14px; border-radius: 8px;
    border: 1.5px solid var(--cb-border); background: transparent;
    color: var(--cb-muted); cursor: pointer; white-space: nowrap;
    transition: all .18s;
}
.ad-btn-default:hover { border-color: var(--cb-accent); color: var(--cb-accent); }

.ad-btn-edit {
    font-family: var(--cb-sans); font-size: 12px; font-weight: 600;
    padding: 7px 14px; border-radius: 8px;
    border: 1.5px solid var(--cb-border); background: var(--cb-white);
    color: var(--cb-text); cursor: pointer;
    display: inline-flex; align-items: center; gap: 5px; white-space: nowrap;
    transition: all .18s;
}
.ad-btn-edit:hover { border-color: var(--cb-accent); color: var(--cb-accent); }

.ad-btn-del {
    font-family: var(--cb-sans); font-size: 12px; font-weight: 500;
    padding: 7px 14px; border-radius: 8px;
    border: 1.5px solid #fecdd3; background: transparent;
    color: #dc2626; cursor: pointer;
    display: inline-flex; align-items: center; gap: 5px; white-space: nowrap;
    transition: background .18s;
}
.ad-btn-del:hover { background: #fff1f2; }

/* ─── Modal shared ────────────────────────────────────── */
.ad-modal-wrap {
    position: fixed; inset: 0; z-index: 50;
    display: none;
    align-items: center; justify-content: center; padding: 16px;
    background: rgba(13,27,16,.52);
    backdrop-filter: blur(3px);
}
.ad-modal-wrap.is-open { display: flex; }

.ad-modal {
    background: var(--cb-white); border-radius: 20px;
    width: 100%; max-width: 560px;
    box-shadow: 0 24px 60px rgba(0,0,0,.16);
    overflow: hidden; position: relative;
}
.ad-modal-head {
    padding: 20px 26px 16px;
    border-bottom: 1px solid var(--cb-border);
    display: flex; align-items: flex-start; justify-content: space-between; gap: 12px;
    position: relative;
}
.ad-modal-head::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
    background: linear-gradient(90deg, var(--cb-accent), #4ade80);
}
.ad-modal-title { font-family: var(--cb-serif); font-size: 20px; font-weight: 700; color: var(--cb-text); margin: 0; }
.ad-modal-sub   { font-family: var(--cb-sans); font-size: 13px; color: var(--cb-muted); margin: 4px 0 0; }
.ad-modal-close {
    width: 30px; height: 30px; border-radius: 8px;
    border: 1.5px solid var(--cb-border); background: transparent;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer; color: var(--cb-muted); transition: all .18s; flex-shrink: 0;
}
.ad-modal-close:hover { border-color: var(--cb-text); color: var(--cb-text); }

.ad-modal-body { padding: 20px 26px; display: flex; flex-direction: column; gap: 12px; }
.ad-modal-foot {
    padding: 0 26px 22px;
    display: flex; justify-content: flex-end; gap: 10px;
}

/* ─── Form grid ───────────────────────────────────────── */
.ad-form-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
@media (max-width: 500px) { .ad-form-grid { grid-template-columns: 1fr; } }
.ad-col-2 { grid-column: 1 / -1; }

.ad-field { display: flex; flex-direction: column; gap: 5px; }
.ad-label {
    font-family: var(--cb-sans); font-size: 12px; font-weight: 600; color: var(--cb-text);
}
.ad-req { color: #dc2626; }
.ad-input {
    font-family: var(--cb-sans); font-size: 13px;
    padding: 10px 14px; border: 1.5px solid var(--cb-border);
    border-radius: 9px; background: var(--cb-white); color: var(--cb-text);
    outline: none; transition: border-color .2s, box-shadow .2s;
    width: 100%; box-sizing: border-box;
}
.ad-input:focus {
    border-color: var(--cb-accent);
    box-shadow: 0 0 0 3px rgba(45,106,79,.09);
}
.ad-input::placeholder { color: #c0b8b0; }

.ad-modal-submit {
    font-family: var(--cb-sans); font-size: 13px; font-weight: 600;
    padding: 10px 22px; border-radius: 9px; border: none;
    background: var(--cb-text); color: #fff; cursor: pointer;
    transition: background .2s; display: inline-flex; align-items: center; gap: 7px;
}
.ad-modal-submit:hover { background: var(--cb-accent); }
.ad-modal-cancel {
    font-family: var(--cb-sans); font-size: 13px; font-weight: 500;
    padding: 10px 18px; border-radius: 9px;
    border: 1.5px solid var(--cb-border); background: transparent;
    color: var(--cb-muted); cursor: pointer; transition: all .18s;
}
.ad-modal-cancel:hover { border-color: var(--cb-text); color: var(--cb-text); }
</style>

<div class="ad-wrap">

    {{-- ── Page header ──────────────────────────────────── --}}
    <div class="ad-header">
        <div>
            <h1 class="ad-heading">Địa chỉ giao hàng</h1>
            <p class="ad-sub">Quản lý danh sách địa chỉ nhận hàng của bạn.</p>
        </div>
        <button type="button" id="open-add-address" class="ad-btn-add">
            <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
            </svg>
            Thêm địa chỉ mới
        </button>
    </div>

    {{-- ── Flash ────────────────────────────────────────── --}}
    @if(session('success'))
        <div class="ad-flash">
            <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                <polyline points="20 6 9 17 4 12"/>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    {{-- ── Address list ─────────────────────────────────── --}}
    @forelse($addresses as $address)
        <div class="ad-list">
        <div class="ad-card {{ $address->is_default ? 'is-default' : '' }}">

            {{-- Info --}}
            <div class="ad-card-info">
                <p class="ad-card-name">
                    {{ $address->receiver_name }}
                    @if($address->is_default)
                        <span class="ad-default-badge">
                            <svg width="10" height="10" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><polyline points="20 6 9 17 4 12"/></svg>
                            Mặc định
                        </span>
                    @endif
                </p>
                <p class="ad-card-phone">
                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display:inline;margin-right:4px;vertical-align:middle;color:var(--cb-muted)">
                        <path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 8.81 19.79 19.79 0 01.0 2.18 2 2 0 012 0h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.91 7.91a16 16 0 006.16 6.16l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/>
                    </svg>
                    {{ $address->receiver_phone }}
                </p>
                <div class="ad-card-addr-wrap">
                    <span class="ad-card-addr-icon">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/>
                            <circle cx="12" cy="9" r="2.5"/>
                        </svg>
                    </span>
                    <div>
                        <p class="ad-card-addr-line">{{ $address->address_line }}</p>
                        @php
                            $area = collect([$address->ward, $address->district, $address->province])->filter()->implode(', ');
                        @endphp
                        @if($area)
                            <p class="ad-card-addr-area">{{ $area }}</p>
                        @endif
                    </div>
                </div>
            </div>

            {{-- Actions --}}
            <div class="ad-card-actions">
                @unless($address->is_default)
                    <form method="POST" action="{{ route('account.addresses.set_default', $address) }}" style="margin:0">
                        @csrf
                        <button type="submit" class="ad-btn-default">
                            Đặt mặc định
                        </button>
                    </form>
                @endunless

                <button type="button"
                        class="ad-btn-edit edit-address-btn"
                        data-address-id="{{ $address->id }}"
                        data-receiver-name="{{ $address->receiver_name }}"
                        data-receiver-phone="{{ $address->receiver_phone }}"
                        data-address-line="{{ $address->address_line }}"
                        data-ward="{{ $address->ward }}"
                        data-district="{{ $address->district }}"
                        data-province="{{ $address->province }}">
                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/>
                        <path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/>
                    </svg>
                    Sửa
                </button>

                <form method="POST"
                      action="{{ route('account.addresses.destroy', $address) }}"
                      onsubmit="return confirm('Xác nhận xoá địa chỉ này?')"
                      style="margin:0">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="ad-btn-del">
                        <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <polyline points="3 6 5 6 21 6"/>
                            <path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/>
                        </svg>
                        Xoá
                    </button>
                </form>
            </div>
        </div>
        </div>
    @empty
        <div class="ad-empty">
            <svg width="52" height="52" fill="none" stroke="var(--cb-border)" stroke-width="1.4" viewBox="0 0 24 24" style="margin:0 auto 16px">
                <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7z"/>
                <circle cx="12" cy="9" r="2.5"/>
            </svg>
            <h3>Chưa có địa chỉ nào</h3>
            <p>Thêm địa chỉ để sử dụng khi thanh toán.</p>
            <button type="button" id="open-add-address-empty" class="ad-empty-btn">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24">
                    <line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/>
                </svg>
                Thêm địa chỉ mới
            </button>
        </div>
    @endforelse

</div>{{-- /.ad-wrap --}}

{{-- ══════════════════════════════════════════════════════
     ADD ADDRESS MODAL
══════════════════════════════════════════════════════ --}}
<div id="add-address-modal" class="ad-modal-wrap">
    <div class="ad-modal">
        <div class="ad-modal-head">
            <div>
                <h3 class="ad-modal-title">Thêm địa chỉ mới</h3>
                <p class="ad-modal-sub">Điền đầy đủ để đảm bảo giao hàng chính xác.</p>
            </div>
            <button type="button" id="add-address-close" class="ad-modal-close">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>

        <form method="POST" action="{{ route('account.addresses.store') }}">
            @csrf
            <div class="ad-modal-body">
                <div class="ad-form-grid">
                    <div class="ad-field">
                        <label class="ad-label">Tên người nhận <span class="ad-req">*</span></label>
                        <input name="receiver_name" type="text" required
                               value="{{ old('receiver_name') }}"
                               placeholder="Họ và tên" class="ad-input">
                    </div>
                    <div class="ad-field">
                        <label class="ad-label">Số điện thoại <span class="ad-req">*</span></label>
                        <input name="receiver_phone" type="tel" required
                               value="{{ old('receiver_phone') }}"
                               placeholder="0xxxxxxxxx" class="ad-input">
                    </div>
                    <div class="ad-col-2 ad-field">
                        <label class="ad-label">Địa chỉ cụ thể <span class="ad-req">*</span></label>
                        <textarea name="address_line" required rows="2"
                                  placeholder="Số nhà, tên đường..." class="ad-input">{{ old('address_line') }}</textarea>
                    </div>
                    <div class="ad-field">
                        <label class="ad-label">Phường / Xã</label>
                        <input name="ward" type="text" value="{{ old('ward') }}"
                               placeholder="Phường/Xã" class="ad-input">
                    </div>
                    <div class="ad-field">
                        <label class="ad-label">Quận / Huyện</label>
                        <input name="district" type="text" value="{{ old('district') }}"
                               placeholder="Quận/Huyện" class="ad-input">
                    </div>
                    <div class="ad-col-2 ad-field">
                        <label class="ad-label">Tỉnh / Thành phố</label>
                        <input name="province" type="text" value="{{ old('province') }}"
                               placeholder="Tỉnh/Thành phố" class="ad-input">
                    </div>
                </div>
                <input type="hidden" name="set_default" value="1">
            </div>
            <div class="ad-modal-foot">
                <button type="button" id="add-address-cancel" class="ad-modal-cancel">Huỷ</button>
                <button type="submit" class="ad-modal-submit">
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                    Lưu địa chỉ
                </button>
            </div>
        </form>
    </div>
</div>

{{-- ══════════════════════════════════════════════════════
     EDIT ADDRESS MODAL
══════════════════════════════════════════════════════ --}}
<div id="edit-address-modal" class="ad-modal-wrap">
    <div class="ad-modal">
        <div class="ad-modal-head">
            <div>
                <h3 class="ad-modal-title">Chỉnh sửa địa chỉ</h3>
                <p class="ad-modal-sub">Cập nhật thông tin địa chỉ giao hàng.</p>
            </div>
            <button type="button" id="edit-address-close" class="ad-modal-close">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>

        <form id="edit-address-form" method="POST">
            @csrf
            @method('PATCH')
            <div class="ad-modal-body">
                <div class="ad-form-grid">
                    <div class="ad-field">
                        <label class="ad-label">Tên người nhận <span class="ad-req">*</span></label>
                        <input id="edit_receiver_name" name="receiver_name" type="text" required
                               placeholder="Họ và tên" class="ad-input">
                    </div>
                    <div class="ad-field">
                        <label class="ad-label">Số điện thoại <span class="ad-req">*</span></label>
                        <input id="edit_receiver_phone" name="receiver_phone" type="tel" required
                               placeholder="0xxxxxxxxx" class="ad-input">
                    </div>
                    <div class="ad-col-2 ad-field">
                        <label class="ad-label">Địa chỉ cụ thể <span class="ad-req">*</span></label>
                        <textarea id="edit_address_line" name="address_line" required rows="2"
                                  placeholder="Số nhà, tên đường..." class="ad-input"></textarea>
                    </div>
                    <div class="ad-field">
                        <label class="ad-label">Phường / Xã</label>
                        <input id="edit_ward" name="ward" type="text"
                               placeholder="Phường/Xã" class="ad-input">
                    </div>
                    <div class="ad-field">
                        <label class="ad-label">Quận / Huyện</label>
                        <input id="edit_district" name="district" type="text"
                               placeholder="Quận/Huyện" class="ad-input">
                    </div>
                    <div class="ad-col-2 ad-field">
                        <label class="ad-label">Tỉnh / Thành phố</label>
                        <input id="edit_province" name="province" type="text"
                               placeholder="Tỉnh/Thành phố" class="ad-input">
                    </div>
                </div>
            </div>
            <div class="ad-modal-foot">
                <button type="button" id="edit-address-cancel" class="ad-modal-cancel">Huỷ</button>
                <button type="submit" class="ad-modal-submit">
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
document.addEventListener('DOMContentLoaded', function () {

    /* ── helpers ── */
    const openModal  = m => m?.classList.add('is-open');
    const closeModal = m => m?.classList.remove('is-open');

    /* ── Add modal ── */
    const addModal    = document.getElementById('add-address-modal');
    const addBackdrop = addModal; /* click-outside on wrapper */

    document.getElementById('open-add-address')
        ?.addEventListener('click', () => openModal(addModal));
    document.getElementById('open-add-address-empty')
        ?.addEventListener('click', () => openModal(addModal));
    document.getElementById('add-address-close')
        ?.addEventListener('click', () => closeModal(addModal));
    document.getElementById('add-address-cancel')
        ?.addEventListener('click', () => closeModal(addModal));
    addModal?.addEventListener('click', e => { if (e.target === addModal) closeModal(addModal); });

    /* ── Edit modal ── */
    const editModal = document.getElementById('edit-address-modal');
    const editForm  = document.getElementById('edit-address-form');

    function openEditModal(addressId, data) {
        document.getElementById('edit_receiver_name').value  = data.receiverName;
        document.getElementById('edit_receiver_phone').value = data.receiverPhone;
        document.getElementById('edit_address_line').value   = data.addressLine;
        document.getElementById('edit_ward').value            = data.ward;
        document.getElementById('edit_district').value        = data.district;
        document.getElementById('edit_province').value        = data.province;
        editForm.action = `{{ route('account.addresses.index') }}/${addressId}`;
        openModal(editModal);
    }

    document.querySelectorAll('.edit-address-btn').forEach(btn => {
        btn.addEventListener('click', function () {
            openEditModal(this.dataset.addressId, {
                receiverName:  this.dataset.receiverName,
                receiverPhone: this.dataset.receiverPhone,
                addressLine:   this.dataset.addressLine,
                ward:          this.dataset.ward,
                district:      this.dataset.district,
                province:      this.dataset.province,
            });
        });
    });

    document.getElementById('edit-address-close')
        ?.addEventListener('click', () => closeModal(editModal));
    document.getElementById('edit-address-cancel')
        ?.addEventListener('click', () => closeModal(editModal));
    editModal?.addEventListener('click', e => { if (e.target === editModal) closeModal(editModal); });

    /* ── ESC ── */
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') { closeModal(addModal); closeModal(editModal); }
    });
});
</script>

@endsection