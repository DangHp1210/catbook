@extends('admin.layout', ['title' => 'Quản lý danh mục'])

@section('content')
    <div class="grid gap-5 xl:grid-cols-[1.2fr_2fr]">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h1 class="text-xl font-bold text-slate-900">Thêm danh mục mới</h1>
            <form method="POST" action="{{ route('admin.categories.store') }}" class="mt-4 space-y-3">
                @csrf
                <input name="name" placeholder="Tên danh mục" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" required>
                <input name="slug" placeholder="Slug (tùy chọn)" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                <select name="parent_id" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                    <option value="">Danh mục cha (không bắt buộc)</option>
                    @foreach ($parents as $parent)
                        <option value="{{ $parent->id }}">{{ $parent->name }}</option>
                    @endforeach
                </select>
                <button class="rounded-xl bg-orange-500 px-4 py-2 text-sm font-semibold text-white">Tạo danh mục</button>
            </form>
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
                            <th class="px-3 py-2">Slug</th>
                            <th class="px-3 py-2">Sách</th>
                            <th class="px-3 py-2">Hành động</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($categories as $category)
                            <tr class="border-b border-slate-100 align-top">
                                <td class="px-3 py-3">
                                    <p class="font-semibold text-slate-900">{{ $category->name }}</p>
                                    <p class="text-slate-500">Cha: {{ $category->parent?->name ?: '---' }}</p>
                                </td>
                                <td class="px-3 py-3 text-slate-700">{{ $category->slug }}</td>
                                <td class="px-3 py-3 text-slate-700">{{ $category->books_count }}</td>
                                <td class="px-3 py-3 space-y-2">
                                    <form method="POST" action="{{ route('admin.categories.update', $category) }}" class="space-y-2">
                                        @csrf
                                        @method('PATCH')
                                        <input name="name" value="{{ $category->name }}" class="w-full rounded-lg border border-slate-300 px-2 py-1 text-sm" required>
                                        <input name="slug" value="{{ $category->slug }}" class="w-full rounded-lg border border-slate-300 px-2 py-1 text-sm">
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
@endsection
