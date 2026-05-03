@extends('layouts.admin', ['title' => 'Quản lý danh mục'])

@section('content')
	@php
		$q = $q ?? trim((string) request()->query('q', ''));
		$categories = $categories ?? collect();
		$allCategories = $allCategories ?? collect();
		$categoryOptions = $categoryOptions ?? collect();

		$totalCategories = $allCategories->count();
		$rootCategories = $allCategories->whereNull('parent_id')->count();
		$leafCategories = $allCategories->filter(fn ($category) => ! (bool) ($category->has_children ?? false))->count();
		$categoriesWithBooks = $allCategories->filter(fn ($category) => (int) ($category->books_count ?? 0) > 0)->count();

		$openCreateModal = old('_form') === 'create-category';
		$openEditCategoryId = old('_form') === 'update-category' ? (int) old('_category_id') : null;
		$editingCategory = $openEditCategoryId ? $allCategories->firstWhere('id', $openEditCategoryId) : null;

		$createSelectedParentId = old('parent_id') !== null && old('parent_id') !== '' ? (int) old('parent_id') : null;
		$createSelectedParent = $createSelectedParentId ? $allCategories->firstWhere('id', $createSelectedParentId) : null;

		$editSelectedParentId = old('parent_id') !== null && old('parent_id') !== ''
			? (int) old('parent_id')
			: ($editingCategory?->parent_id ? (int) $editingCategory->parent_id : null);
		$editSelectedParent = $editSelectedParentId ? $allCategories->firstWhere('id', $editSelectedParentId) : null;
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

		.admin-modal.hidden {
			display: none;
		}

		.admin-modal-content {
			width: 100%;
			max-width: 640px;
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

		@media (max-width: 640px) {
			.admin-modal {
				padding: 12px;
			}

			.admin-modal-content {
				border-radius: 16px;
			}

			.admin-modal-header,
			.admin-modal-body {
				padding-left: 16px;
				padding-right: 16px;
			}

			.admin-modal-footer {
				flex-direction: column;
			}

			.admin-modal-footer > * {
				width: 100%;
			}
		}
	</style>

	<div class="space-y-6">
		<div class="flex flex-col gap-4 rounded-xl border border-slate-200 bg-white p-4 shadow-sm md:flex-row md:items-center md:justify-between">
			<div>
				<h1 class="text-xl font-bold text-slate-900">Quản lý danh mục</h1>
				<p class="mt-1 text-sm text-slate-500">Thêm, sửa, xóa và xem cây danh mục theo dữ liệu trong database.</p>
			</div>

			<div class="flex flex-wrap items-center gap-3">
				<form method="GET" action="{{ route('admin.categories.index') }}" class="relative w-full sm:w-72">
					<svg class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
						<path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 5.196a7.5 7.5 0 0010.607 10.607z" />
					</svg>
					<input
						type="search"
						name="q"
						value="{{ $q }}"
						placeholder="Tìm danh mục..."
						class="w-full rounded-lg border border-slate-300 bg-slate-50 py-2 pl-9 pr-4 text-sm outline-none transition-all focus:border-blue-500 focus:bg-white focus:ring-2 focus:ring-blue-100"
					>
				</form>

				<button
					type="button"
					id="openCreateCategoryModal"
					class="flex items-center justify-center whitespace-nowrap rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2"
				>
					<svg class="mr-1.5 h-4 w-4" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
						<path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
					</svg>
					Thêm danh mục
				</button>
			</div>
		</div>

		<div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-4">
			<div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
				<p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Tổng danh mục</p>
				<p class="mt-2 text-2xl font-bold text-slate-900">{{ $totalCategories }}</p>
			</div>
			<div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
				<p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Danh mục gốc</p>
				<p class="mt-2 text-2xl font-bold text-slate-900">{{ $rootCategories }}</p>
			</div>
			<div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
				<p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Danh mục lá</p>
				<p class="mt-2 text-2xl font-bold text-slate-900">{{ $leafCategories }}</p>
			</div>
			<div class="rounded-lg border border-slate-200 bg-white p-4 shadow-sm">
				<p class="text-xs font-semibold uppercase tracking-wide text-slate-500">Có gắn sách</p>
				<p class="mt-2 text-2xl font-bold text-slate-900">{{ $categoriesWithBooks }}</p>
			</div>
		</div>

		<div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
			<div class="overflow-x-auto">
				<table class="min-w-[980px] w-full text-left text-sm whitespace-nowrap">
					<thead class="bg-slate-50 text-slate-600">
						<tr>
							<th class="px-5 py-4 font-semibold">Danh mục</th>
							<th class="px-5 py-4 font-semibold">Sách</th>
							<th class="px-5 py-4 font-semibold">Con</th>
							<th class="px-5 py-4 font-semibold text-right">Hành động</th>
						</tr>
					</thead>
					<tbody class="divide-y divide-slate-100 text-slate-700">
						@forelse($categories as $category)
							<tr class="transition-colors hover:bg-slate-50/60">
								<td class="px-5 py-4 align-top">
									<div class="flex items-start gap-3">
										<div class="flex shrink-0 items-center pt-0.5">
											@for($depth = 0; $depth < (int) ($category->depth ?? 0); $depth++)
												<span class="inline-block w-4"></span>
											@endfor
											<span class="inline-flex h-2.5 w-2.5 rounded-full {{ (int) ($category->children_count ?? 0) > 0 ? 'bg-emerald-500' : 'bg-slate-300' }}"></span>
										</div>
										<div class="min-w-0">
											<div class="flex flex-wrap items-center gap-2">
												<span class="font-semibold text-slate-900">{{ $category->name }}</span>
												@if((int) ($category->children_count ?? 0) > 0)
													<span class="inline-flex rounded-full bg-emerald-50 px-2 py-0.5 text-xs font-semibold text-emerald-600">{{ $category->children_count }} con</span>
												@else
													<span class="inline-flex rounded-full bg-slate-100 px-2 py-0.5 text-xs font-semibold text-slate-600">Lá</span>
												@endif
											</div>
											<div class="mt-2 flex flex-wrap items-center gap-2 text-xs text-slate-500">
												@if($category->parent)
													<span>Cha: {{ $category->parent->name }}</span>
												@else
													<span>Gốc</span>
												@endif
											</div>
										</div>
									</div>
								</td>
								<td class="px-5 py-4 align-top">
									<span class="inline-flex rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-semibold text-slate-600">{{ (int) ($category->books_count ?? 0) }} sách</span>
								</td>
								<td class="px-5 py-4 align-top">
									<span class="inline-flex rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-semibold text-slate-600">{{ (int) ($category->children_count ?? 0) }}</span>
								</td>
								<td class="px-5 py-4 align-top">
									<div class="flex items-center justify-end gap-2">
										<button
											type="button"
											class="inline-flex items-center rounded-lg border border-slate-200 bg-white px-2.5 py-1.5 text-xs font-medium text-slate-600 shadow-sm transition-colors hover:bg-slate-50 hover:text-indigo-600 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1"
											data-edit-category="true"
											data-category-id="{{ $category->id }}"
											data-category-name="{{ e($category->name) }}"
											data-category-parent-id="{{ $category->parent_id ?? '' }}"
										>
											Sửa
										</button>

										<form method="POST" action="{{ route('admin.categories.destroy', $category) }}" class="inline-block" onsubmit="return confirm('Xóa danh mục này?');">
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
										<p class="text-sm font-semibold text-slate-700">Không có danh mục</p>
										<p class="mt-1 text-sm text-slate-500">Hãy tạo danh mục đầu tiên để bắt đầu.</p>
									</div>
								</td>
							</tr>
						@endforelse
					</tbody>
				</table>
			</div>
		</div>
	</div>

	<div id="createCategoryModal" class="admin-modal {{ $openCreateModal ? '' : 'hidden' }}">
		<div class="admin-modal-content">
			<div class="admin-modal-header">
				<div>
					<h2>Thêm danh mục mới</h2>
					<p>Tạo danh mục gốc hoặc gán vào danh mục cha.</p>
				</div>
				<button type="button" id="closeCreateCategoryModal" class="admin-modal-close">✕</button>
			</div>

			<form method="POST" action="{{ route('admin.categories.store') }}" class="admin-modal-body">
				@csrf
				<input type="hidden" name="_form" value="create-category">

				<div class="admin-form-group">
					<label class="admin-form-label">Tên danh mục</label>
					<input type="text" name="name" value="{{ old('name') }}" class="admin-form-input" placeholder="Nhập tên danh mục" required>
				</div>

				<div class="admin-form-group">
					<label class="admin-form-label">Danh mục cha</label>
					<select name="parent_id" class="admin-form-input">
						<option value="">Danh mục gốc</option>
						@foreach($allCategories as $option)
							<option value="{{ $option->id }}" @selected((string) $createSelectedParentId === (string) $option->id)>
								{{ str_repeat('— ', (int) ($option->depth ?? 0)) }}{{ $option->name }}
							</option>
						@endforeach
					</select>
				</div>

				<div class="admin-modal-footer">
					<button type="button" id="cancelCreateCategoryModal" class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-slate-400 hover:bg-slate-50">Hủy</button>
					<button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700">Tạo danh mục</button>
				</div>
			</form>
		</div>
	</div>

	<div id="editCategoryModal" class="admin-modal {{ $editingCategory ? '' : 'hidden' }}">
		<div class="admin-modal-content">
			<div class="admin-modal-header">
				<div>
					<h2>Sửa danh mục</h2>
					<p>Cập nhật tên, slug và danh mục cha.</p>
				</div>
				<button type="button" id="closeEditCategoryModal" class="admin-modal-close">✕</button>
			</div>

			<form method="POST" action="{{ $editingCategory ? route('admin.categories.update', $editingCategory) : '#' }}" id="editCategoryForm" class="admin-modal-body">
				@csrf
				@method('PATCH')
				<input type="hidden" name="_form" value="update-category">
				<input type="hidden" name="_category_id" value="{{ $editingCategory?->id }}">

				<div class="admin-form-group">
					<label class="admin-form-label">Tên danh mục</label>
					<input type="text" name="name" value="{{ $editingCategory ? old('name', $editingCategory->name) : '' }}" class="admin-form-input" placeholder="Nhập tên danh mục" required>
				</div>

				<div class="admin-form-group">
					<label class="admin-form-label">Danh mục cha</label>
					<select name="parent_id" class="admin-form-input">
						<option value="">Danh mục gốc</option>
						@foreach($allCategories as $option)
							@continue($editingCategory && (int) $option->id === (int) $editingCategory->id)
							<option value="{{ $option->id }}" @selected((string) $editSelectedParentId === (string) $option->id)>
								{{ str_repeat('— ', (int) ($option->depth ?? 0)) }}{{ $option->name }}
							</option>
						@endforeach
					</select>
				</div>

				<div class="admin-modal-footer">
					<button type="button" id="cancelEditCategoryModal" class="rounded-lg border border-slate-300 bg-white px-4 py-2 text-sm font-semibold text-slate-700 shadow-sm transition hover:border-slate-400 hover:bg-slate-50">Hủy</button>
					<button type="submit" class="rounded-lg bg-blue-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-blue-700">Lưu thay đổi</button>
				</div>
			</form>
		</div>
	</div>

	<script>
		(function () {
			const createModal = document.getElementById('createCategoryModal');
			const editModal = document.getElementById('editCategoryModal');
			const openCreateButton = document.getElementById('openCreateCategoryModal');
			const closeCreateButton = document.getElementById('closeCreateCategoryModal');
			const cancelCreateButton = document.getElementById('cancelCreateCategoryModal');
			const closeEditButton = document.getElementById('closeEditCategoryModal');
			const cancelEditButton = document.getElementById('cancelEditCategoryModal');
			const editForm = document.getElementById('editCategoryForm');
			const editButtons = document.querySelectorAll('[data-edit-category="true"]');

			const openModal = (modal) => modal?.classList.remove('hidden');
			const closeModal = (modal) => modal?.classList.add('hidden');

			if (openCreateButton) {
				openCreateButton.addEventListener('click', () => openModal(createModal));
			}

			if (closeCreateButton) {
				closeCreateButton.addEventListener('click', () => closeModal(createModal));
			}

			if (cancelCreateButton) {
				cancelCreateButton.addEventListener('click', () => closeModal(createModal));
			}

			if (closeEditButton) {
				closeEditButton.addEventListener('click', () => closeModal(editModal));
			}

			if (cancelEditButton) {
				cancelEditButton.addEventListener('click', () => closeModal(editModal));
			}

			editButtons.forEach((button) => {
				button.addEventListener('click', () => {
					if (!editForm) return;

					const categoryId = button.dataset.categoryId || '';
					const categoryName = button.dataset.categoryName || '';
					const categoryParentId = button.dataset.categoryParentId || '';

					editForm.action = `/admin/categories/${categoryId}`;
					editForm.querySelector('input[name="_category_id"]').value = categoryId;
					editForm.querySelector('input[name="name"]').value = categoryName;
					editForm.querySelector('select[name="parent_id"]').value = categoryParentId;

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
