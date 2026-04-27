@extends('admin.layout', ['title' => 'Quản lý nhà xuất bản'])

@section('content')
    <div class="grid gap-5 xl:grid-cols-[1.2fr_2fr]">
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <h1 class="text-xl font-bold text-slate-900">Thêm nhà xuất bản</h1>
            <form method="POST" action="{{ route('admin.publishers.store') }}" class="mt-4 space-y-3">
                @csrf
                <input name="name" placeholder="Tên nhà xuất bản" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm" required>
                <input name="phone" placeholder="Số điện thoại" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                <input name="website" placeholder="Website (https://...)" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm">
                <textarea name="address" rows="3" placeholder="Địa chỉ" class="w-full rounded-xl border border-slate-300 px-3 py-2 text-sm"></textarea>
                <button class="rounded-xl bg-orange-500 px-4 py-2 text-sm font-semibold text-white">Tạo NXB</button>
            </form>
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
@endsection
