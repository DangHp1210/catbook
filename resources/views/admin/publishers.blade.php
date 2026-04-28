@extends('admin.layout', ['title' => 'Quản lý nhà xuất bản'])

@section('content')
    @php
        $openCreateModal = old('_form') === 'create-publisher';
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
                                    <form method="POST" action="{{ route('admin.publishers.update', $publisher) }}" class="space-y-2">
                                        @csrf
                                        @method('PATCH')
                                        <input name="name" value="{{ $publisher->name }}" class="w-full rounded-lg border border-slate-300 px-2 py-1 text-sm" required>
                                        <input name="phone" value="{{ $publisher->phone }}" class="w-full rounded-lg border border-slate-300 px-2 py-1 text-sm">
                                        <input name="website" value="{{ $publisher->website }}" class="w-full rounded-lg border border-slate-300 px-2 py-1 text-sm">
                                        <textarea name="address" rows="2" class="w-full rounded-lg border border-slate-300 px-2 py-1 text-sm">{{ $publisher->address }}</textarea>
                                        <button class="rounded-lg bg-slate-900 px-3 py-1.5 text-xs font-semibold text-white">Lưu</button>
                                    </form>
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

    <script>
        (function () {
            const modal = document.getElementById('createPublisherModal');
            const openBtn = document.getElementById('openCreatePublisherModal');
            const closeBtn = document.getElementById('closeCreatePublisherModal');
            const cancelBtn = document.getElementById('cancelCreatePublisherModal');

            if (!modal || !openBtn || !closeBtn || !cancelBtn) {
                return;
            }

            const openModal = () => {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
            };

            const closeModal = () => {
                modal.classList.remove('flex');
                modal.classList.add('hidden');
            };

            openBtn.addEventListener('click', openModal);
            closeBtn.addEventListener('click', closeModal);
            cancelBtn.addEventListener('click', closeModal);
            modal.addEventListener('click', (event) => {
                if (event.target === modal) {
                    closeModal();
                }
            });
        })();
    </script>
@endsection
