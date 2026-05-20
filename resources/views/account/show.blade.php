@extends('layouts.app')

@section('title', 'Thông tin tài khoản')

@section('content')

<style>
/* ─── Tokens ──────────────────────────────────────────── */
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
.ac-wrap {
    max-width: 680px;
    margin: 0 auto;
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.ac-page-gap {
    margin-bottom: 72px;
}

/* ─── Shared card ─────────────────────────────────────── */
.ac-card {
    background: var(--cb-white);
    border: 1px solid var(--cb-border);
    border-radius: 18px;
    overflow: hidden;
}
.ac-card-head {
    padding: 18px 24px;
    border-bottom: 1px solid var(--cb-border);
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 12px;
    flex-wrap: wrap;
}
.ac-card-title {
    font-family: var(--cb-serif);
    font-size: 18px; font-weight: 700;
    color: var(--cb-text);
}
.ac-card-body { padding: 22px 24px; }

/* ─── Profile hero card ───────────────────────────────── */
.ac-hero {
    position: relative;
    overflow: hidden;
}
.ac-hero::before {
    content: '';
    position: absolute; top: 0; left: 0; right: 0;
    height: 3px;
    background: linear-gradient(90deg, var(--cb-accent), #4ade80);
}
.ac-hero-body {
    padding: 28px 28px 24px;
    display: flex;
    gap: 24px;
    align-items: flex-start;
    flex-wrap: wrap;
}

/* Avatar */
.ac-avatar-wrap { position: relative; flex-shrink: 0; }
.ac-avatar {
    width: 100px; height: 100px;
    border-radius: 16px;
    background: linear-gradient(135deg, var(--cb-accent) 0%, #4ade80 100%);
    display: flex; align-items: center; justify-content: center;
    overflow: hidden;
    font-family: var(--cb-serif);
    font-size: 42px; font-weight: 900; color: #fff;
    border: 3px solid var(--cb-white);
    box-shadow: 0 4px 16px rgba(45,106,79,.25);
}
.ac-avatar img { width: 100%; height: 100%; object-fit: cover; }
.ac-avatar-btn {
    display: block; width: 100%;
    margin-top: 10px;
    font-family: var(--cb-sans);
    font-size: 11px; font-weight: 600;
    padding: 7px 12px; border-radius: 8px;
    background: var(--cb-bg); border: 1.5px solid var(--cb-border);
    color: var(--cb-text); cursor: pointer;
    transition: all .18s; text-align: center;
    white-space: nowrap;
}
.ac-avatar-btn:hover { border-color: var(--cb-accent); color: var(--cb-accent); }

/* Hero info */
.ac-hero-info { flex: 1; min-width: 200px; }
.ac-hero-name {
    font-family: var(--cb-serif);
    font-size: 30px; font-weight: 900;
    color: #0d1b10; letter-spacing: -.7px;
    line-height: 1.1; margin-bottom: 16px;
}
.ac-info-row {
    display: flex; align-items: center; justify-content: space-between;
    padding: 10px 0;
    border-bottom: 1px solid var(--cb-border);
    font-family: var(--cb-sans); font-size: 13px;
}
.ac-info-row:last-child { border-bottom: none; padding-bottom: 0; }
.ac-info-lbl { color: var(--cb-muted); font-weight: 500; }
.ac-info-val { color: var(--cb-text); font-weight: 600; }

/* Role badge */
.ac-role-badge {
    display: inline-flex; align-items: center;
    font-size: 11px; font-weight: 700;
    padding: 3px 10px; border-radius: 999px; letter-spacing: .4px;
}
.ac-role-admin { background: #fef3c7; color: #92400e; }
.ac-role-staff { background: #e0eaff; color: #1e3a8a; }
.ac-role-customer { background: var(--cb-accent-light); color: var(--cb-accent); }

/* ─── Form elements ───────────────────────────────────── */
.ac-form-group { margin-bottom: 16px; }
.ac-form-group:last-of-type { margin-bottom: 0; }

.ac-label {
    display: block;
    font-family: var(--cb-sans);
    font-size: 12px; font-weight: 600;
    color: var(--cb-text);
    margin-bottom: 7px;
    letter-spacing: .2px;
}
.ac-input {
    display: block; width: 100%;
    font-family: var(--cb-sans); font-size: 14px;
    padding: 11px 16px;
    border: 1.5px solid var(--cb-border);
    border-radius: 10px;
    background: var(--cb-white); color: var(--cb-text);
    outline: none; transition: border-color .2s, box-shadow .2s;
    box-sizing: border-box;
}
.ac-input:focus {
    border-color: var(--cb-accent);
    box-shadow: 0 0 0 3px rgba(45,106,79,.09);
}
.ac-input::placeholder { color: #c0b8b0; }
.ac-error {
    margin-top: 6px;
    font-family: var(--cb-sans); font-size: 12px; color: #dc2626;
}

/* ─── Buttons ─────────────────────────────────────────── */
.ac-btn-primary {
    font-family: var(--cb-sans);
    font-size: 13px; font-weight: 600;
    padding: 10px 22px; border-radius: 10px;
    border: none; background: var(--cb-text); color: #fff;
    cursor: pointer; transition: background .2s;
    display: inline-flex; align-items: center; gap: 7px;
}
.ac-btn-primary:hover { background: var(--cb-accent); }

.ac-btn-ghost {
    font-family: var(--cb-sans);
    font-size: 13px; font-weight: 500;
    padding: 10px 20px; border-radius: 10px;
    border: 1.5px solid var(--cb-border);
    background: transparent; color: var(--cb-muted);
    cursor: pointer; transition: all .2s;
    display: inline-flex; align-items: center; gap: 7px;
}
.ac-btn-ghost:hover { border-color: var(--cb-text); color: var(--cb-text); }

/* ─── Quick-action row (Email / Password cards) ───────── */
.ac-action-row {
    display: flex; align-items: center; justify-content: space-between;
    gap: 16px; flex-wrap: wrap;
}
.ac-action-sub {
    font-family: var(--cb-sans); font-size: 13px;
    color: var(--cb-muted); margin-top: 4px;
}

/* ─── Utilities ───────────────────────────────────────── */
.hidden { display: none; }

/* ─── Modal ───────────────────────────────────────────── */
.ac-modal-wrap {
    position: fixed; inset: 0; z-index: 60;
    display: flex; align-items: center; justify-content: center;
    padding: 16px;
}
.ac-modal-wrap.hidden { display: none; }
.ac-modal-overlay {
    position: absolute; inset: 0;
    background: rgba(13,27,16,.55);
    backdrop-filter: blur(3px);
}
.ac-modal {
    position: relative; z-index: 1;
    background: var(--cb-white);
    border-radius: 20px;
    width: 100%; max-width: 440px;
    box-shadow: 0 24px 60px rgba(0,0,0,.18);
    overflow: hidden;
}
.ac-modal-head {
    padding: 22px 26px 18px;
    border-bottom: 1px solid var(--cb-border);
    display: flex; align-items: center; justify-content: space-between;
    position: relative;
}
.ac-modal-head::before {
    content: ''; position: absolute;
    top: 0; left: 0; right: 0; height: 3px;
    background: linear-gradient(90deg, var(--cb-accent), #4ade80);
}
.ac-modal-title {
    font-family: var(--cb-serif);
    font-size: 22px; font-weight: 700; color: var(--cb-text);
}
.ac-modal-close {
    width: 32px; height: 32px; border-radius: 8px;
    border: 1.5px solid var(--cb-border); background: transparent;
    display: flex; align-items: center; justify-content: center;
    cursor: pointer; color: var(--cb-muted); transition: all .18s;
}
.ac-modal-close:hover { border-color: var(--cb-text); color: var(--cb-text); }
.ac-modal-body { padding: 22px 26px; }
.ac-modal-foot {
    padding: 0 26px 22px;
    display: grid; grid-template-columns: 1fr 1fr; gap: 10px;
}
</style>

@php
    $avatarPath = trim((string) ($user->avatar_url ?? ''));
    $avatarSrc  = null;
    if ($avatarPath !== '') {
        $avatarSrc = \Illuminate\Support\Str::startsWith($avatarPath, ['http://', 'https://', '/'])
            ? $avatarPath
            : asset('storage/' . ltrim($avatarPath, '/'));
    }
    $initial = mb_strtoupper(mb_substr($user->full_name ?? '', 0, 1));
@endphp

<div class="ac-wrap ac-page-gap">

    {{-- ── 1. Profile hero card ──────────────────────────── --}}
    <div class="ac-card ac-hero">
        <div class="ac-hero-body">

            {{-- Avatar + upload --}}
            <div class="ac-avatar-wrap">
                <div class="ac-avatar">
                    @if($avatarSrc)
                        <img src="{{ $avatarSrc }}" alt="{{ $user->full_name }}">
                    @else
                        {{ $initial }}
                    @endif
                </div>

                {{-- Hidden avatar form (logic giữ nguyên) --}}
                <form id="avatar-form"
                      method="POST"
                      action="{{ route('account.avatar.update') }}"
                      enctype="multipart/form-data">
                    @csrf
                    <input id="avatar_file" name="avatar_file"
                           type="file" accept="image/*" class="hidden">
                </form>

                <button type="button"
                        onclick="document.getElementById('avatar_file').click()"
                        class="ac-avatar-btn">
                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24" style="display:inline;margin-right:4px;vertical-align:middle">
                        <path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/>
                        <polyline points="17 8 12 3 7 8"/>
                        <line x1="12" y1="3" x2="12" y2="15"/>
                    </svg>
                    Đổi ảnh
                </button>
            </div>

            {{-- Info --}}
            <div class="ac-hero-info">
                <h1 class="ac-hero-name">{{ $user->full_name }}</h1>

                <div class="ac-info-row">
                    <span class="ac-info-lbl">Email</span>
                    <span class="ac-info-val">{{ $user->email }}</span>
                </div>
                <div class="ac-info-row">
                    <span class="ac-info-lbl">Số điện thoại</span>
                    <span class="ac-info-val">{{ $user->phone ?? 'Chưa cập nhật' }}</span>
                </div>
                @if($user->role === 'admin' || $user->role === 'staff')
                    <div class="ac-info-row">
                        <span class="ac-info-lbl">Vai trò</span>

                        <span>
                            @if($user->role === 'admin')
                                <span class="ac-role-badge ac-role-admin">
                                    Admin
                                </span>
                            @elseif($user->role === 'staff')
                                <span class="ac-role-badge ac-role-staff">
                                    Staff
                                </span>
                            @endif
                        </span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    {{-- ── 2. Thông tin cá nhân ───────────────────────────── --}}
    <div class="ac-card">
        <div class="ac-card-head">
            <span class="ac-card-title">Thông tin cá nhân</span>
        </div>
        <div class="ac-card-body">
            <form method="POST" action="{{ route('account.profile.update') }}">
                @csrf
                @method('PATCH')

                <div class="ac-form-group">
                    <label for="full_name" class="ac-label">Họ và tên</label>
                    <input id="full_name" name="full_name" type="text"
                           value="{{ $user->full_name }}"
                           class="ac-input" required>
                    @error('full_name')
                        <p class="ac-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="ac-form-group">
                    <label for="phone" class="ac-label">Số điện thoại</label>
                    <input id="phone" name="phone" type="tel"
                           value="{{ $user->phone ?? '' }}"
                           placeholder="Nhập số điện thoại"
                           class="ac-input">
                    @error('phone')
                        <p class="ac-error">{{ $message }}</p>
                    @enderror
                </div>

                <div style="margin-top:20px">
                    <button type="submit" class="ac-btn-primary">
                        <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                            <polyline points="20 6 9 17 4 12"/>
                        </svg>
                        Lưu thay đổi
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- ── 3. Mật khẩu ─────────────────────────────────────── --}}
    <div class="ac-card">
        <div class="ac-card-head">
            <span class="ac-card-title">Bảo mật</span>
            <button type="button"
                    onclick="document.getElementById('password-modal').classList.remove('hidden')"
                    class="ac-btn-primary">
                <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <rect x="3" y="11" width="18" height="11" rx="2" ry="2"/>
                    <path d="M7 11V7a5 5 0 0110 0v4"/>
                </svg>
                Đổi mật khẩu
            </button>
        </div>
        <div class="ac-card-body" style="padding-top:14px;padding-bottom:14px">
            <p class="ac-action-sub">
                Mật khẩu mạnh giúp bảo vệ tài khoản của bạn. Nên dùng ít nhất 8 ký tự gồm chữ hoa, chữ thường và số.
            </p>
        </div>
    </div>

</div>{{-- /.ac-wrap --}}

{{-- ════════════════════════════════════════════════
     Password change modal (logic giữ nguyên)
════════════════════════════════════════════════ --}}
<div id="password-modal" class="ac-modal-wrap hidden">
    <div id="password-overlay" class="ac-modal-overlay"></div>

    <div class="ac-modal">
        <div class="ac-modal-head">
            <span class="ac-modal-title">Đổi mật khẩu</span>
            <button type="button" id="close-password-modal" class="ac-modal-close">
                <svg width="15" height="15" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>

        <form method="POST" action="{{ route('account.password.update') }}">
            @csrf
            @method('PATCH')

            <div class="ac-modal-body">
                <div class="ac-form-group">
                    <label for="modal_current_password" class="ac-label">Mật khẩu hiện tại</label>
                    <input id="modal_current_password"
                           name="current_password"
                           type="password"
                           class="ac-input"
                           placeholder="••••••••"
                           required>
                    @error('current_password')
                        <p class="ac-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="ac-form-group">
                    <label for="modal_new_password" class="ac-label">Mật khẩu mới</label>
                    <input id="modal_new_password"
                           name="new_password"
                           type="password"
                           class="ac-input"
                           placeholder="Tối thiểu 8 ký tự"
                           required>
                    @error('new_password')
                        <p class="ac-error">{{ $message }}</p>
                    @enderror
                </div>

                <div class="ac-form-group" style="margin-bottom:0">
                    <label for="modal_new_password_confirmation" class="ac-label">Xác nhận mật khẩu mới</label>
                    <input id="modal_new_password_confirmation"
                           name="new_password_confirmation"
                           type="password"
                           class="ac-input"
                           placeholder="Nhập lại mật khẩu mới"
                           required>
                    @error('new_password_confirmation')
                        <p class="ac-error">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="ac-modal-foot">
                <button type="button"
                        onclick="document.getElementById('password-modal').classList.add('hidden')"
                        class="ac-btn-ghost" style="justify-content:center">
                    Huỷ
                </button>
                <button type="submit" class="ac-btn-primary" style="justify-content:center">
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                        <polyline points="20 6 9 17 4 12"/>
                    </svg>
                    Xác nhận
                </button>
            </div>
        </form>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {
    /* Avatar auto-submit */
    const avatarInput = document.getElementById('avatar_file');
    const avatarForm  = document.getElementById('avatar-form');
    if (avatarInput && avatarForm) {
        avatarInput.addEventListener('change', function () {
            if (this.files.length > 0) avatarForm.submit();
        });
    }

    /* Password modal */
    const modal   = document.getElementById('password-modal');
    const closeBtn = document.getElementById('close-password-modal');
    const overlay  = document.getElementById('password-overlay');

    const closeModal = () => modal?.classList.add('hidden');
    closeBtn?.addEventListener('click', closeModal);
    overlay?.addEventListener('click', closeModal);

    /* ESC key */
    document.addEventListener('keydown', e => {
        if (e.key === 'Escape') closeModal();
    });
});
</script>

@endsection