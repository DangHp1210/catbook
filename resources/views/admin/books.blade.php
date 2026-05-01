@extends('admin.layout', ['title' => 'Quản lý sách'])

@section('content')
    @php
        $openCreateModal = old('_form') === 'create-book';
        $openEditBookId = old('_form') === 'update-book' ? (int) old('_book_id') : null;
        $authorLookup = $authors->keyBy('id');
        $buildAuthorTags = function (array $authorIds, array $authorNames) use ($authorLookup) {
            $selectedAuthors = collect($authorIds)
                ->filter(fn ($id) => is_numeric($id))
                ->map(function ($id) use ($authorLookup) {
                    $author = $authorLookup->get((int) $id);

                    return $author ? [
                        'type' => 'existing',
                        'value' => (int) $author->id,
                        'name' => $author->name,
                    ] : null;
                })
                ->filter()
                ->values();

            $newAuthors = collect($authorNames)
                ->map(fn ($name) => trim((string) $name))
                ->filter()
                ->unique(fn ($name) => mb_strtolower($name))
                ->map(fn ($name) => [
                    'type' => 'new',
                    'value' => $name,
                    'name' => $name,
                ])
                ->values();

            return $selectedAuthors->merge($newAuthors)->values()->all();
        };
        
        $statusColors = [
            'available' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
            'hidden' => 'bg-slate-50 text-slate-700 border-slate-200',
            'out_of_stock' => 'bg-rose-50 text-rose-700 border-rose-200',
        ];
        $statusLabels = [
            'available' => 'Đang bán',
            'hidden' => 'Đang ẩn',
            'out_of_stock' => 'Hết hàng',
        ];
    @endphp

 <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 rounded-xl border border-slate-200 bg-white p-4 shadow-sm text-left">
            <div>
                <h1 class="text-xl font-bold text-slate-900">Quản lý sách</h1>
                <p class="mt-1 text-sm text-slate-500">Quản lý kho sách, giá bán và thông tin xuất bản.</p>
            </div>
            
            <div class="flex flex-wrap items-center gap-3">
                <form method="GET" class="relative w-full sm:w-72">
                    <svg class="absolute left-3 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
                    </svg>
                    <input name="q" value="{{ $q }}" placeholder="Tìm tên sách hoặc ISBN..." class="w-full rounded-lg border border-slate-300 bg-slate-50 py-2 pl-9 pr-4 text-sm outline-none transition-all focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100" />
                </form>
                
                <button
                    type="button"
                    id="openCreateBookModal"
                    class="flex items-center justify-center whitespace-nowrap rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
                >
                    <svg class="mr-1.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    Thêm sách mới
                </button>
            </div>
        </div>

        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
            <div class="overflow-x-auto">
                <table class="min-w-[980px] w-full text-left text-sm whitespace-nowrap">
                    <thead class="bg-slate-50 text-slate-600">
                        <tr>
                            <th class="px-5 py-4 font-semibold">Thông tin sách</th>
                            <th class="px-5 py-4 font-semibold">Kho & Giá</th>
                            <th class="px-5 py-4 font-semibold">Trạng thái</th>
                            <th class="px-5 py-4 font-semibold text-right">Hành động</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 text-slate-700">
                        @forelse ($books as $book)
                            <tr class="hover:bg-slate-50/50 transition-colors">
                                <td class="px-5 py-4">
                                    <div class="flex items-start gap-3">
                                        <div class="h-12 w-10 shrink-0 rounded-md bg-slate-100 flex items-center justify-center border border-slate-200">
                                            <svg class="h-5 w-5 text-slate-300" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-2a1 1 0 00-1-1H9a1 1 0 00-1 1v2a1 1 0 01-1 1H4a1 1 0 110-2V4zm3 1h2v2H7V5zm2 4H7v2h2V9zm2-4h2v2h-2V5zm2 4h-2v2h2V9z" clip-rule="evenodd" />
                                            </svg>
                                        </div>
                                        <div>
                                            <p class="font-bold text-slate-900 line-clamp-1 whitespace-normal">{{ $book->title }}</p>
                                            <p class="mt-0.5 text-xs text-slate-500">ISBN: <span class="font-medium text-slate-700">{{ $book->isbn ?: 'N/A' }}</span></p>
                                            <div class="mt-1 flex items-center gap-2 text-xs text-slate-500">
                                                <span class="rounded bg-slate-100 px-1.5 py-0.5">{{ $book->publisher?->name ?: 'Chưa cập nhật' }}</span>
                                                <span>• {{ $book->categories_count }} danh mục</span>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-4 align-top">
                                    <div class="space-y-1">
                                        <p class="font-semibold text-slate-900">{{ number_format($book->price, 0, ',', '.') }} đ</p>
                                        @if($book->discount_price)
                                            <p class="text-xs text-rose-500 line-through">{{ number_format($book->discount_price, 0, ',', '.') }} đ</p>
                                        @endif
                                        <p class="text-xs text-slate-500 mt-1">Tồn kho: <span class="font-medium {{ $book->stock_quantity > 0 ? 'text-emerald-600' : 'text-rose-600' }}">{{ $book->stock_quantity }}</span></p>
                                    </div>
                                </td>
                                <td class="px-5 py-4 align-top">
                                    <span class="inline-flex items-center rounded-full border px-2.5 py-0.5 text-xs font-semibold {{ $statusColors[$book->status] ?? 'bg-slate-50 text-slate-700 border-slate-200' }}">
                                        {{ $statusLabels[$book->status] ?? $book->status }}
                                    </span>
                                </td>
                                <td class="px-5 py-4 align-top">
                                    <div class="flex items-center justify-end gap-2">
                                        <button
                                            type="button"
                                            data-edit-open="{{ $book->id }}"
                                            class="inline-flex items-center rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-xs font-medium text-slate-600 shadow-sm transition-colors hover:bg-slate-50 hover:text-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1"
                                            title="Chỉnh sửa"
                                        >
                                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L6.832 19.82a4.5 4.5 0 01-1.897 1.13l-2.685.8.8-2.685a4.5 4.5 0 011.13-1.897L16.863 4.487zm0 0L19.5 7.125" />
                                            </svg>
                                        </button>

                                        <form method="POST" action="{{ route('admin.books.destroy', $book) }}" class="inline-block" onsubmit="return confirm('Bạn chắc chắn muốn xóa sách này? Hành động này không thể hoàn tác.');">
                                            @csrf
                                            @method('DELETE')
                                            <button class="inline-flex items-center rounded-lg border border-rose-200 bg-rose-50 px-2.5 py-1.5 text-xs font-medium text-rose-600 shadow-sm transition-colors hover:bg-rose-100 hover:text-rose-700 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:ring-offset-1" title="Xóa">
                                                <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="M14.74 9l-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 01-2.244 2.077H8.084a2.25 2.25 0 01-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 00-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 013.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 00-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 00-7.5 0" />
                                                </svg>
                                            </button>
                                        </form>
                                    </div>

                                    <div id="editBookModal-{{ $book->id }}" class="fixed inset-0 z-[60] {{ $openEditBookId === $book->id ? 'flex' : 'hidden' }} items-center justify-center bg-slate-900/60 backdrop-blur-sm px-4">
                                        <div class="w-full max-w-3xl rounded-2xl bg-white shadow-2xl overflow-hidden max-h-[90vh] flex flex-col text-left whitespace-normal">
                                            <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4 bg-slate-50/50">
                                                <h2 class="text-lg font-bold text-slate-900">Chỉnh sửa thông tin sách</h2>
                                                <button type="button" data-edit-close="{{ $book->id }}" class="rounded-full p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-colors">
                                                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                                </button>
                                            </div>

                                            <div class="flex-1 overflow-y-auto p-6">
                                                <form id="form-edit-{{ $book->id }}" method="POST" action="{{ route('admin.books.update', $book) }}" enctype="multipart/form-data" class="space-y-5">
                                                    @csrf
                                                    @method('PATCH')
                                                    <input type="hidden" name="_form" value="update-book">
                                                    <input type="hidden" name="_book_id" value="{{ $book->id }}">
                                                    <input type="hidden" name="slug" value="{{ $book->slug }}">
                                                    
                                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                                                        <div class="space-y-1.5">
                                                            <label class="text-sm font-semibold text-slate-700">Tên sách <span class="text-rose-500">*</span></label>
                                                            <input name="title" value="{{ $openEditBookId === $book->id ? old('title') : $book->title }}" placeholder="Nhập tên sách..." class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm outline-none transition-all focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100" required>
                                                        </div>
                                                        <div class="space-y-1.5">
                                                            <label class="text-sm font-semibold text-slate-700">Mã ISBN</label>
                                                            <input name="isbn" value="{{ $openEditBookId === $book->id ? old('isbn') : $book->isbn }}" placeholder="Tùy chọn..." class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm outline-none transition-all focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                                        </div>
                                                        
                                                        <div class="space-y-1.5">
                                                            <label class="text-sm font-semibold text-slate-700">Giá bán (đ) <span class="text-rose-500">*</span></label>
                                                            <input type="number" name="price" min="0" step="1000" value="{{ $openEditBookId === $book->id ? old('price') : (float) $book->price }}" class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm outline-none transition-all focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100" required>
                                                        </div>
                                                        <div class="space-y-1.5">
                                                            <label class="text-sm font-semibold text-slate-700">Giá khuyến mãi (đ)</label>
                                                            <input type="number" name="discount_price" min="0" step="1000" value="{{ $openEditBookId === $book->id ? old('discount_price') : ($book->discount_price !== null ? (float) $book->discount_price : '') }}" placeholder="Bỏ trống nếu không KM" class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm outline-none transition-all focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                                        </div>
                                                        
                                                        <div class="space-y-1.5">
                                                            <label class="text-sm font-semibold text-slate-700">Tồn kho <span class="text-rose-500">*</span></label>
                                                            <input type="number" name="stock_quantity" min="0" value="{{ $openEditBookId === $book->id ? old('stock_quantity') : $book->stock_quantity }}" class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm outline-none transition-all focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100" required>
                                                        </div>
                                                        <div class="space-y-1.5">
                                                            <label class="text-sm font-semibold text-slate-700">Trạng thái <span class="text-rose-500">*</span></label>
                                                            <select name="status" class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm outline-none transition-all focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100" required>
                                                                @foreach (['available' => 'Đang bán', 'hidden' => 'Ẩn', 'out_of_stock' => 'Hết hàng'] as $val => $label)
                                                                    <option value="{{ $val }}" @selected(($openEditBookId === $book->id ? old('status') : $book->status) === $val)>{{ $label }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>

                                                        <div class="space-y-1.5">
                                                            <label class="text-sm font-semibold text-slate-700">Số trang</label>
                                                            <input type="number" name="page_count" min="1" value="{{ $openEditBookId === $book->id ? old('page_count') : $book->page_count }}" class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm outline-none transition-all focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                                        </div>
                                                        <div class="space-y-1.5">
                                                            <label class="text-sm font-semibold text-slate-700">Ngôn ngữ</label>
                                                            <input type="text" name="language" value="{{ $openEditBookId === $book->id ? old('language') : $book->language }}" class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm outline-none transition-all focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                                        </div>

                                                        <div class="space-y-1.5">
                                                            <label class="text-sm font-semibold text-slate-700">Năm xuất bản</label>
                                                            <input type="number" name="publication_year" min="1900" max="{{ now()->year + 1 }}" value="{{ $openEditBookId === $book->id ? old('publication_year') : $book->publication_year }}" class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm outline-none transition-all focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                                        </div>
                                                        <div class="space-y-1.5">
                                                            <label class="text-sm font-semibold text-slate-700">Nhà xuất bản</label>
                                                            <select name="publisher_id" class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm outline-none transition-all focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                                                <option value="">-- Chọn NXB --</option>
                                                                @foreach ($publishers as $publisher)
                                                                    <option value="{{ $publisher->id }}" @selected((string) ($openEditBookId === $book->id ? old('publisher_id') : $book->publisher_id) === (string) $publisher->id)>{{ $publisher->name }}</option>
                                                                @endforeach
                                                            </select>
                                                        </div>

                                                        <div class="sm:col-span-2 space-y-1.5">
                                                            <label class="text-sm font-semibold text-slate-700">Tác giả</label>
                                                            @php
                                                                $selectedAuthors = $openEditBookId === $book->id
                                                                    ? $buildAuthorTags((array) old('author_ids', []), (array) old('author_names', []))
                                                                    : $buildAuthorTags($book->authors->pluck('id')->all(), []);
                                                            @endphp
                                                            <div class="space-y-2" data-author-picker data-search-url="{{ route('admin.books.authors.search') }}">
                                                                <div class="min-h-16 rounded-xl border border-slate-300 bg-white px-3 py-2.5 shadow-sm transition-all focus-within:border-indigo-500 focus-within:ring-2 focus-within:ring-indigo-100">
                                                                    <div class="flex flex-wrap gap-2" data-author-tags>
                                                                        @foreach ($selectedAuthors as $authorItem)
                                                                            <span class="inline-flex items-center gap-2 rounded-full bg-indigo-50 px-3 py-1 text-sm font-medium text-indigo-700" data-author-tag data-author-type="{{ $authorItem['type'] }}" data-author-value="{{ $authorItem['value'] }}" data-author-name="{{ $authorItem['name'] }}">
                                                                                <span>{{ $authorItem['name'] }}</span>
                                                                                <button type="button" class="inline-flex h-5 w-5 items-center justify-center rounded-full text-indigo-500 transition-colors hover:bg-indigo-100 hover:text-indigo-700" data-author-remove aria-label="Xóa tác giả">×</button>
                                                                                <input type="hidden" name="{{ $authorItem['type'] === 'existing' ? 'author_ids[]' : 'author_names[]' }}" value="{{ $authorItem['value'] }}">
                                                                            </span>
                                                                        @endforeach
                                                                        <input type="text" data-author-input class="min-w-48 flex-1 border-0 bg-transparent px-1 py-1 text-sm outline-none placeholder:text-slate-400" placeholder="Gõ để tìm hoặc thêm tác giả..." autocomplete="off">
                                                                    </div>
                                                                </div>
                                                                <div class="relative">
                                                                    <div data-author-suggestions class="absolute left-0 right-0 top-full z-20 mt-2 hidden max-h-64 overflow-auto rounded-xl border border-slate-200 bg-white shadow-xl"></div>
                                                                </div>
                                                            </div>
                                                            <p class="text-xs text-slate-500">Gõ tên để tìm tác giả. Nếu chưa có, nhấn Enter để thêm mới.</p>
                                                        </div>

                                                        <div class="sm:col-span-2 space-y-1.5">
                                                            <label class="text-sm font-semibold text-slate-700">Ảnh bìa</label>
                                                            <input name="cover_image_file" type="file" accept="image/*" class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm file:mr-4 file:rounded-lg file:border-0 file:bg-slate-100 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-slate-700 hover:file:bg-slate-200 outline-none transition-all focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                                        </div>
                                                        
                                                        <div class="sm:col-span-2 space-y-1.5">
                                                            <label class="text-sm font-semibold text-slate-700">Danh mục</label>
                                                            <select name="category_ids[]" multiple size="4" class="w-full rounded-xl border border-slate-300 p-2 text-sm outline-none transition-all focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                                                @foreach ($categories as $category)
                                                                    @php
                                                                        $selectedCategories = $openEditBookId === $book->id
                                                                            ? collect(old('category_ids', []))->map(fn ($id) => (int) $id)
                                                                            : $book->categories->pluck('id');
                                                                    @endphp
                                                                    <option class="rounded-md p-1.5 mb-1 hover:bg-slate-50 checked:bg-indigo-50 checked:text-indigo-700" value="{{ $category->id }}" @selected($selectedCategories->contains((int) $category->id))>{{ $category->name }}</option>
                                                                @endforeach
                                                            </select>
                                                            <p class="text-xs text-slate-500">Giữ phím Ctrl (hoặc Cmd trên Mac) để chọn nhiều danh mục.</p>
                                                        </div>

                                                        <div class="sm:col-span-2 space-y-1.5">
                                                            <label class="text-sm font-semibold text-slate-700">Mô tả sách</label>
                                                            <textarea name="description" rows="3" placeholder="Nhập tóm tắt hoặc mô tả..." class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm outline-none transition-all focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">{{ $openEditBookId === $book->id ? old('description') : $book->description }}</textarea>
                                                        </div>
                                                    </div>
                                                </form>
                                            </div>
                                            
                                            <div class="flex items-center justify-end gap-3 border-t border-slate-100 bg-slate-50 px-6 py-4">
                                                <button type="button" data-edit-close="{{ $book->id }}" class="rounded-xl px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-200 transition-colors">Hủy bỏ</button>
                                                <button type="submit" form="form-edit-{{ $book->id }}" class="rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-indigo-700 transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Lưu thay đổi</button>
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-5 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center text-slate-500">
                                        <svg class="h-12 w-12 text-slate-300 mb-3" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v12m-3-2.818l.879.659c1.171.879 3.07.879 4.242 0 1.172-.879 1.172-2.303 0-3.182C13.536 12.219 12.768 12 12 12c-.725 0-1.45-.22-2.003-.659-1.106-.879-1.106-2.303 0-3.182s2.9-.879 4.006 0l.415.33M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <p class="text-base font-medium text-slate-900">Không tìm thấy sách nào.</p>
                                        <p class="text-sm mt-1">Hãy thử tìm kiếm với từ khóa khác hoặc thêm sách mới.</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if ($books->hasPages())
                <div class="border-t border-slate-200 bg-white px-5 py-3">
                    {{ $books->links() }}
                </div>
            @endif
        </div>
    </div>

    <div id="createBookModal" class="fixed inset-0 z-[60] {{ $openCreateModal ? 'flex' : 'hidden' }} items-center justify-center bg-slate-900/60 backdrop-blur-sm px-4">
        <div class="w-full max-w-3xl rounded-2xl bg-white shadow-2xl overflow-hidden max-h-[90vh] flex flex-col">
            <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4 bg-slate-50/50">
                <h2 class="text-lg font-bold text-slate-900">Thêm sách mới</h2>
                <button type="button" id="closeCreateBookModal" class="rounded-full p-2 text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition-colors">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>

            <div class="flex-1 overflow-y-auto p-6">
                <form id="form-create" method="POST" action="{{ route('admin.books.store') }}" enctype="multipart/form-data" class="space-y-5">
                    @csrf
                    <input type="hidden" name="_form" value="create-book">
                    
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-slate-700">Tên sách <span class="text-rose-500">*</span></label>
                            <input name="title" value="{{ old('title') }}" placeholder="Nhập tên sách..." class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm outline-none transition-all focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100" required>
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-slate-700">Mã ISBN</label>
                            <input name="isbn" value="{{ old('isbn') }}" placeholder="Tùy chọn..." class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm outline-none transition-all focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                        </div>
                        
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-slate-700">Giá bán (đ) <span class="text-rose-500">*</span></label>
                            <input type="number" name="price" min="0" step="1000" value="{{ old('price') }}" placeholder="0" class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm outline-none transition-all focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100" required>
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-slate-700">Giá khuyến mãi (đ)</label>
                            <input type="number" name="discount_price" min="0" step="1000" value="{{ old('discount_price') }}" placeholder="Bỏ trống nếu không KM" class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm outline-none transition-all focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                        </div>
                        
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-slate-700">Tồn kho <span class="text-rose-500">*</span></label>
                            <input type="number" name="stock_quantity" min="0" value="{{ old('stock_quantity', 0) }}" class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm outline-none transition-all focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100" required>
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-slate-700">Trạng thái <span class="text-rose-500">*</span></label>
                            <select name="status" class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm outline-none transition-all focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100" required>
                                @foreach (['available' => 'Đang bán', 'hidden' => 'Ẩn', 'out_of_stock' => 'Hết hàng'] as $val => $label)
                                    <option value="{{ $val }}" @selected(old('status', 'available') === $val)>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-slate-700">Số trang</label>
                            <input type="number" name="page_count" min="1" value="{{ old('page_count') }}" class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm outline-none transition-all focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-slate-700">Ngôn ngữ</label>
                            <input type="text" name="language" value="{{ old('language') }}" class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm outline-none transition-all focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-slate-700">Năm xuất bản</label>
                            <input type="number" name="publication_year" min="1900" max="{{ now()->year + 1 }}" value="{{ old('publication_year') }}" class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm outline-none transition-all focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                        </div>
                        <div class="space-y-1.5">
                            <label class="text-sm font-semibold text-slate-700">Nhà xuất bản</label>
                            <select name="publisher_id" class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm outline-none transition-all focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                <option value="">-- Chọn NXB --</option>
                                @foreach ($publishers as $publisher)
                                    <option value="{{ $publisher->id }}" @selected((string) old('publisher_id') === (string) $publisher->id)>{{ $publisher->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="sm:col-span-2 space-y-1.5">
                            <label class="text-sm font-semibold text-slate-700">Tác giả</label>
                            @php
                                $selectedAuthors = $buildAuthorTags((array) old('author_ids', []), (array) old('author_names', []));
                            @endphp
                            <div class="space-y-2" data-author-picker data-search-url="{{ route('admin.books.authors.search') }}">
                                <div class="min-h-16 rounded-xl border border-slate-300 bg-white px-3 py-2.5 shadow-sm transition-all focus-within:border-indigo-500 focus-within:ring-2 focus-within:ring-indigo-100">
                                    <div class="flex flex-wrap gap-2" data-author-tags>
                                        @foreach ($selectedAuthors as $authorItem)
                                            <span class="inline-flex items-center gap-2 rounded-full bg-indigo-50 px-3 py-1 text-sm font-medium text-indigo-700" data-author-tag data-author-type="{{ $authorItem['type'] }}" data-author-value="{{ $authorItem['value'] }}" data-author-name="{{ $authorItem['name'] }}">
                                                <span>{{ $authorItem['name'] }}</span>
                                                <button type="button" class="inline-flex h-5 w-5 items-center justify-center rounded-full text-indigo-500 transition-colors hover:bg-indigo-100 hover:text-indigo-700" data-author-remove aria-label="Xóa tác giả">×</button>
                                                <input type="hidden" name="{{ $authorItem['type'] === 'existing' ? 'author_ids[]' : 'author_names[]' }}" value="{{ $authorItem['value'] }}">
                                            </span>
                                        @endforeach
                                        <input type="text" data-author-input class="min-w-48 flex-1 border-0 bg-transparent px-1 py-1 text-sm outline-none placeholder:text-slate-400" placeholder="Gõ để tìm hoặc thêm tác giả..." autocomplete="off">
                                    </div>
                                </div>
                                <div class="relative">
                                    <div data-author-suggestions class="absolute left-0 right-0 top-full z-20 mt-2 hidden max-h-64 overflow-auto rounded-xl border border-slate-200 bg-white shadow-xl"></div>
                                </div>
                            </div>
                            <p class="text-xs text-slate-500">Gõ tên để tìm tác giả. Nếu chưa có, nhấn Enter để thêm mới.</p>
                        </div>

                        <div class="sm:col-span-2 space-y-1.5">
                            <label class="text-sm font-semibold text-slate-700">Ảnh bìa</label>
                            <input name="cover_image_file" type="file" accept="image/*" class="w-full rounded-xl border border-slate-300 px-3.5 py-2 text-sm file:mr-4 file:rounded-lg file:border-0 file:bg-slate-100 file:px-4 file:py-2 file:text-sm file:font-semibold file:text-slate-700 hover:file:bg-slate-200 outline-none transition-all focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                        </div>
                        
                        <div class="sm:col-span-2 space-y-1.5">
                            <label class="text-sm font-semibold text-slate-700">Danh mục</label>
                            <select name="category_ids[]" multiple size="4" class="w-full rounded-xl border border-slate-300 p-2 text-sm outline-none transition-all focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">
                                @foreach ($categories as $category)
                                    <option class="rounded-md p-1.5 mb-1 hover:bg-slate-50 checked:bg-indigo-50 checked:text-indigo-700" value="{{ $category->id }}" @selected(collect(old('category_ids', []))->contains($category->id))>{{ $category->name }}</option>
                                @endforeach
                            </select>
                            <p class="text-xs text-slate-500">Giữ phím Ctrl (hoặc Cmd trên Mac) để chọn nhiều danh mục.</p>
                        </div>

                        <div class="sm:col-span-2 space-y-1.5">
                            <label class="text-sm font-semibold text-slate-700">Mô tả sách</label>
                            <textarea name="description" rows="3" placeholder="Nhập tóm tắt hoặc mô tả..." class="w-full rounded-xl border border-slate-300 px-3.5 py-2.5 text-sm outline-none transition-all focus:border-indigo-500 focus:ring-2 focus:ring-indigo-100">{{ old('description') }}</textarea>
                        </div>
                    </div>
                </form>
            </div>
            
            <div class="flex items-center justify-end gap-3 border-t border-slate-100 bg-slate-50 px-6 py-4">
                <button type="button" id="cancelCreateBookModal" class="rounded-xl px-5 py-2.5 text-sm font-semibold text-slate-700 hover:bg-slate-200 transition-colors">Hủy bỏ</button>
                <button type="submit" form="form-create" class="rounded-xl bg-indigo-600 px-5 py-2.5 text-sm font-semibold text-green shadow-sm hover:bg-indigo-700 transition-colors focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2">Thêm sách</button>
            </div>
        </div>
    </div>

    <script>
        (function () {
            const normalizeName = (value) => value.replace(/\s+/g, ' ').trim();

            const initAuthorPicker = (picker) => {
                const tags = picker.querySelector('[data-author-tags]');
                const input = picker.querySelector('[data-author-input]');
                const suggestions = picker.querySelector('[data-author-suggestions]');
                const searchUrl = picker.dataset.searchUrl;

                if (!tags || !input || !suggestions || !searchUrl) return;

                const selectedKey = (type, value) => `${type}:${String(value).toLowerCase()}`;
                const selectedName = (tag) => normalizeName(tag.dataset.authorName || '');
                let debounceTimer = null;
                let abortController = null;
                let blurTimer = null;

                const getSelectedTags = () => Array.from(tags.querySelectorAll('[data-author-tag]'));

                const isDuplicate = (type, value, name) => {
                    const normalizedName = normalizeName(name).toLowerCase();

                    return getSelectedTags().some((tag) => {
                        const tagType = tag.dataset.authorType || '';
                        const tagValue = tag.dataset.authorValue || '';
                        const tagName = selectedName(tag).toLowerCase();

                        return selectedKey(tagType, tagValue) === selectedKey(type, value) || (normalizedName !== '' && tagName === normalizedName);
                    });
                };

                const removeTag = (tag) => {
                    if (!tag) return;
                    tag.remove();
                    input.focus();
                };

                const addTag = ({ type, value, name }) => {
                    const normalizedName = normalizeName(name);
                    if (!normalizedName || isDuplicate(type, value, normalizedName)) return;

                    const tag = document.createElement('span');
                    tag.className = 'inline-flex items-center gap-2 rounded-full bg-indigo-50 px-3 py-1 text-sm font-medium text-indigo-700';
                    tag.dataset.authorTag = 'true';
                    tag.dataset.authorType = type;
                    tag.dataset.authorValue = value;
                    tag.dataset.authorName = normalizedName;

                    const label = document.createElement('span');
                    label.textContent = normalizedName;

                    const removeBtn = document.createElement('button');
                    removeBtn.type = 'button';
                    removeBtn.className = 'inline-flex h-5 w-5 items-center justify-center rounded-full text-indigo-500 transition-colors hover:bg-indigo-100 hover:text-indigo-700';
                    removeBtn.setAttribute('aria-label', 'Xóa tác giả');
                    removeBtn.textContent = '×';

                    const hidden = document.createElement('input');
                    hidden.type = 'hidden';
                    hidden.name = type === 'existing' ? 'author_ids[]' : 'author_names[]';
                    hidden.value = value;

                    tag.append(label, removeBtn, hidden);
                    tags.insertBefore(tag, input);
                    input.value = '';
                    closeSuggestions();
                    input.focus();
                };

                const closeSuggestions = () => {
                    suggestions.classList.add('hidden');
                    suggestions.innerHTML = '';
                };

                const renderSuggestions = (items, query, canCreate) => {
                    suggestions.innerHTML = '';

                    if (items.length === 0 && ! canCreate) {
                        const empty = document.createElement('div');
                        empty.className = 'px-4 py-3 text-sm text-slate-500';
                        empty.textContent = 'Không tìm thấy tác giả phù hợp.';
                        suggestions.appendChild(empty);
                        suggestions.classList.remove('hidden');
                        return;
                    }

                    items.forEach((item) => {
                        const button = document.createElement('button');
                        button.type = 'button';
                        button.className = 'flex w-full items-center justify-between gap-3 px-4 py-3 text-left text-sm transition-colors hover:bg-slate-50';
                        button.dataset.authorAction = 'select';
                        button.dataset.authorId = item.id;
                        button.dataset.authorName = item.name;
                        button.innerHTML = `<span class="font-medium text-slate-800"></span><span class="text-xs text-slate-400">Chọn</span>`;
                        button.querySelector('span').textContent = item.name;
                        suggestions.appendChild(button);
                    });

                    if (canCreate) {
                        const createBtn = document.createElement('button');
                        createBtn.type = 'button';
                        createBtn.className = 'flex w-full items-center justify-between gap-3 border-t border-slate-100 px-4 py-3 text-left text-sm transition-colors hover:bg-emerald-50';
                        createBtn.dataset.authorAction = 'create';
                        createBtn.dataset.authorName = query;
                        createBtn.innerHTML = `<span class="font-medium text-emerald-700"></span><span class="text-xs text-emerald-600">Tạo mới</span>`;
                        createBtn.querySelector('span').textContent = `Thêm "${query}"`;
                        suggestions.appendChild(createBtn);
                    }

                    suggestions.classList.remove('hidden');
                };

                const fetchSuggestions = async (query) => {
                    if (abortController) {
                        abortController.abort();
                    }

                    abortController = new AbortController();

                    try {
                        const response = await fetch(`${searchUrl}?q=${encodeURIComponent(query)}`, {
                            headers: { Accept: 'application/json' },
                            signal: abortController.signal,
                        });

                        if (! response.ok) {
                            closeSuggestions();
                            return;
                        }

                        const payload = await response.json();
                        const items = (payload.data || [])
                            .filter((item) => ! isDuplicate('existing', item.id, item.name))
                            .slice(0, 8);
                        const canCreate = Boolean(payload.can_create) && ! isDuplicate('new', query, query);

                        renderSuggestions(items, query, canCreate);
                    } catch (error) {
                        if (error.name !== 'AbortError') {
                            closeSuggestions();
                        }
                    }
                };

                const commitFromKeyboard = () => {
                    const query = normalizeName(input.value);
                    if (! query) return;

                    const firstSelect = suggestions.querySelector('[data-author-action="select"]');
                    const exactSelect = Array.from(suggestions.querySelectorAll('[data-author-action="select"]')).find((button) => normalizeName(button.dataset.authorName || '').toLowerCase() === query.toLowerCase());
                    const createBtn = suggestions.querySelector('[data-author-action="create"]');

                    if (exactSelect) {
                        exactSelect.click();
                        return;
                    }

                    if (createBtn) {
                        createBtn.click();
                        return;
                    }

                    if (firstSelect) {
                        firstSelect.click();
                        return;
                    }

                    addTag({ type: 'new', value: query, name: query });
                };

                tags.addEventListener('click', (event) => {
                    const removeBtn = event.target.closest('[data-author-remove]');
                    if (removeBtn) {
                        removeTag(removeBtn.closest('[data-author-tag]'));
                    }
                });

                input.addEventListener('input', () => {
                    const query = normalizeName(input.value);
                    clearTimeout(debounceTimer);

                    if (! query) {
                        closeSuggestions();
                        return;
                    }

                    debounceTimer = setTimeout(() => fetchSuggestions(query), 220);
                });

                input.addEventListener('keydown', (event) => {
                    if (event.key === 'Enter') {
                        event.preventDefault();
                        commitFromKeyboard();
                        return;
                    }

                    if (event.key === 'Backspace' && input.value === '') {
                        const tagsList = getSelectedTags();
                        removeTag(tagsList[tagsList.length - 1]);
                    }
                });

                input.addEventListener('focus', () => {
                    const query = normalizeName(input.value);
                    if (query) {
                        fetchSuggestions(query);
                    }
                });

                input.addEventListener('blur', () => {
                    blurTimer = setTimeout(closeSuggestions, 150);
                });

                suggestions.addEventListener('mousedown', (event) => {
                    event.preventDefault();
                    clearTimeout(blurTimer);
                });

                suggestions.addEventListener('click', (event) => {
                    const button = event.target.closest('[data-author-action]');
                    if (! button) return;

                    const action = button.dataset.authorAction;
                    const name = normalizeName(button.dataset.authorName || button.textContent || '');

                    if (action === 'select') {
                        addTag({ type: 'existing', value: button.dataset.authorId || '', name });
                        return;
                    }

                    if (action === 'create') {
                        addTag({ type: 'new', value: name, name });
                    }
                });
            };

            const modal = document.getElementById('createBookModal');
            const openBtn = document.getElementById('openCreateBookModal');
            const closeBtn = document.getElementById('closeCreateBookModal');
            const cancelBtn = document.getElementById('cancelCreateBookModal');

            document.querySelectorAll('[data-author-picker]').forEach(initAuthorPicker);

            if (!modal || !openBtn || !closeBtn || !cancelBtn) return;

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
                if (event.target === modal) closeModal();
            });

            document.querySelectorAll('[data-edit-open]').forEach((button) => {
                button.addEventListener('click', () => {
                    const id = button.getAttribute('data-edit-open');
                    const editModal = document.getElementById(`editBookModal-${id}`);
                    if (editModal) {
                        editModal.classList.remove('hidden');
                        editModal.classList.add('flex');
                    }
                });
            });

            document.querySelectorAll('[data-edit-close]').forEach((button) => {
                button.addEventListener('click', () => {
                    const id = button.getAttribute('data-edit-close');
                    const editModal = document.getElementById(`editBookModal-${id}`);
                    if (editModal) {
                        editModal.classList.remove('flex');
                        editModal.classList.add('hidden');
                    }
                });
            });

            document.querySelectorAll('[id^="editBookModal-"]').forEach((editModal) => {
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