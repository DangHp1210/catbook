@extends('layouts.app')

@section('title','Quản lý địa chỉ')

@section('content')
        <div class="max-w-4xl mx-auto p-6">
            <div class="mb-6 flex items-center justify-between">
                <h1 class="text-2xl font-black">Quản lý địa chỉ</h1>
                <button type="button" id="open-add-address" class="cb-btn-solid">Thêm địa chỉ mới</button>
            </div>

            @if(session('success'))
                <div class="mb-4 rounded p-3 bg-emerald-50 border border-emerald-100 text-emerald-800">{{ session('success') }}</div>
            @endif

            @forelse($addresses as $address)
                <div class="mb-4 rounded-xl border border-slate-200 p-4 bg-white">
                    <div class="flex items-start justify-between">
                        <div>
                            <div class="font-semibold text-slate-900">{{ $address->receiver_name }} @if($address->is_default)<span class="text-xs text-emerald-600">(Mặc định)</span>@endif</div>
                            <div class="text-sm text-slate-600">{{ $address->receiver_phone }}</div>
                            <div class="mt-2 text-sm text-slate-700">{{ $address->address_line }}</div>
                            <div class="text-xs text-slate-500">{{ collect([$address->ward, $address->district, $address->province])->filter()->implode(', ') }}</div>
                        </div>
                        <div class="flex items-center gap-2">
                            @unless($address->is_default)
                                <form method="POST" action="{{ route('account.addresses.set_default', $address) }}">
                                    @csrf
                                    <button class="cb-btn-ghost" type="submit">Đặt mặc định</button>
                                </form>
                            @endunless
                            <a href="{{ route('account.addresses.edit', $address) }}" class="cb-btn-ghost">Sửa</a>
                            <form method="POST" action="{{ route('account.addresses.destroy', $address) }}" onsubmit="return confirm('Xác nhận xóa địa chỉ này?')">
                                @csrf
                                @method('DELETE')
                                <button class="cb-btn-ghost text-rose-600" type="submit">Xóa</button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="rounded p-4 bg-yellow-50 border border-yellow-100 text-yellow-800">Chưa có địa chỉ nào. Hãy thêm địa chỉ để dùng khi thanh toán.</div>
            @endforelse
        </div>

        <!-- Add Address Modal -->
        <div id="add-address-modal" class="hidden fixed inset-0 z-50 items-center justify-center">
            <div id="add-address-backdrop" class="absolute inset-0 bg-black/40"></div>
            <div class="relative w-full max-w-2xl rounded-2xl bg-white p-6 shadow-lg">
                <div class="flex items-start justify-between">
                    <h3 class="text-lg font-bold text-slate-900">Thêm địa chỉ mới</h3>
                    <button type="button" id="add-address-close" class="text-slate-500">✕</button>
                </div>

                <form method="POST" action="{{ route('account.addresses.store') }}" class="mt-4">
                    @csrf
                    <div class="grid gap-3">
                        <input name="receiver_name" placeholder="Tên người nhận" value="{{ old('receiver_name') }}" class="border rounded px-3 py-2" required>
                        <input name="receiver_phone" placeholder="Số điện thoại" value="{{ old('receiver_phone') }}" class="border rounded px-3 py-2" required>
                        <textarea name="address_line" placeholder="Địa chỉ cụ thể" class="border rounded px-3 py-2" required>{{ old('address_line') }}</textarea>
                        <input name="ward" placeholder="Phường/Xã" value="{{ old('ward') }}" class="border rounded px-3 py-2">
                        <input name="district" placeholder="Quận/Huyện" value="{{ old('district') }}" class="border rounded px-3 py-2">
                        <input name="province" placeholder="Tỉnh/Thành phố" value="{{ old('province') }}" class="border rounded px-3 py-2">
                        <input type="hidden" name="set_default" value="1">
                        <div class="flex gap-2 justify-end">
                            <button type="button" id="add-address-cancel" class="cb-btn-ghost">Hủy</button>
                            <button class="cb-btn-solid">Lưu</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                const openBtn = document.getElementById('open-add-address');
                const modal = document.getElementById('add-address-modal');
                const backdrop = document.getElementById('add-address-backdrop');
                const closeBtn = document.getElementById('add-address-close');
                const cancelBtn = document.getElementById('add-address-cancel');

                function openModal() {
                    modal.classList.remove('hidden');
                    modal.classList.add('flex');
                }
                function closeModal() {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                }

                openBtn.addEventListener('click', openModal);
                closeBtn.addEventListener('click', closeModal);
                cancelBtn.addEventListener('click', closeModal);
                backdrop.addEventListener('click', closeModal);
            });
        </script>
@endsection
