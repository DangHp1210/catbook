@extends('layouts.admin', ['title' => 'Quản lý người dùng'])

@section('content')
    <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
        <div class="flex flex-wrap items-center justify-between gap-3">
            <h1 class="text-xl font-bold text-slate-900">Quản lý người dùng</h1>
            <form method="GET" class="w-full max-w-sm">
                <input name="q" value="{{ $q }}" placeholder="Tìm tên, email, SĐT..." class="w-full rounded-xl border border-slate-300 px-4 py-2 text-sm" />
            </form>
        </div>
    </div>

    <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-sm">
        <table class="min-w-full text-left text-sm">
            <thead>
                <tr class="border-b border-slate-200 text-slate-500">
                    <th class="px-3 py-2">Người dùng</th>
                    <th class="px-3 py-2">Vai trò</th>
                    <th class="px-3 py-2">Trạng thái</th>
                    <th class="px-3 py-2">Đơn hàng</th>
                    <th class="px-3 py-2">Hành động</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($users as $user)
                    <tr class="border-b border-slate-100 align-top">
                        <td class="px-3 py-3">
                            <p class="font-semibold text-slate-900">{{ $user->full_name }}</p>
                            <p class="text-slate-500">{{ $user->email }}</p>
                            <p class="text-slate-400">{{ $user->phone ?: '---' }}</p>
                        </td>
                        <td class="px-3 py-3 text-slate-700">
                            {{ match($user->role) {
                                'customer' => 'Khách hàng',
                                'staff' => 'Nhân viên',
                                'admin' => 'Quản trị viên',
                                default => $user->role,
                            } }}
                        </td>
                        <td class="px-3 py-3 text-slate-700">
                            {{ match($user->status) {
                                'active' => 'Hoạt động',
                                'blocked' => 'Bị khóa',
                                'pending' => 'Chờ duyệt',
                                default => $user->status,
                            } }}
                        </td>
                        <td class="px-3 py-3 text-slate-700">{{ $user->orders_count }}</td>
                        <td class="px-3 py-3">
                            <form method="POST" action="{{ route('admin.users.update', $user) }}" class="space-y-2">
                                @csrf
                                @method('PATCH')
                                <select name="role" class="w-full rounded-lg border border-slate-300 px-2 py-1 text-sm">
                                    @foreach (['customer', 'staff', 'admin'] as $role)
                                        <option value="{{ $role }}" @selected($user->role === $role)>
                                            {{ match($role) {
                                                'customer' => 'Khách hàng',
                                                'staff' => 'Nhân viên',
                                                'admin' => 'Quản trị viên',
                                                default => $role,
                                            } }}
                                        </option>
                                    @endforeach
                                </select>
                                <select name="status" class="w-full rounded-lg border border-slate-300 px-2 py-1 text-sm">
                                    @foreach (['active', 'blocked', 'pending'] as $status)
                                        <option value="{{ $status }}" @selected($user->status === $status)>
                                            {{ match($status) {
                                                'active' => 'Hoạt động',
                                                'blocked' => 'Bị khóa',
                                                'pending' => 'Chờ duyệt',
                                                default => $status,
                                            } }}
                                        </option>
                                    @endforeach
                                </select>
                                <button class="rounded-lg bg-slate-900 px-3 py-1.5 text-xs font-semibold text-white">Lưu</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-3 py-4 text-center text-slate-500">Không tìm thấy người dùng.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div>
        {{ $users->links() }}
    </div>
@endsection
