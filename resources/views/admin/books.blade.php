@extends('layouts.admin', ['title' => 'Quản lý sách'])

@section('content')
	@php
		$q = $q ?? trim((string) request()->query('q', ''));
		$books = $books ?? collect();
		$publishers = $publishers ?? collect();
		$categories = $categories ?? collect();
		$authors = $authors ?? collect();
		$authorsById = $authors->keyBy('id');

		$openCreateModal = old('_form') === 'create-book';
		$openEditBookId = old('_form') === 'update-book' ? (int) old('_book_id') : null;
		$editingBook = $openEditBookId ? \App\Models\Book::query()->with(['publisher', 'categories:id', 'authors:id,name'])->find($openEditBookId) : null;

		$selectedCreateCategories = collect(old('category_ids', []))->filter(fn ($id) => is_numeric($id))->map(fn ($id) => (int) $id)->all();
		$selectedEditCategories = collect(old('category_ids', $editingBook ? $editingBook->categories->pluck('id')->all() : []))
			->filter(fn ($id) => is_numeric($id))
			->map(fn ($id) => (int) $id)
			->all();

		$selectedCreateAuthors = collect(old('author_ids', []))->filter(fn ($id) => is_numeric($id))->map(fn ($id) => (int) $id)->all();
		$selectedCreateAuthorNames = collect(old('author_names', []))->map(fn ($name) => trim((string) $name))->filter()->values()->all();
		$selectedEditAuthors = collect(old('author_ids', $editingBook ? $editingBook->authors->pluck('id')->all() : []))
			->filter(fn ($id) => is_numeric($id))
			->map(fn ($id) => (int) $id)
			->all();
		$selectedEditAuthorNames = collect(old('author_names', []))->map(fn ($name) => trim((string) $name))->filter()->values()->all();

		$createAuthorItems = collect($selectedCreateAuthors)->map(function ($id) use ($authorsById) {
			$author = $authorsById->get($id);

			return $author ? ['type' => 'existing', 'id' => (int) $author->id, 'name' => $author->name] : null;
		})->filter()->values()->all();
		$createAuthorItems = array_merge($createAuthorItems, collect($selectedCreateAuthorNames)->map(fn ($name) => ['type' => 'new', 'id' => null, 'name' => $name])->values()->all());

		$editAuthorItems = collect($selectedEditAuthors)->map(function ($id) use ($authorsById) {
			$author = $authorsById->get($id);

			return $author ? ['type' => 'existing', 'id' => (int) $author->id, 'name' => $author->name] : null;
		})->filter()->values()->all();
		$editAuthorItems = array_merge($editAuthorItems, collect($selectedEditAuthorNames)->map(fn ($name) => ['type' => 'new', 'id' => null, 'name' => $name])->values()->all());

		$statusLabels = [
			'available' => 'Đang bán',
			'hidden' => 'Đang ẩn',
			'out_of_stock' => 'Hết hàng',
		];

		$statusColors = [
			'available' => 'bg-emerald-50 text-emerald-700 border-emerald-200',
			'hidden' => 'bg-slate-50 text-slate-700 border-slate-200',
			'out_of_stock' => 'bg-rose-50 text-rose-700 border-rose-200',
		];
	@endphp

	<style>
		.admin-modal {
			position: fixed;
			inset: 0;
			z-index: 60;
			display: flex;
			align-items: flex-start;
			justify-content: center;
			overflow-y: auto;
			padding: 24px 16px;
			background: rgba(15, 23, 42, 0.45);
			backdrop-filter: blur(4px);
		}

		.admin-modal.hidden { display: none; }

		.admin-modal-content {
			width: 100%;
			max-width: 980px;
			overflow: hidden;
			border-radius: 20px;
			background: #fff;
			box-shadow: 0 24px 60px rgba(15, 23, 42, 0.18);
		}

		.admin-modal-header {
			display: flex;
			align-items: flex-start;
			justify-content: space-between;
			gap: 16px;
			border-bottom: 1px solid #e2e8f0;
			padding: 20px 24px;
		}

		.admin-modal-header h2 {
			margin: 0;
			font-size: 18px;
			font-weight: 800;
			color: #0f172a;
		}

		.admin-modal-header p {
			margin: 4px 0 0;
			font-size: 14px;
			color: #64748b;
		}

		.admin-modal-close {
			display: inline-flex;
			align-items: center;
			justify-content: center;
			width: 36px;
			height: 36px;
			border: 0;
			border-radius: 999px;
			background: #f8fafc;
			color: #64748b;
			cursor: pointer;
		}

		.admin-modal-body {
			display: flex;
			flex-direction: column;
			gap: 16px;
			padding: 24px;
		}

		.admin-modal-footer {
			display: flex;
			justify-content: flex-end;
			gap: 12px;
			border-top: 1px solid #e2e8f0;
			padding-top: 16px;
		}

		.admin-form-group {
			display: flex;
			flex-direction: column;
			gap: 8px;
		}

		.admin-form-label {
			font-size: 14px;
			font-weight: 700;
			color: #0f172a;
		}

		.admin-form-input {
			width: 100%;
			border: 1px solid #e2e8f0;
			border-radius: 14px;
			background: #fff;
			padding: 12px 14px;
			font-size: 14px;
			color: #0f172a;
			outline: none;
		}

		.admin-form-input:focus {
			border-color: #f97316;
			box-shadow: 0 0 0 3px rgba(249, 115, 22, 0.12);
		}

		.author-picker {
			position: relative;
			display: flex;
			flex-direction: column;
			gap: 10px;
		}

		.author-picker-tags {
			display: flex;
			flex-wrap: wrap;
			gap: 8px;
		}

		.author-tag {
			display: inline-flex;
			align-items: center;
			gap: 8px;
			border: 1px solid #e2e8f0;
			border-radius: 999px;
			background: #f8fafc;
			padding: 6px 10px;
			font-size: 13px;
			font-weight: 600;
			color: #0f172a;
		}

		.author-tag--new {
			border-color: #fed7aa;
			background: #fff7ed;
			color: #9a3412;
		}

		.author-tag-remove {
			display: inline-flex;
			align-items: center;
			justify-content: center;
			width: 18px;
			height: 18px;
			border: 0;
			border-radius: 999px;
			background: #e2e8f0;
			color: #334155;
			font-size: 12px;
			cursor: pointer;
		}

		.author-picker-input {
			min-height: 46px;
		}

		.author-picker-suggestions {
			position: absolute;
			left: 0;
			right: 0;
			top: calc(100% + 6px);
			z-index: 80;
			max-height: 240px;
			overflow-y: auto;
			border: 1px solid #e2e8f0;
			border-radius: 14px;
			background: #fff;
			box-shadow: 0 18px 40px rgba(15, 23, 42, 0.12);
		}

		.author-suggestion-item {
			display: flex;
			width: 100%;
			align-items: center;
			justify-content: space-between;
			gap: 12px;
			border: 0;
			border-bottom: 1px solid #f1f5f9;
			background: #fff;
			padding: 10px 14px;
			text-align: left;
			cursor: pointer;
		}

		.author-suggestion-item:hover {
			background: #f8fafc;
		}

		.author-suggestion-item:last-child {
			border-bottom: 0;
		}

		.author-suggestion-name {
			font-size: 14px;
			font-weight: 700;
			color: #0f172a;
		}

		.author-suggestion-meta {
			font-size: 12px;
			color: #64748b;
		}

		@media (max-width: 640px) {
			.admin-modal { padding: 12px; }
			.admin-modal-content { border-radius: 16px; }
			.admin-modal-header,
			.admin-modal-body { padding-left: 16px; padding-right: 16px; }
			.admin-modal-footer { flex-direction: column; }
			.admin-modal-footer > * { width: 100%; }
		}
	</style>

	<div class="space-y-6">
		<div class="flex flex-col gap-4 rounded-xl border border-slate-200 bg-white p-4 shadow-sm md:flex-row md:items-center md:justify-between">
			<div>
				<h1 class="text-xl font-bold text-slate-900">Quản lý sách</h1>
				<p class="mt-1 text-sm text-slate-500">Thêm, sửa, xóa và xem sách từ database.</p>
			</div>

			<div class="flex flex-wrap items-center gap-3">
				<form method="GET" action="{{ route('admin.books.index') }}" class="relative w-full sm:w-72">
					<svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
						<path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
					</svg>
					<input type="search" name="q" value="{{ $q }}" placeholder="Tìm tên sách hoặc ISBN..." class="w-full rounded-lg border border-slate-300 bg-slate-50 py-2 pl-9 pr-4 text-sm outline-none transition-all focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100">
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
				<table class="min-w-[1100px] w-full text-left text-sm whitespace-nowrap">
					<thead class="bg-slate-50 text-slate-600">
						<tr>
							<th class="px-5 py-4 font-semibold">Thông tin sách</th>
							<th class="px-5 py-4 font-semibold">Kho & giá</th>
							<th class="px-5 py-4 font-semibold">Trạng thái</th>
							<th class="px-5 py-4 font-semibold text-right">Hành động</th>
						</tr>
					</thead>
					<tbody class="divide-y divide-slate-100 text-slate-700">
						@forelse($books as $book)
							<tr class="transition-colors hover:bg-slate-50/60">
								<td class="px-5 py-4 align-top">
									<div class="flex items-start gap-3">
										<div class="flex h-14 w-10 shrink-0 items-center justify-center overflow-hidden rounded-md border border-slate-200 bg-slate-100">
											@if($book->cover_image)
												<img src="{{ asset('storage/'.$book->cover_image) }}" alt="{{ $book->title }}" class="h-full w-full object-cover">
											@else
												<svg class="h-5 w-5 text-slate-300" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
													<path fill-rule="evenodd" d="M4 4a2 2 0 012-2h8a2 2 0 012 2v12a1 1 0 110 2h-3a1 1 0 01-1-1v-2a1 1 0 00-1-1H9a1 1 0 00-1 1v2a1 1 0 01-1 1H4a1 1 0 110-2V4zm3 1h2v2H7V5zm2 4H7v2h2V9zm2-4h2v2h-2V5zm2 4h-2v2h2V9z" clip-rule="evenodd" />
												</svg>
											@endif
										</div>

										<div class="min-w-0">
											<p class="font-bold text-slate-900 line-clamp-1 whitespace-normal">{{ $book->title }}</p>
											<p class="mt-0.5 text-xs text-slate-500">ISBN: <span class="font-medium text-slate-700">{{ $book->isbn ?: 'N/A' }}</span></p>
											<div class="mt-1 flex flex-wrap items-center gap-2 text-xs text-slate-500">
												<span class="rounded bg-slate-100 px-1.5 py-0.5">{{ $book->publisher?->name ?: 'Chưa cập nhật' }}</span>
												<span>{{ $book->categories_count }} danh mục</span>
												<span>{{ $book->authors->count() }} tác giả</span>
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
										<p class="mt-1 text-xs text-slate-500">Tồn kho: <span class="font-medium {{ $book->stock_quantity > 0 ? 'text-emerald-600' : 'text-rose-600' }}">{{ $book->stock_quantity }}</span></p>
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
											class="inline-flex items-center rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-xs font-medium text-slate-600 shadow-sm transition-colors hover:bg-slate-50 hover:text-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1"
											data-edit-book="true"
											data-book-id="{{ $book->id }}"
											data-book-title="{{ e($book->title) }}"
											data-book-isbn="{{ e($book->isbn ?? '') }}"
											data-book-description="{{ e($book->description ?? '') }}"
											data-book-price="{{ $book->price }}"
											data-book-discount-price="{{ $book->discount_price ?? '' }}"
											data-book-stock-quantity="{{ $book->stock_quantity }}"
											data-book-page-count="{{ $book->page_count ?? '' }}"
											data-book-language="{{ e($book->language ?? '') }}"
											data-book-publication-year="{{ $book->publication_year ?? '' }}"
											data-book-status="{{ $book->status }}"
											data-book-publisher-id="{{ $book->publisher_id ?? '' }}"
										>
											Sửa
										</button>

										<form method="POST" action="{{ route('admin.books.destroy', $book) }}" class="inline-block" onsubmit="return confirm('Xóa sách này?');">
											@csrf
											@method('DELETE')
											<button type="submit" class="inline-flex items-center rounded-lg border border-rose-200 bg-rose-50 px-2.5 py-1.5 text-xs font-medium text-rose-600 shadow-sm transition-colors hover:bg-rose-100 hover:text-rose-700 focus:outline-none focus:ring-2 focus:ring-rose-500 focus:ring-offset-1">Xóa</button>
										</form>
									</div>
								</td>
							</tr>
						@empty
							<tr>
								<td colspan="4" class="px-5 py-10">
									<div class="text-center">
										<p class="text-sm font-semibold text-slate-700">Không có sách</p>
										<p class="mt-1 text-sm text-slate-500">Hãy thêm cuốn sách đầu tiên để bắt đầu.</p>
									</div>
								</td>
							</tr>
						@endforelse
					</tbody>
				</table>
			</div>
		</div>

		<div>
			{{ $books->links() }}
		</div>
	</div>

	<div id="createBookModal" class="admin-modal {{ $openCreateModal ? '' : 'hidden' }}">
		<div class="admin-modal-content">
			<div class="admin-modal-header">
				<div>
					<h2>Thêm sách mới</h2>
					<p>Nhập thông tin sách và lưu vào database.</p>
				</div>
				<button type="button" id="closeCreateBookModal" class="admin-modal-close">✕</button>
			</div>

			<form method="POST" action="{{ route('admin.books.store') }}" enctype="multipart/form-data" class="admin-modal-body">
				@csrf
				<input type="hidden" name="_form" value="create-book">

				<div class="grid gap-4 md:grid-cols-2">
					<div class="admin-form-group md:col-span-2">
						<label class="admin-form-label">Tên sách</label>
						<input type="text" name="title" value="{{ old('title') }}" class="admin-form-input" required>
					</div>
					<div class="admin-form-group">
						<label class="admin-form-label">ISBN</label>
						<input type="text" name="isbn" value="{{ old('isbn') }}" class="admin-form-input" placeholder="Tùy chọn">
					</div>
					<div class="admin-form-group">
						<label class="admin-form-label">Nhà xuất bản</label>
						<select name="publisher_id" class="admin-form-input">
							<option value="">Chọn nhà xuất bản</option>
							@foreach($publishers as $publisher)
								<option value="{{ $publisher->id }}" @selected((string) old('publisher_id') === (string) $publisher->id)>{{ $publisher->name }}</option>
							@endforeach
						</select>
					</div>
					<div class="admin-form-group md:col-span-2">
						<label class="admin-form-label">Mô tả</label>
						<textarea name="description" rows="4" class="admin-form-input">{{ old('description') }}</textarea>
					</div>
					<div class="admin-form-group">
						<label class="admin-form-label">Giá bán</label>
						<input type="number" name="price" min="0" step="1000" value="{{ old('price') }}" class="admin-form-input" required>
					</div>
					<div class="admin-form-group">
						<label class="admin-form-label">Giá khuyến mãi</label>
						<input type="number" name="discount_price" min="0" step="1000" value="{{ old('discount_price') }}" class="admin-form-input">
					</div>
					<div class="admin-form-group">
						<label class="admin-form-label">Tồn kho</label>
						<input type="number" name="stock_quantity" min="0" value="{{ old('stock_quantity', 0) }}" class="admin-form-input" required>
					</div>
					<div class="admin-form-group">
						<label class="admin-form-label">Số trang</label>
						<input type="number" name="page_count" min="1" value="{{ old('page_count') }}" class="admin-form-input">
					</div>
					<div class="admin-form-group">
						<label class="admin-form-label">Ngôn ngữ</label>
						<input type="text" name="language" value="{{ old('language') }}" class="admin-form-input">
					</div>
					<div class="admin-form-group">
						<label class="admin-form-label">Năm xuất bản</label>
						<input type="number" name="publication_year" min="1900" max="{{ now()->year + 1 }}" value="{{ old('publication_year') }}" class="admin-form-input">
					</div>
					<div class="admin-form-group">
						<label class="admin-form-label">Trạng thái</label>
						<select name="status" class="admin-form-input" required>
							<option value="available" @selected(old('status', 'available') === 'available')>Đang bán</option>
							<option value="hidden" @selected(old('status') === 'hidden')>Đang ẩn</option>
							<option value="out_of_stock" @selected(old('status') === 'out_of_stock')>Hết hàng</option>
						</select>
					</div>
					<div class="admin-form-group md:col-span-2">
						<label class="admin-form-label">Danh mục</label>
						<select name="category_ids[]" multiple class="admin-form-input" size="6">
							@foreach($categories as $category)
								<option value="{{ $category->id }}" @selected(in_array((int) $category->id, $selectedCreateCategories, true))>{{ $category->name }}</option>
							@endforeach
						</select>
					</div>
					<div class="admin-form-group md:col-span-2">
						<label class="admin-form-label">Tác giả</label>
						<div class="author-picker" data-author-picker data-search-url="{{ route('admin.books.authors.search') }}" data-author-items='@json($createAuthorItems)'>
							<div class="author-picker-tags" data-author-tags></div>
							<input type="text" class="admin-form-input author-picker-input" data-author-input placeholder="Gõ tên tác giả để tìm hoặc thêm mới..." autocomplete="off">
							<div class="author-picker-suggestions hidden" data-author-suggestions></div>
						</div>
					</div>
					<div class="admin-form-group md:col-span-2">
						<label class="admin-form-label">Ảnh bìa</label>
						<input type="file" name="cover_image_file" class="admin-form-input">
					</div>
				</div>

				<div class="admin-modal-footer">
					<button type="button" id="cancelCreateBookModal" class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-slate-400 hover:bg-slate-50">Hủy</button>
					<button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700">Tạo sách</button>
				</div>
			</form>
		</div>
	</div>

	<div id="editBookModal" class="admin-modal {{ $editingBook ? '' : 'hidden' }}">
		<div class="admin-modal-content">
			<div class="admin-modal-header">
				<div>
					<h2>Sửa sách</h2>
					<p>Cập nhật thông tin sách đã có trong database.</p>
				</div>
				<button type="button" id="closeEditBookModal" class="admin-modal-close">✕</button>
			</div>

			<form method="POST" action="{{ $editingBook ? route('admin.books.update', $editingBook) : '#' }}" enctype="multipart/form-data" id="editBookForm" class="admin-modal-body">
				@csrf
				@method('PATCH')
				<input type="hidden" name="_form" value="update-book">
				<input type="hidden" name="_book_id" value="{{ $editingBook?->id }}">

				<div class="grid gap-4 md:grid-cols-2">
					<div class="admin-form-group md:col-span-2">
						<label class="admin-form-label">Tên sách</label>
						<input type="text" name="title" value="{{ $editingBook ? old('title', $editingBook->title) : '' }}" class="admin-form-input" required>
					</div>
					<div class="admin-form-group">
						<label class="admin-form-label">ISBN</label>
						<input type="text" name="isbn" value="{{ $editingBook ? old('isbn', $editingBook->isbn) : '' }}" class="admin-form-input">
					</div>
					<div class="admin-form-group">
						<label class="admin-form-label">Nhà xuất bản</label>
						<select name="publisher_id" class="admin-form-input">
							<option value="">Chọn nhà xuất bản</option>
							@foreach($publishers as $publisher)
								<option value="{{ $publisher->id }}" @selected((string) old('publisher_id', $editingBook?->publisher_id) === (string) $publisher->id)>{{ $publisher->name }}</option>
							@endforeach
						</select>
					</div>
					<div class="admin-form-group md:col-span-2">
						<label class="admin-form-label">Mô tả</label>
						<textarea name="description" rows="4" class="admin-form-input">{{ $editingBook ? old('description', $editingBook->description) : '' }}</textarea>
					</div>
					<div class="admin-form-group">
						<label class="admin-form-label">Giá bán</label>
						<input type="number" name="price" min="0" step="1000" value="{{ $editingBook ? old('price', $editingBook->price) : '' }}" class="admin-form-input" required>
					</div>
					<div class="admin-form-group">
						<label class="admin-form-label">Giá khuyến mãi</label>
						<input type="number" name="discount_price" min="0" step="1000" value="{{ $editingBook ? old('discount_price', $editingBook->discount_price) : '' }}" class="admin-form-input">
					</div>
					<div class="admin-form-group">
						<label class="admin-form-label">Tồn kho</label>
						<input type="number" name="stock_quantity" min="0" value="{{ $editingBook ? old('stock_quantity', $editingBook->stock_quantity) : 0 }}" class="admin-form-input" required>
					</div>
					<div class="admin-form-group">
						<label class="admin-form-label">Số trang</label>
						<input type="number" name="page_count" min="1" value="{{ $editingBook ? old('page_count', $editingBook->page_count) : '' }}" class="admin-form-input">
					</div>
					<div class="admin-form-group">
						<label class="admin-form-label">Ngôn ngữ</label>
						<input type="text" name="language" value="{{ $editingBook ? old('language', $editingBook->language) : '' }}" class="admin-form-input">
					</div>
					<div class="admin-form-group">
						<label class="admin-form-label">Năm xuất bản</label>
						<input type="number" name="publication_year" min="1900" max="{{ now()->year + 1 }}" value="{{ $editingBook ? old('publication_year', $editingBook->publication_year) : '' }}" class="admin-form-input">
					</div>
					<div class="admin-form-group">
						<label class="admin-form-label">Trạng thái</label>
						<select name="status" class="admin-form-input" required>
							<option value="available" @selected(($editingBook ? old('status', $editingBook->status) : 'available') === 'available')>Đang bán</option>
							<option value="hidden" @selected(($editingBook ? old('status', $editingBook->status) : '') === 'hidden')>Đang ẩn</option>
							<option value="out_of_stock" @selected(($editingBook ? old('status', $editingBook->status) : '') === 'out_of_stock')>Hết hàng</option>
						</select>
					</div>
					<div class="admin-form-group md:col-span-2">
						<label class="admin-form-label">Danh mục</label>
						<select name="category_ids[]" multiple class="admin-form-input" size="6">
							@foreach($categories as $category)
								<option value="{{ $category->id }}" @selected(in_array((int) $category->id, $selectedEditCategories, true))>{{ $category->name }}</option>
							@endforeach
						</select>
					</div>
					<div class="admin-form-group md:col-span-2">
						<label class="admin-form-label">Tác giả</label>
						<div class="author-picker" data-author-picker data-search-url="{{ route('admin.books.authors.search') }}" data-author-items='@json($editAuthorItems)'>
							<div class="author-picker-tags" data-author-tags></div>
							<input type="text" class="admin-form-input author-picker-input" data-author-input placeholder="Gõ tên tác giả để tìm hoặc thêm mới..." autocomplete="off">
							<div class="author-picker-suggestions hidden" data-author-suggestions></div>
						</div>
					</div>
					<div class="admin-form-group md:col-span-2">
						<label class="admin-form-label">Ảnh bìa</label>
						<input type="file" name="cover_image_file" class="admin-form-input">
					</div>
				</div>

				<div class="admin-modal-footer">
					<button type="button" id="cancelEditBookModal" class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-slate-400 hover:bg-slate-50">Hủy</button>
					<button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700">Lưu thay đổi</button>
				</div>
			</form>
		</div>
	</div>

	<script>
		(function () {
			const normalize = (value) => String(value ?? '').replace(/\s+/g, ' ').trim();
			const lower = (value) => normalize(value).toLowerCase();

			const createModal = document.getElementById('createBookModal');
			const editModal = document.getElementById('editBookModal');
			const openCreateButton = document.getElementById('openCreateBookModal');
			const closeCreateButton = document.getElementById('closeCreateBookModal');
			const cancelCreateButton = document.getElementById('cancelCreateBookModal');
			const closeEditButton = document.getElementById('closeEditBookModal');
			const cancelEditButton = document.getElementById('cancelEditBookModal');
			const editForm = document.getElementById('editBookForm');
			const editButtons = document.querySelectorAll('[data-edit-book="true"]');
			const pickerList = document.querySelectorAll('[data-author-picker]');

			const renderTag = (picker, item) => {
				const tags = picker.querySelector('[data-author-tags]');
				if (!tags) return;

				const normalizedName = normalize(item.name);
				if (!normalizedName) return;

				const currentNames = Array.from(tags.querySelectorAll('[data-author-tag-name]')).map((element) => lower(element.textContent));

				const existingSameId = item.type === 'existing'
					? tags.querySelector(`input[name="author_ids[]"][value="${item.id}"]`)
					: null;
				const existingSameName = currentNames.includes(lower(normalizedName));

				if (existingSameId || existingSameName) return;

				const tag = document.createElement('span');
				tag.className = `author-tag ${item.type === 'new' ? 'author-tag--new' : ''}`;
				tag.dataset.authorTag = 'true';

				const label = document.createElement('span');
				label.dataset.authorTagName = 'true';
				label.textContent = normalizedName;

				const hidden = document.createElement('input');
				hidden.type = 'hidden';
			hidden.name = item.type === 'existing' ? 'author_ids[]' : 'author_names[]';
			hidden.value = item.type === 'existing' ? String(item.id) : normalizedName;

				const remove = document.createElement('button');
				remove.type = 'button';
				remove.className = 'author-tag-remove';
				remove.textContent = '×';
				remove.addEventListener('click', () => tag.remove());

				tag.appendChild(label);
				tag.appendChild(hidden);
				tag.appendChild(remove);
				tags.appendChild(tag);
			};

			const renderSuggestions = async (picker, query) => {
				const suggestions = picker.querySelector('[data-author-suggestions]');
				if (!suggestions) return;

				const searchUrl = picker.dataset.searchUrl || '';
				const keyword = normalize(query);

				if (keyword.length < 1) {
					suggestions.classList.add('hidden');
					suggestions.innerHTML = '';
					return;
				}

				const response = await fetch(`${searchUrl}?q=${encodeURIComponent(keyword)}`, {
					headers: { 'Accept': 'application/json' },
				});

				if (!response.ok) {
					suggestions.classList.add('hidden');
					suggestions.innerHTML = '';
					return;
				}

				const payload = await response.json();
				const authors = Array.isArray(payload.data) ? payload.data : [];
				const canCreate = Boolean(payload.can_create);

				suggestions.innerHTML = '';

				authors.forEach((author) => {
					const button = document.createElement('button');
					button.type = 'button';
					button.className = 'author-suggestion-item';
					button.innerHTML = `<span class="author-suggestion-name">${author.name}</span><span class="author-suggestion-meta">Chọn</span>`;
					button.addEventListener('click', () => {
						renderTag(picker, { type: 'existing', id: author.id, name: author.name });
						const input = picker.querySelector('[data-author-input]');
						if (input) input.value = '';
						suggestions.classList.add('hidden');
						suggestions.innerHTML = '';
						input?.focus();
					});
					suggestions.appendChild(button);
				});

				if (canCreate) {
					const button = document.createElement('button');
					button.type = 'button';
					button.className = 'author-suggestion-item';
					button.innerHTML = `<span class="author-suggestion-name">Thêm mới: ${keyword}</span><span class="author-suggestion-meta">Tạo tác giả</span>`;
					button.addEventListener('click', () => {
						renderTag(picker, { type: 'new', id: null, name: keyword });
						const input = picker.querySelector('[data-author-input]');
						if (input) input.value = '';
						suggestions.classList.add('hidden');
						suggestions.innerHTML = '';
						input?.focus();
					});
					suggestions.appendChild(button);
				}

				if (authors.length === 0 && !canCreate) {
					const empty = document.createElement('div');
					empty.className = 'author-suggestion-item';
					empty.style.cursor = 'default';
					empty.innerHTML = '<span class="author-suggestion-name">Không tìm thấy tác giả</span><span class="author-suggestion-meta">Hãy nhập tên khác</span>';
					suggestions.appendChild(empty);
				}

				suggestions.classList.remove('hidden');
			};

			pickerList.forEach((picker) => {
				const input = picker.querySelector('[data-author-input]');
				const suggestions = picker.querySelector('[data-author-suggestions]');
				const initialItems = JSON.parse(picker.dataset.authorItems || '[]');

				initialItems.forEach((item) => renderTag(picker, item));

				if (!input || !suggestions) return;

				let debounce = null;

				input.addEventListener('input', () => {
					clearTimeout(debounce);
					debounce = setTimeout(() => renderSuggestions(picker, input.value), 180);
				});

				input.addEventListener('focus', () => {
					if (normalize(input.value)) {
						renderSuggestions(picker, input.value);
					}
				});

				input.addEventListener('keydown', (event) => {
					if (event.key === 'Enter') {
						event.preventDefault();
						const firstSuggestion = suggestions.querySelector('.author-suggestion-item');
						if (firstSuggestion) {
							firstSuggestion.click();
							return;
						}
						const keyword = normalize(input.value);
						if (keyword) {
							renderTag(picker, { type: 'new', id: null, name: keyword });
							input.value = '';
							suggestions.classList.add('hidden');
							suggestions.innerHTML = '';
						}
					}
				});

				document.addEventListener('click', (event) => {
					if (!picker.contains(event.target)) {
						suggestions.classList.add('hidden');
					}
				});
			});

			const openModal = (modal) => modal?.classList.remove('hidden');
			const closeModal = (modal) => modal?.classList.add('hidden');

			if (openCreateButton) openCreateButton.addEventListener('click', () => openModal(createModal));
			if (closeCreateButton) closeCreateButton.addEventListener('click', () => closeModal(createModal));
			if (cancelCreateButton) cancelCreateButton.addEventListener('click', () => closeModal(createModal));
			if (closeEditButton) closeEditButton.addEventListener('click', () => closeModal(editModal));
			if (cancelEditButton) cancelEditButton.addEventListener('click', () => closeModal(editModal));

			editButtons.forEach((button) => {
				button.addEventListener('click', () => {
					if (!editForm) return;

					const bookId = button.dataset.bookId || '';
					const title = button.dataset.bookTitle || '';
					const isbn = button.dataset.bookIsbn || '';
					const description = button.dataset.bookDescription || '';
					const price = button.dataset.bookPrice || '';
					const discountPrice = button.dataset.bookDiscountPrice || '';
					const stockQuantity = button.dataset.bookStockQuantity || '';
					const pageCount = button.dataset.bookPageCount || '';
					const language = button.dataset.bookLanguage || '';
					const publicationYear = button.dataset.bookPublicationYear || '';
					const status = button.dataset.bookStatus || 'available';
					const publisherId = button.dataset.bookPublisherId || '';

					editForm.action = `/admin/books/${bookId}`;
					editForm.querySelector('input[name="_book_id"]').value = bookId;
					editForm.querySelector('input[name="title"]').value = title;
					editForm.querySelector('input[name="isbn"]').value = isbn;
					editForm.querySelector('textarea[name="description"]').value = description;
					editForm.querySelector('input[name="price"]').value = price;
					editForm.querySelector('input[name="discount_price"]').value = discountPrice;
					editForm.querySelector('input[name="stock_quantity"]').value = stockQuantity;
					editForm.querySelector('input[name="page_count"]').value = pageCount;
					editForm.querySelector('input[name="language"]').value = language;
					editForm.querySelector('input[name="publication_year"]').value = publicationYear;
					editForm.querySelector('select[name="status"]').value = status;
					editForm.querySelector('select[name="publisher_id"]').value = publisherId;

					openModal(editModal);
				});
			});

			document.addEventListener('keydown', (event) => {
				if (event.key === 'Escape') {
					closeModal(createModal);
					closeModal(editModal);
				}
			});
		})();
	</script>
@endsection
