@extends('admin.layout', ['title' => 'Quản lý danh mục'])

@section('content')
    @php
        $openCreateModal = old('_form') === 'create-category';
    @endphp

    <div class="grid gap-5 xl:grid-cols-[1.2fr_2fr]">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="flex items-center justify-between gap-3">
                <div>
                    <h1 class="text-xl font-bold text-slate-900">Quản lý danh mục</h1>
                </div>
                <button type="button" id="openCreateCategoryModal" class="inline-flex min-w-[170px] items-center justify-center gap-2 whitespace-nowrap rounded-xl bg-orange-600 px-4 py-2 text-sm font-semibold leading-none text-blue shadow-sm transition hover:bg-orange-700 hover:shadow-md">
                    <span class="text-base leading-none">+</span>
                    <span>Thêm danh mục mới</span>
                </button>
            </div>
        </div>

        <div class="space-y-4">
            <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                <form method="GET" class="max-w-sm">
                    <input name="q" value="{{ $q }}" placeholder="Tìm danh mục..." class="w-full rounded-xl border border-slate-300 px-4 py-2 text-sm" />
                </form>
            </div>

            <div class="overflow-x-auto rounded-2xl border border-slate-200 bg-white shadow-sm">
                <table class="min-w-full text-left text-sm">
                    <thead>
                        <tr class="border-b border-slate-200 text-slate-500">
                            <th class="px-3 py-2">Danh mục</th>
                            <th class="px-3 py-2">Sách</th>
                            <th class="px-3 py-2">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($categories as $category)
                            <tr class="border-b border-slate-100 align-top">
                                <td class="px-3 py-3">
                                    <p class="font-semibold text-slate-900">{{ $category->name }}</p>
                                    <p class="text-slate-500">{{ $category->parent?->name ?: '---' }}</p>
                                </td>
                                <td class="px-3 py-3 text-slate-700">{{ $category->books_count }}</td>
                                <td class="px-3 py-3 space-y-2">
                                    <form method="POST" action="{{ route('admin.categories.update', $category) }}" class="space-y-2">
                                        @csrf
                                        @method('PATCH')
                                        <input name="name" value="{{ $category->name }}" class="w-full rounded-lg border border-slate-300 px-2 py-1 text-sm" required>
                                        <select name="parent_id" class="w-full rounded-lg border border-slate-300 px-2 py-1 text-sm">
                                            <option value="">Không có cha</option>
                                            @foreach ($parents as $parent)
                                                <option value="{{ $parent->id }}" @selected((int) $category->parent_id === (int) $parent->id)>{{ $parent->name }}</option>
                                            @endforeach
                                        </select>
                                        <button class="rounded-lg bg-slate-900 px-3 py-1.5 text-xs font-semibold text-white">Lưu</button>
                                    </form>
                                    <form method="POST" action="{{ route('admin.categories.destroy', $category) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button class="rounded-lg bg-rose-600 px-3 py-1.5 text-xs font-semibold text-white">Xóa</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-3 py-4 text-center text-slate-500">Không có danh mục.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div>
                {{ $categories->links() }}
            </div>
        </div>
    </div>

    <div id="createCategoryModal" class="fixed inset-0 z-[60] {{ $openCreateModal ? 'flex' : 'hidden' }} items-center justify-center bg-slate-950/55 px-3">
        <div class="w-full max-w-xl rounded-xl border border-slate-200 bg-white p-4 shadow-xl">
            <div class="flex items-center justify-between gap-3">
                <h2 class="text-lg font-bold text-slate-900">Thêm danh mục mới</h2>
                <button type="button" id="closeCreateCategoryModal" class="rounded-md border border-slate-300 px-2.5 py-1 text-xs text-slate-700 hover:bg-slate-100">Đóng</button>
            </div>

            <form method="POST" action="{{ route('admin.categories.store') }}" class="mt-3 space-y-3">
                @csrf
                <input type="hidden" name="_form" value="create-category">
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-slate-600">Tên danh mục</label>
                    <input name="name" value="{{ old('name') }}" placeholder="Tên danh mục" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" required>
                </div>
                <div class="space-y-1">
                    <label class="text-xs font-semibold text-slate-600">Danh mục cha</label>
                    <select name="parent_id" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                        <option value="">Danh mục cha (không bắt buộc)</option>
                        @foreach ($parents as $parent)
                            <option value="{{ $parent->id }}" @selected((string) old('parent_id') === (string) $parent->id)>{{ $parent->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="flex items-center justify-end gap-2">
                    <button type="button" id="cancelCreateCategoryModal" class="rounded-md border border-slate-300 px-3 py-1.5 text-xs font-semibold text-slate-700 hover:bg-slate-100">Hủy</button>
                    <button class="rounded-md bg-orange-600 px-3 py-1.5 text-xs font-semibold text-green hover:bg-orange-700">Tạo danh mục</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        (function () {
            const modal = document.getElementById('createCategoryModal');
            const openBtn = document.getElementById('openCreateCategoryModal');
            const closeBtn = document.getElementById('closeCreateCategoryModal');
            const cancelBtn = document.getElementById('cancelCreateCategoryModal');

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
