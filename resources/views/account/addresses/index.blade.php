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
                            <button type="button" class="cb-btn-ghost edit-address-btn" data-address-id="{{ $address->id }}" data-receiver-name="{{ $address->receiver_name }}" data-receiver-phone="{{ $address->receiver_phone }}" data-address-line="{{ $address->address_line }}" data-ward="{{ $address->ward }}" data-district="{{ $address->district }}" data-province="{{ $address->province }}">Sửa</button>
                            <form method="POST" action="{{ route('account.addresses.destroy', $address) }}" onsubmit="return confirm('Xác nhận xóa địa chỉ này?')" style="display:inline">
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

        {{-- ════════════════════════════════════════════
             Add Address Modal
        ════════════════════════════════════════════ --}}
        <div id="add-address-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center">
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

        {{-- ════════════════════════════════════════════
             Edit Address Modal
        ════════════════════════════════════════════ --}}
        <div id="edit-address-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center">
            <div id="edit-address-backdrop" class="absolute inset-0 bg-black/40"></div>
            <div class="relative w-full max-w-2xl rounded-2xl bg-white p-6 shadow-lg">
                <div class="flex items-start justify-between">
                    <h3 class="text-lg font-bold text-slate-900">Sửa địa chỉ</h3>
                    <button type="button" id="edit-address-close" class="text-slate-500">✕</button>
                </div>

                <form id="edit-address-form" method="POST" class="mt-4">
                    @csrf
                    @method('PATCH')
                    <div class="grid gap-3">
                        <input id="edit_receiver_name" name="receiver_name" placeholder="Tên người nhận" class="border rounded px-3 py-2" required>
                        <input id="edit_receiver_phone" name="receiver_phone" placeholder="Số điện thoại" class="border rounded px-3 py-2" required>
                        <textarea id="edit_address_line" name="address_line" placeholder="Địa chỉ cụ thể" class="border rounded px-3 py-2" required></textarea>
                        <input id="edit_ward" name="ward" placeholder="Phường/Xã" class="border rounded px-3 py-2">
                        <input id="edit_district" name="district" placeholder="Quận/Huyện" class="border rounded px-3 py-2">
                        <input id="edit_province" name="province" placeholder="Tỉnh/Thành phố" class="border rounded px-3 py-2">
                        <div class="flex gap-2 justify-end">
                            <button type="button" id="edit-address-cancel" class="cb-btn-ghost">Hủy</button>
                            <button type="submit" class="cb-btn-solid">Lưu</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Add Address Modal
                const addOpenBtn = document.getElementById('open-add-address');
                const addModal = document.getElementById('add-address-modal');
                const addBackdrop = document.getElementById('add-address-backdrop');
                const addCloseBtn = document.getElementById('add-address-close');
                const addCancelBtn = document.getElementById('add-address-cancel');

                function openAddModal() {
                    addModal.classList.remove('hidden');
                    addModal.classList.add('flex');
                }
                function closeAddModal() {
                    addModal.classList.add('hidden');
                    addModal.classList.remove('flex');
                }

                addOpenBtn.addEventListener('click', openAddModal);
                addCloseBtn.addEventListener('click', closeAddModal);
                addCancelBtn.addEventListener('click', closeAddModal);
                addBackdrop.addEventListener('click', closeAddModal);

                // Edit Address Modal
                const editModal = document.getElementById('edit-address-modal');
                const editBackdrop = document.getElementById('edit-address-backdrop');
                const editCloseBtn = document.getElementById('edit-address-close');
                const editCancelBtn = document.getElementById('edit-address-cancel');
                const editBtns = document.querySelectorAll('.edit-address-btn');

                function openEditModal(addressId, data) {
                    document.getElementById('edit_receiver_name').value = data.receiverName;
                    document.getElementById('edit_receiver_phone').value = data.receiverPhone;
                    document.getElementById('edit_address_line').value = data.addressLine;
                    document.getElementById('edit_ward').value = data.ward;
                    document.getElementById('edit_district').value = data.district;
                    document.getElementById('edit_province').value = data.province;
                    document.getElementById('edit-address-form').action = `{{ route('account.addresses.index') }}/${addressId}`;
                    
                    editModal.classList.remove('hidden');
                    editModal.classList.add('flex');
                }
                function closeEditModal() {
                    editModal.classList.add('hidden');
                    editModal.classList.remove('flex');
                }

                editBtns.forEach(btn => {
                    btn.addEventListener('click', function () {
                        const addressId = this.dataset.addressId;
                        const data = {
                            receiverName: this.dataset.receiverName,
                            receiverPhone: this.dataset.receiverPhone,
                            addressLine: this.dataset.addressLine,
                            ward: this.dataset.ward,
                            district: this.dataset.district,
                            province: this.dataset.province
                        };
                        openEditModal(addressId, data);
                    });
                });

                editCloseBtn.addEventListener('click', closeEditModal);
                editCancelBtn.addEventListener('click', closeEditModal);
                editBackdrop.addEventListener('click', closeEditModal);
            });
        </script>
@endsection
