@extends('layouts.admin', ['title' => 'Quản lý nhà xuất bản'])

@section('content')
    @php
        $openCreateModal = old('_form') === 'create-publisher';
        $openEditModal = old('_form') === 'edit-publisher';
        $editingPublisher = $openEditModal ? $publishers->firstWhere('id', (int) old('_publisher_id')) : null;
    @endphp

    <div class="grid gap-5 xl:grid-cols-[1.2fr_2fr]">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <h1 class="text-xl font-bold text-slate-900">Quản lý nhà xuất bản</h1>
                    <p class="mt-1 text-sm text-slate-500">Thêm và chỉnh sửa nhà xuất bản trong popup để giao diện gọn hơn.</p>
                </div>
                <button type="button" id="openCreatePublisherModal" class="inline-flex min-w-[170px] items-center justify-center gap-2 whitespace-nowrap rounded-xl bg-orange-600 px-4 py-2 text-sm font-semibold leading-none text-white shadow-sm transition hover:bg-orange-700 hover:shadow-md">
                    <span class="text-base leading-none">+</span>
                    <span>Thêm nhà xuất bản mới</span>
                </button>
            </div>
        </div>

        <div class="space-y-4">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <form method="GET" class="max-w-sm">
                    <input name="q" value="{{ $q }}" placeholder="Tìm tên, website, SĐT..." class="w-full rounded-xl border border-slate-300 px-4 py-2 text-sm" />
                </form>
            </div>

            <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-sm">
                <table class="min-w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 text-slate-500">
                            <th class="px-3 py-2">NXB</th>
                            <th class="px-3 py-2">Liên hệ</th>
                            <th class="px-3 py-2">Sách</th>
                            <th class="px-3 py-2">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($publishers as $publisher)
                            <tr class="border-b border-slate-100 align-top">
                                <td class="px-3 py-3 font-semibold text-slate-900">{{ $publisher->name }}</td>
                                <td class="px-3 py-3 text-slate-700">
                                    <p>{{ $publisher->phone ?: '---' }}</p>
                                    <p class="text-slate-500">{{ $publisher->website ?: '---' }}</p>
                                    <p class="text-slate-400">{{ $publisher->address ?: '---' }}</p>
                                </td>
                                <td class="px-3 py-3 text-slate-700">{{ $publisher->books_count }}</td>
                                <td class="px-3 py-3 space-y-2">
                                    <button
                                        type="button"
                                        class="openEditPublisherModal rounded-lg bg-slate-900 px-3 py-1.5 text-xs font-semibold text-white"
                                        data-id="{{ $publisher->id }}"
                                        data-name="{{ $publisher->name }}"
                                        data-phone="{{ $publisher->phone }}"
                                        data-website="{{ $publisher->website }}"
                                        data-address="{{ $publisher->address }}"
                                        data-update-url="{{ route('admin.publishers.update', $publisher) }}"
                                    >
                                        Sửa NXB
                                    </button>
                                    <form method="POST" action="{{ route('admin.publishers.destroy', $publisher) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="rounded-lg bg-rose-600 px-3 py-1.5 text-xs font-semibold text-white">Xóa</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-3 py-4 text-center text-slate-500">Không có nhà xuất bản.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div>
                {{ $publishers->links() }}
            </div>
        </div>
    </div>

    <div id="createPublisherModal" class="fixed inset-0 z-[60] {{ $openCreateModal ? 'flex' : 'hidden' }} items-center justify-center bg-slate-950/55 px-3">
        <div class="w-full max-w-xl rounded-xl border border-slate-200 bg-white p-4 shadow-xl">
            <div class="flex items-center justify-between gap-3">
                <h2 class="text-lg font-bold text-slate-900">Thêm nhà xuất bản mới</h2>
                <button type="button" id="closeCreatePublisherModal" class="rounded-md border border-slate-300 px-2.5 py-1 text-xs text-slate-700 hover:bg-slate-100">Đóng</button>
            </div>

            <form method="POST" action="{{ route('admin.publishers.store') }}" class="mt-3 space-y-3">
                @csrf
                <input type="hidden" name="_form" value="create-publisher">
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-slate-600">Tên nhà xuất bản</label>
                    <input name="name" value="{{ old('name') }}" placeholder="Tên nhà xuất bản" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" required>
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-slate-600">Số điện thoại</label>
                    <input name="phone" value="{{ old('phone') }}" placeholder="Số điện thoại" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-slate-600">Website</label>
                    <input name="website" value="{{ old('website') }}" placeholder="Website (https://...)" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-slate-600">Địa chỉ</label>
                    <textarea name="address" rows="3" placeholder="Địa chỉ" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">{{ old('address') }}</textarea>
                </div>
                <div class="flex items-center justify-end gap-2">
                    <button type="button" id="cancelCreatePublisherModal" class="rounded-md border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100">Hủy</button>
                    <button class="rounded-md bg-orange-600 px-3 py-1.5 text-xs font-semibold text-white hover:bg-orange-700">Tạo NXB</button>
                </div>
            </form>
        </div>
    </div>

    <div id="editPublisherModal" class="fixed inset-0 z-[60] {{ $openEditModal ? 'flex' : 'hidden' }} items-center justify-center bg-slate-950/55 px-3">
        <div class="w-full max-w-xl rounded-xl border border-slate-200 bg-white p-4 shadow-xl">
            <div class="flex items-center justify-between gap-3">
                <h2 class="text-lg font-bold text-slate-900">Sửa nhà xuất bản</h2>
                <button type="button" id="closeEditPublisherModal" class="rounded-md border border-slate-300 px-2.5 py-1 text-xs text-slate-700 hover:bg-slate-100">Đóng</button>
            </div>

            <form id="editPublisherForm" method="POST" action="{{ $editingPublisher ? route('admin.publishers.update', $editingPublisher) : '#' }}" class="mt-3 space-y-3">
                @csrf
                @method('PATCH')
                <input type="hidden" name="_form" value="edit-publisher">
                <input type="hidden" name="_publisher_id" id="editPublisherId" value="{{ old('_publisher_id') }}">

                <div class="space-y-1">
                    <label class="text-xs font-semibold text-slate-600">Tên nhà xuất bản</label>
                    <input id="editPublisherName" name="name" value="{{ old('name', $editingPublisher?->name) }}" placeholder="Tên nhà xuất bản" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" required>
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-slate-600">Số điện thoại</label>
                    <input id="editPublisherPhone" name="phone" value="{{ old('phone', $editingPublisher?->phone) }}" placeholder="Số điện thoại" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-slate-600">Website</label>
                    <input id="editPublisherWebsite" name="website" value="{{ old('website', $editingPublisher?->website) }}" placeholder="Website (https://...)" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-slate-600">Địa chỉ</label>
                    <textarea id="editPublisherAddress" name="address" rows="3" placeholder="Địa chỉ" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">{{ old('address', $editingPublisher?->address) }}</textarea>
                </div>
                <div class="flex items-center justify-end gap-2">
                    <button type="button" id="cancelEditPublisherModal" class="rounded-md border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100">Hủy</button>
                    <button class="rounded-md bg-slate-900 px-3 py-1.5 text-xs font-semibold text-white hover:bg-slate-800">Cập nhật NXB</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        (function () {
            const createModal = document.getElementById('createPublisherModal');
            const openCreateBtn = document.getElementById('openCreatePublisherModal');
            const closeCreateBtn = document.getElementById('closeCreatePublisherModal');
            const cancelCreateBtn = document.getElementById('cancelCreatePublisherModal');

            const editModal = document.getElementById('editPublisherModal');
            const editForm = document.getElementById('editPublisherForm');
            const closeEditBtn = document.getElementById('closeEditPublisherModal');
            const cancelEditBtn = document.getElementById('cancelEditPublisherModal');

            const editPublisherId = document.getElementById('editPublisherId');
            const editPublisherName = document.getElementById('editPublisherName');
            const editPublisherPhone = document.getElementById('editPublisherPhone');
            const editPublisherWebsite = document.getElementById('editPublisherWebsite');
            const editPublisherAddress = document.getElementById('editPublisherAddress');
            const openEditButtons = document.querySelectorAll('.openEditPublisherModal');

            if (createModal && openCreateBtn && closeCreateBtn && cancelCreateBtn) {
                const openCreateModal = () => {
                    createModal.classList.remove('hidden');
                    createModal.classList.add('flex');
                };

                const closeCreateModal = () => {
                    createModal.classList.remove('flex');
                    createModal.classList.add('hidden');
                };

                openCreateBtn.addEventListener('click', openCreateModal);
                closeCreateBtn.addEventListener('click', closeCreateModal);
                cancelCreateBtn.addEventListener('click', closeCreateModal);
                createModal.addEventListener('click', (event) => {
                    if (event.target === createModal) {
                        closeCreateModal();
                    }
                });
            }

            if (
                editModal && editForm && closeEditBtn && cancelEditBtn &&
                editPublisherId && editPublisherName && editPublisherPhone && editPublisherWebsite && editPublisherAddress
            ) {
                const openEditModal = (publisher) => {
                    editPublisherId.value = publisher.id || '';
                    editPublisherName.value = publisher.name || '';
                    editPublisherPhone.value = publisher.phone || '';
                    editPublisherWebsite.value = publisher.website || '';
                    editPublisherAddress.value = publisher.address || '';
                    editForm.setAttribute('action', publisher.updateUrl || '#');

                    editModal.classList.remove('hidden');
                    editModal.classList.add('flex');
                };

                const closeEditModal = () => {
                    editModal.classList.remove('flex');
                    editModal.classList.add('hidden');
                };

                openEditButtons.forEach((button) => {
                    button.addEventListener('click', () => {
                        openEditModal({
                            id: button.dataset.id,
                            name: button.dataset.name,
                            phone: button.dataset.phone,
                            website: button.dataset.website,
                            address: button.dataset.address,
                            updateUrl: button.dataset.updateUrl,
                        });
                    });
                });

                closeEditBtn.addEventListener('click', closeEditModal);
                cancelEditBtn.addEventListener('click', closeEditModal);
                editModal.addEventListener('click', (event) => {
                    if (event.target === editModal) {
                        closeEditModal();
                    }
                });
            }
        })();
    </script>
@endsection
