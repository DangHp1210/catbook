@extends('layouts.admin', ['title' => 'Quản lý người dùng'])

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
.us-header {
    background: var(--cb-white); border: 1px solid var(--cb-border);
    border-radius: 18px; padding: 20px 26px;
    display: flex; align-items: flex-end; justify-content: space-between;
    gap: 20px; flex-wrap: wrap; margin-bottom: 16px;
    position: relative; overflow: hidden;
    max-width: 1300px;
    margin: 0 auto 16px;
}
.us-header::before {
    content: ''; position: absolute; top: 0; left: 0; right: 0; height: 3px;
    background: linear-gradient(90deg, #6366f1, var(--cb-accent));
}
.us-header-title {
    font-family: var(--cb-serif); font-size: 22px; font-weight: 900;
    color: #0d1b10; letter-spacing: -.5px; margin: 0 0 3px;
}
.us-header-sub { font-family: var(--cb-sans); font-size: 13px; color: var(--cb-muted); }

/* Search */
.us-search-wrap {
    display: flex; border: 1.5px solid var(--cb-border); border-radius: 10px;
    overflow: hidden; background: var(--cb-white); transition: border-color .2s;
    min-width: 260px;
}
.us-search-wrap:focus-within { border-color: var(--cb-accent); }
.us-search-icon { padding: 0 10px 0 12px; display: flex; align-items: center; color: var(--cb-muted); }
.us-search-input {
    font-family: var(--cb-sans); font-size: 13px; border: none; outline: none;
    background: transparent; color: var(--cb-text); padding: 10px 14px 10px 0; flex: 1;
}
.us-search-input::placeholder { color: #c0b8b0; }

/* ─── Table card ──────────────────────────────────────── */
.us-table-card {
    background: var(--cb-white); border: 1px solid var(--cb-border);
    border-radius: 18px; overflow: hidden; margin-bottom: 16px;
    max-width: 1300px;
    margin: 0 auto 16px;
}

.us-table { width: 100%; border-collapse: collapse; font-family: var(--cb-sans); }
.us-table thead tr { border-bottom: 1px solid var(--cb-border); }
.us-table th {
    padding: 11px 18px; font-size: 11px; font-weight: 700;
    letter-spacing: 1.2px; text-transform: uppercase; color: #b0a898; text-align: left;
    white-space: nowrap;
}
.us-table tbody tr { border-bottom: 1px solid var(--cb-border); transition: background .15s; }
.us-table tbody tr:last-child { border-bottom: none; }
.us-table tbody tr:hover { background: #fdfcfa; }
.us-table td { padding: 14px 18px; vertical-align: middle; }

/* User info cell */
.us-user-cell { display: flex; align-items: center; gap: 12px; }
.us-name  { font-size: 14px; font-weight: 600; color: var(--cb-text); margin-bottom: 2px; }
.us-email { font-size: 12px; color: var(--cb-muted); margin-bottom: 1px; }
.us-phone { font-size: 11px; color: #b0a898; }

/* Role badge */
.us-role-badge {
    display: inline-flex; align-items: center; gap: 4px;
    font-size: 11px; font-weight: 600; padding: 3px 10px;
    border-radius: 999px; letter-spacing: .3px;
}
.us-role-customer { background: var(--cb-accent-light); color: var(--cb-accent); }
.us-role-staff    { background: #e0eaff; color: #1e3a8a; }
.us-role-admin    { background: #fef3c7; color: #92400e; }

/* Status badge */
.us-status-badge {
    display: inline-flex; align-items: center; gap: 5px;
    font-size: 11px; font-weight: 600; padding: 3px 10px;
    border-radius: 999px; border: 1px solid;
}
.us-status-dot { width: 6px; height: 6px; border-radius: 50%; flex-shrink: 0; }
.us-status-active  { background: var(--cb-accent-light); color: var(--cb-accent); border-color: #86efac; }
.us-status-blocked { background: #fff1f2; color: #dc2626; border-color: #fecdd3; }
.us-status-pending { background: #fffbeb; color: #d97706; border-color: #fde68a; }
.us-status-active .us-status-dot { background: var(--cb-accent); }
.us-status-blocked .us-status-dot { background: #dc2626; }
.us-status-pending .us-status-dot { background: #d97706; }

/* Orders count */
.us-orders-count {
    display: inline-flex; align-items: center;
    font-size: 12px; font-weight: 600; padding: 3px 12px;
    border-radius: 999px; background: var(--cb-bg);
    border: 1px solid var(--cb-border); color: var(--cb-muted);
}

/* ─── Inline edit form ────────────────────────────────── */
.us-edit-form { display: flex; flex-direction: column; gap: 8px; min-width: 160px; }

.us-select {
    font-family: var(--cb-sans); font-size: 12px;
    padding: 7px 10px; border: 1.5px solid var(--cb-border);
    border-radius: 8px; background: var(--cb-white); color: var(--cb-text);
    outline: none; cursor: pointer; width: 100%;
    transition: border-color .18s; appearance: none;
    background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' width='12' height='12' fill='none' viewBox='0 0 24 24'%3E%3Cpolyline points='6 9 12 15 18 9' stroke='%23999' stroke-width='2' stroke-linecap='round' stroke-linejoin='round'/%3E%3C/svg%3E");
    background-repeat: no-repeat; background-position: right 8px center;
    padding-right: 28px;
}
.us-select:focus { border-color: var(--cb-accent); }

.us-save-btn {
    font-family: var(--cb-sans); font-size: 12px; font-weight: 600;
    padding: 8px 14px; border-radius: 8px; border: none;
    background: var(--cb-text); color: #fff; cursor: pointer;
    display: inline-flex; align-items: center; gap: 5px;
    transition: background .2s; width: 100%; justify-content: center;
}
.us-save-btn:hover { background: var(--cb-accent); }

/* ─── Empty state ─────────────────────────────────────── */
.us-empty { padding: 56px 32px; text-align: center; }
.us-empty h3 {
    font-family: var(--cb-serif); font-size: 20px; font-weight: 700;
    color: var(--cb-text); margin-bottom: 6px;
}
.us-empty p { font-family: var(--cb-sans); font-size: 13px; color: var(--cb-muted); }
</style>

{{-- ── Page header ──────────────────────────────────────── --}}
<div class="us-header">
    <div>
        <h1 class="us-header-title">Quản lý người dùng</h1>
        <p class="us-header-sub">Phân quyền và kiểm soát trạng thái tài khoản.</p>
    </div>
    <form method="GET">
        <div class="us-search-wrap">
            <span class="us-search-icon">
                <svg width="14" height="14" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <circle cx="11" cy="11" r="8"/><line x1="21" y1="21" x2="16.65" y2="16.65"/>
                </svg>
            </span>
            <input name="q" value="{{ $q }}"
                   placeholder="Tìm tên, email, SĐT..."
                   class="us-search-input">
        </div>
    </form>
</div>

{{-- ── Users table ───────────────────────────────────────── --}}
<div class="us-table-card">
    <div style="overflow-x:auto">
        <table class="us-table" style="min-width:820px">
            <thead>
                <tr>
                    <th>Người dùng</th>
                    <th>Vai trò</th>
                    <th>Trạng thái</th>
                    <th>Đơn hàng</th>
                    <th>Chỉnh sửa</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                    @php
                        $initial = mb_strtoupper(mb_substr($user->full_name, 0, 1));
                        $roleLabel = match($user->role) {
                            'customer' => 'Khách hàng',
                            'staff'    => 'Nhân viên',
                            'admin'    => 'Quản trị viên',
                            default    => $user->role,
                        };
                        $statusLabel = match($user->status) {
                            'active'  => 'Hoạt động',
                            'blocked' => 'Bị khoá',
                            'pending' => 'Chờ duyệt',
                            default   => $user->status,
                        };
                    @endphp
                    <tr>
                        {{-- User info --}}
                        <td>
                            <div class="us-user-cell">
                                <div>
                                    <p class="us-name">{{ $user->full_name }}</p>
                                    <p class="us-email">{{ $user->email }}</p>
                                    @if($user->phone)
                                        <p class="us-phone">{{ $user->phone }}</p>
                                    @endif
                                </div>
                            </div>
                        </td>

                        {{-- Role --}}
                        <td>
                            <span class="us-role-badge us-role-{{ $user->role }}">
                                {{ $roleLabel }}
                            </span>
                        </td>

                        {{-- Status --}}
                        <td>
                            <span class="us-status-badge us-status-{{ $user->status }}">
                                <span class="us-status-dot"></span>
                                {{ $statusLabel }}
                            </span>
                        </td>

                        {{-- Orders count --}}
                        <td>
                            <span class="us-orders-count">{{ $user->orders_count }} đơn</span>
                        </td>

                        {{-- Inline edit form --}}
                        <td>
                            <form method="POST"
                                  action="{{ route('admin.users.update', $user) }}"
                                  class="us-edit-form">
                                @csrf
                                @method('PATCH')

                                <select name="role" class="us-select">
                                    @foreach(['customer','staff','admin'] as $role)
                                        <option value="{{ $role }}" @selected($user->role === $role)>
                                            {{ match($role) {
                                                'customer' => 'Khách hàng',
                                                'staff'    => 'Nhân viên',
                                                'admin'    => 'Quản trị viên',
                                                default    => $role,
                                            } }}
                                        </option>
                                    @endforeach
                                </select>

                                <select name="status" class="us-select">
                                    @foreach(['active','blocked','pending'] as $status)
                                        <option value="{{ $status }}" @selected($user->status === $status)>
                                            {{ match($status) {
                                                'active'  => 'Hoạt động',
                                                'blocked' => 'Bị khoá',
                                                'pending' => 'Chờ duyệt',
                                                default   => $status,
                                            } }}
                                        </option>
                                    @endforeach
                                </select>

                                <button type="submit" class="us-save-btn">
                                    <svg width="12" height="12" fill="none" stroke="currentColor" stroke-width="2.2" viewBox="0 0 24 24">
                                        <polyline points="20 6 9 17 4 12"/>
                                    </svg>
                                    Lưu thay đổi
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5">
                            <div class="us-empty">
                                <svg width="48" height="48" fill="none" stroke="var(--cb-border)" stroke-width="1.4" viewBox="0 0 24 24" style="margin:0 auto 14px">
                                    <path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/>
                                    <circle cx="12" cy="7" r="4"/>
                                </svg>
                                <h3>Không tìm thấy người dùng</h3>
                                <p>Thử thay đổi từ khoá tìm kiếm.</p>
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
    {{ $users->links() }}
</div>

@endsection