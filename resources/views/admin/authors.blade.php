@extends('layouts.admin', ['title' => 'Quản lý tác giả'])

@section('content')
    @php
        $openCreateModal = old('_form') === 'create-author';
        $openEditAuthorId = old('_form') === 'update-author' ? (int) old('_author_id') : null;
    @endphp

    <div class="space-y-4">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-center lg:justify-between">
                <div>
                    <h1 class="text-xl font-bold text-slate-900">Quản lý tác giả</h1>
                    <p class="mt-1 text-sm text-slate-500">Thêm mới, chỉnh sửa và quản lý thông tin tác giả.</p>
                </div>

                <div class="flex flex-col gap-2 sm:flex-row sm:items-center sm:justify-end">
                    <form method="GET" class="w-full sm:w-80">
                        <input name="q" value="{{ $q }}" placeholder="Tìm theo tên hoặc tiểu sử..." class="w-full rounded-xl border border-slate-300 px-4 py-2 text-sm" />
                    </form>
                    <button type="button" id="openCreateAuthorModal" class="inline-flex min-w-[160px] items-center justify-center gap-2 rounded-xl bg-orange-600 px-4 py-2.5 text-sm font-semibold text-green shadow-sm transition hover:bg-orange-700 hover:shadow-md whitespace-nowrap">
                        <span class="text-base leading-none">+</span>
                        <span>Thêm tác giả mới</span>
                    </button>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-sm">
            <table class="min-w-full text-left text-sm">
                <thead>
                    <tr class="border-b border-slate-200 text-slate-500">
                        <th class="px-3 py-2">Tác giả</th>
                        <th class="px-3 py-2">Số sách</th>
                        <th class="px-3 py-2">Hành động</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($authors as $author)
                        <tr class="border-b border-slate-100 align-top">
                            <td class="px-3 py-3">
                                @php
                                    $avatarPath = trim((string) $author->avatar_url);
                                    $avatarSrc = null;

                                    if ($avatarPath !== '') {
                                        $avatarSrc = \Illuminate\Support\Str::startsWith($avatarPath, ['http://', 'https://', '/'])
                                            ? $avatarPath
                                            : asset('storage/' . ltrim($avatarPath, '/'));
                                    }
                                @endphp

                                <div class="flex items-start gap-2">
                                    <div class="mt-0.5 h-7 w-7 shrink-0 overflow-hidden rounded-md border border-slate-200 bg-slate-100">
                                        @if ($avatarSrc)
                                            <img src="{{ $avatarSrc }}" alt="{{ $author->name }}" class="h-full w-full object-cover">
                                        @else
                                            <div class="flex h-full w-full items-center justify-center text-[9px] font-semibold text-slate-500">--</div>
                                        @endif
                                    </div>
                                    <div>
                                        <p class="font-semibold text-slate-900">{{ $author->name }}</p>
                                        <p class="text-slate-500 line-clamp-2">{{ $author->bio ?: '---' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-3 py-3 text-slate-700">{{ $author->books_count }}</td>
                            <td class="px-3 py-3 space-y-2">
                                <button
                                    type="button"
                                    data-edit-open="{{ $author->id }}"
                                    class="rounded-lg border border-slate-300 bg-white px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-50"
                                >
                                    Sửa
                                </button>

                                <form method="POST" action="{{ route('admin.authors.destroy', $author) }}" onsubmit="return confirm('Bạn chắc chắn muốn xóa tác giả này?');">
                                    @csrf
                                    @method('DELETE')
                                    <button class="rounded-lg bg-rose-600 px-3 py-1.5 text-xs font-semibold text-white">Xóa</button>
                                </form>

                                <div id="editAuthorModal-{{ $author->id }}" class="fixed inset-0 z-[60] {{ $openEditAuthorId === $author->id ? 'flex' : 'hidden' }} items-center justify-center bg-slate-950/55 px-3">
                                    <div class="w-full max-w-xl rounded-xl border border-slate-200 bg-white p-4 shadow-xl">
                                        <div class="flex items-center justify-between gap-3">
                                            <h2 class="text-lg font-bold text-slate-900">Chỉnh sửa tác giả</h2>
                                            <button type="button" data-edit-close="{{ $author->id }}" class="rounded-md border border-slate-300 px-2.5 py-1 text-xs text-slate-700 hover:bg-slate-100">Đóng</button>
                                        </div>

                                        <form method="POST" action="{{ route('admin.authors.update', $author) }}" enctype="multipart/form-data" class="mt-3 space-y-3">
                                            @csrf
                                            @method('PATCH')
                                            <input type="hidden" name="_form" value="update-author">
                                            <input type="hidden" name="_author_id" value="{{ $author->id }}">
                                            <div class="space-y-1">
                                                <label class="text-xs font-semibold text-slate-600">Tên tác giả</label>
                                                <input name="name" value="{{ $openEditAuthorId === $author->id ? old('name') : $author->name }}" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" required>
                                            </div>
                                            <div class="space-y-1">
                                                <label class="text-xs font-semibold text-slate-600">Ảnh tác giả</label>
                                                <input name="avatar_file" type="file" accept="image/*" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                                                <p class="text-[11px] text-slate-500">Để trống nếu muốn giữ ảnh hiện tại.</p>
                                            </div>
                                            <div class="space-y-1">
                                                <label class="text-xs font-semibold text-slate-600">Tiểu sử</label>
                                                <textarea name="bio" rows="4" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" placeholder="Tiểu sử (tùy chọn)">{{ $openEditAuthorId === $author->id ? old('bio') : $author->bio }}</textarea>
                                            </div>
                                            <div class="flex items-center justify-end gap-2">
                                                <button type="button" data-edit-close="{{ $author->id }}" class="rounded-md border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100">Hủy</button>
                                                <button class="rounded-md bg-slate-900 px-3 py-1.5 text-xs font-semibold text-white hover:bg-slate-800">Lưu thay đổi</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="px-3 py-4 text-center text-slate-500">Không có tác giả phù hợp.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div>
            {{ $authors->links() }}
        </div>
    </div>

    <div id="createAuthorModal" class="fixed inset-0 z-[60] {{ $openCreateModal ? 'flex' : 'hidden' }} items-center justify-center bg-slate-950/55 px-3">
        <div class="w-full max-w-xl rounded-xl border border-slate-200 bg-white p-4 shadow-xl">
            <div class="flex items-center justify-between gap-3">
                <h2 class="text-lg font-bold text-slate-900">Thêm tác giả mới</h2>
                <button type="button" id="closeCreateAuthorModal" class="rounded-md border border-slate-300 px-2.5 py-1 text-xs text-slate-700 hover:bg-slate-100">Đóng</button>
            </div>

            <form method="POST" action="{{ route('admin.authors.store') }}" enctype="multipart/form-data" class="mt-3 space-y-3">
                @csrf
                <input type="hidden" name="_form" value="create-author">
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-slate-600">Tên tác giả</label>
                    <input name="name" value="{{ old('name') }}" placeholder="Tên tác giả" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" required>
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-slate-600">Ảnh tác giả</label>
                    <input name="avatar_file" type="file" accept="image/*" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-slate-600">Tiểu sử</label>
                    <textarea name="bio" rows="4" placeholder="Tiểu sử (tùy chọn)" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">{{ old('bio') }}</textarea>
                </div>
                <div class="flex items-center justify-end gap-2">
                    <button type="button" id="cancelCreateAuthorModal" class="rounded-md border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100">Hủy</button>
                    <button class="rounded-md bg-orange-500 px-3 py-1.5 text-xs font-semibold text-greens hover:bg-orange-600">Thêm tác giả</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        (function () {
            const modal = document.getElementById('createAuthorModal');
            const openBtn = document.getElementById('openCreateAuthorModal');
            const closeBtn = document.getElementById('closeCreateAuthorModal');
            const cancelBtn = document.getElementById('cancelCreateAuthorModal');
            const editOpenButtons = document.querySelectorAll('[data-edit-open]');
            const editCloseButtons = document.querySelectorAll('[data-edit-close]');

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

            editOpenButtons.forEach((button) => {
                button.addEventListener('click', () => {
                    const id = button.getAttribute('data-edit-open');
                    const editModal = document.getElementById(`editAuthorModal-${id}`);
                    if (!editModal) {
                        return;
                    }

                    editModal.classList.remove('hidden');
                    editModal.classList.add('flex');
                });
            });

            editCloseButtons.forEach((button) => {
                button.addEventListener('click', () => {
                    const id = button.getAttribute('data-edit-close');
                    const editModal = document.getElementById(`editAuthorModal-${id}`);
                    if (!editModal) {
                        return;
                    }

                    editModal.classList.remove('flex');
                    editModal.classList.add('hidden');
                });
            });

            document.querySelectorAll('[id^="editAuthorModal-"]').forEach((editModal) => {
                editModal.addEventListener('click', (event) => {
                    if (event.target === editModal) {
                        editModal.classList.remove('flex');
                        editModal.classList.add('hidden');
                    }
                });
            });
        })();
    </script>
@endsection
