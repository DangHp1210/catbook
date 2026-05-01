<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CatBook | Danh muc sach</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="cb-site bg-[#f8f6f1] text-slate-800">
    <x-navbar />

    @php
        $parentSlug = $selectedParent?->slug;
        $childSlug = $selectedChild?->slug;
        $queryBase = [
            'q' => $keyword,
            'sort' => $sortBy,
            'view' => $viewMode,
            'min_price' => $minPrice,
            'max_price' => $maxPrice,
            'language' => $languageFilter,
            'stock' => $stockFilter,
        ];
        $clearFilterUrl = route('catalog.categories', []);
    @endphp

    <main class="mx-auto w-full max-w-[1240px] px-4 pb-12 pt-7 sm:px-6 lg:px-8">
        <section class="rounded-3xl border border-emerald-100 bg-gradient-to-r from-[#fefaf0] via-white to-[#edf9f3] p-5 shadow-sm sm:p-7">
            <div class="flex flex-col gap-4 lg:flex-row lg:items-end lg:justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-emerald-700">Danh muc sach</p>
                    <h1 class="mt-2 text-3xl font-black text-slate-900">{{ $heading }}</h1>
                    <div class="mt-3 flex flex-wrap items-center gap-3 text-sm text-slate-600">
                        <span>Trang chu / Danh muc</span>
                        <span class="inline-flex items-center gap-2 rounded-full bg-white px-3 py-1 shadow-sm">
                            <strong class="text-slate-900">{{ $totalCategories }}</strong> danh muc
                        </span>
                        <span class="inline-flex items-center gap-2 rounded-full bg-white px-3 py-1 shadow-sm">
                            <strong class="text-slate-900">{{ $totalBooks }}</strong> dau sach
                        </span>
                    </div>
                </div>

                <form method="GET" action="{{ route('catalog.categories') }}" class="w-full max-w-xl">
                    @if ($parentSlug)
                        <input type="hidden" name="parent" value="{{ $parentSlug }}">
                    @endif
                    @if ($childSlug)
                        <input type="hidden" name="child" value="{{ $childSlug }}">
                    @endif
                    <div class="flex overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
                        <input
                            type="search"
                            name="q"
                            value="{{ $keyword }}"
                            placeholder="Tim sach, tac gia, ISBN..."
                            class="w-full border-0 bg-transparent px-4 py-3 text-sm text-slate-700 outline-none"
                        >
                        <button type="submit" class="bg-slate-900 px-5 text-sm font-semibold text-white transition hover:bg-emerald-700">
                            Tim nhanh
                        </button>
                    </div>
                </form>
            </div>
        </section>

        <section class="mt-5">
            <div class="flex flex-wrap gap-2">
                @foreach ($parentCategories as $parent)
                    @php
                        $parentQuery = array_filter(array_merge($queryBase, [
                            'parent' => $parent->slug,
                            'child' => null,
                            'page' => null,
                        ]), fn ($value) => $value !== null && $value !== '');
                    @endphp
                    <a
                        href="{{ route('catalog.categories', $parentQuery) }}"
                        class="inline-flex items-center gap-2 rounded-full border px-4 py-2 text-sm font-semibold transition {{ $selectedParent && $selectedParent->id === $parent->id ? 'border-emerald-600 bg-emerald-600 text-white shadow-sm' : 'border-slate-200 bg-white text-slate-700 hover:border-emerald-300 hover:text-emerald-700' }}"
                    >
                        <span>{{ $parent->name }}</span>
                        <span class="rounded-full px-2 py-0.5 text-xs {{ $selectedParent && $selectedParent->id === $parent->id ? 'bg-white/25 text-white' : 'bg-slate-100 text-slate-600' }}">{{ $parent->children_count }}</span>
                    </a>
                @endforeach
            </div>
        </section>

        <section class="mt-6 grid gap-6 lg:grid-cols-[300px_1fr]">
            <aside class="space-y-5">
                <div class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                    <h2 class="text-sm font-black uppercase tracking-[0.14em] text-slate-500">Danh muc con</h2>
                    <div class="mt-3 space-y-1.5">
                        @if ($selectedParent)
                            @php
                                $allChildQuery = array_filter(array_merge($queryBase, [
                                    'parent' => $selectedParent->slug,
                                    'child' => null,
                                    'page' => null,
                                ]), fn ($value) => $value !== null && $value !== '');
                            @endphp
                            <a
                                href="{{ route('catalog.categories', $allChildQuery) }}"
                                class="flex items-center justify-between rounded-xl px-3 py-2 text-sm transition {{ $selectedChild ? 'text-slate-700 hover:bg-slate-50' : 'bg-emerald-50 font-semibold text-emerald-700' }}"
                            >
                                <span>Tat ca trong {{ $selectedParent->name }}</span>
                            </a>
                        @endif

                        @forelse ($childCategories as $child)
                            @php
                                $childQuery = array_filter(array_merge($queryBase, [
                                    'parent' => $selectedParent?->slug,
                                    'child' => $child->slug,
                                    'page' => null,
                                ]), fn ($value) => $value !== null && $value !== '');
                            @endphp
                            <a
                                href="{{ route('catalog.categories', $childQuery) }}"
                                class="flex items-center justify-between rounded-xl px-3 py-2 text-sm transition {{ $selectedChild && $selectedChild->id === $child->id ? 'bg-emerald-50 font-semibold text-emerald-700' : 'text-slate-700 hover:bg-slate-50' }}"
                            >
                                <span>{{ $child->name }}</span>
                                <span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs text-slate-600">{{ $child->books_count }}</span>
                            </a>
                        @empty
                            <p class="rounded-xl bg-slate-50 px-3 py-2 text-sm text-slate-500">Khong co danh muc con.</p>
                        @endforelse
                    </div>
                </div>

                <form method="GET" action="{{ route('catalog.categories') }}" class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                    @if ($parentSlug)
                        <input type="hidden" name="parent" value="{{ $parentSlug }}">
                    @endif
                    @if ($childSlug)
                        <input type="hidden" name="child" value="{{ $childSlug }}">
                    @endif
                    <input type="hidden" name="q" value="{{ $keyword }}">
                    <input type="hidden" name="sort" value="{{ $sortBy }}">
                    <input type="hidden" name="view" value="{{ $viewMode }}">

                    <h2 class="text-sm font-black uppercase tracking-[0.14em] text-slate-500">Bo loc</h2>

                    <div class="mt-4">
                        <h3 class="text-sm font-semibold text-slate-800">Khoang gia</h3>
                        <div class="mt-3 space-y-3">
                            <label class="block text-xs text-slate-500">Tu</label>
                            <input id="minRange" type="range" name="min_price" min="{{ $minPossiblePrice }}" max="{{ $maxPossiblePrice }}" value="{{ $minPrice }}" class="w-full accent-emerald-600">
                            <label class="block text-xs text-slate-500">Den</label>
                            <input id="maxRange" type="range" name="max_price" min="{{ $minPossiblePrice }}" max="{{ $maxPossiblePrice }}" value="{{ $maxPrice }}" class="w-full accent-emerald-600">
                            <div class="flex items-center justify-between rounded-xl bg-slate-50 px-3 py-2 text-sm font-semibold text-slate-700">
                                <span id="minRangeLabel">{{ number_format((float) $minPrice, 0, ',', '.') }}đ</span>
                                <span id="maxRangeLabel">{{ number_format((float) $maxPrice, 0, ',', '.') }}đ</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-5">
                        <h3 class="text-sm font-semibold text-slate-800">Ngon ngu</h3>
                        <select name="language" class="mt-2 w-full rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm text-slate-700">
                            <option value="">Tat ca ngon ngu</option>
                            @foreach ($availableLanguages as $language)
                                <option value="{{ $language }}" {{ $languageFilter === $language ? 'selected' : '' }}>{{ $language }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mt-5">
                        <h3 class="text-sm font-semibold text-slate-800">Tinh trang hang</h3>
                        <div class="mt-2 space-y-1.5 text-sm text-slate-700">
                            <label class="flex items-center gap-2"><input type="radio" name="stock" value="all" {{ $stockFilter === 'all' ? 'checked' : '' }}> Tat ca</label>
                            <label class="flex items-center gap-2"><input type="radio" name="stock" value="in_stock" {{ $stockFilter === 'in_stock' ? 'checked' : '' }}> Con hang</label>
                            <label class="flex items-center gap-2"><input type="radio" name="stock" value="out_of_stock" {{ $stockFilter === 'out_of_stock' ? 'checked' : '' }}> Tam het hang</label>
                        </div>
                    </div>

                    <div class="mt-5 grid grid-cols-2 gap-2">
                        <button type="submit" class="rounded-xl bg-slate-900 px-3 py-2 text-sm font-semibold text-white transition hover:bg-emerald-700">Ap dung</button>
                        <a href="{{ $clearFilterUrl }}" class="rounded-xl border border-slate-200 px-3 py-2 text-center text-sm font-semibold text-slate-700 hover:border-slate-300">Xoa bo loc</a>
                    </div>
                </form>
            </aside>

            <section>
                <div class="mb-4 rounded-2xl border border-slate-200 bg-white p-3 shadow-sm sm:flex sm:items-center sm:justify-between">
                    <p class="text-sm text-slate-600">Tim thay <strong class="text-slate-900">{{ $books->total() }}</strong> sach</p>

                    <div class="mt-3 flex flex-wrap items-center gap-2 sm:mt-0">
                        <form method="GET" action="{{ route('catalog.categories') }}" class="flex items-center gap-2">
                            @if ($parentSlug)
                                <input type="hidden" name="parent" value="{{ $parentSlug }}">
                            @endif
                            @if ($childSlug)
                                <input type="hidden" name="child" value="{{ $childSlug }}">
                            @endif
                            <input type="hidden" name="q" value="{{ $keyword }}">
                            <input type="hidden" name="view" value="{{ $viewMode }}">
                            <input type="hidden" name="min_price" value="{{ $minPrice }}">
                            <input type="hidden" name="max_price" value="{{ $maxPrice }}">
                            <input type="hidden" name="language" value="{{ $languageFilter }}">
                            <input type="hidden" name="stock" value="{{ $stockFilter }}">
                            <select name="sort" onchange="this.form.submit()" class="rounded-xl border border-slate-200 px-3 py-2 text-sm text-slate-700">
                                <option value="newest" {{ $sortBy === 'newest' ? 'selected' : '' }}>Moi nhat</option>
                                <option value="price_asc" {{ $sortBy === 'price_asc' ? 'selected' : '' }}>Gia tang dan</option>
                                <option value="price_desc" {{ $sortBy === 'price_desc' ? 'selected' : '' }}>Gia giam dan</option>
                                <option value="title_asc" {{ $sortBy === 'title_asc' ? 'selected' : '' }}>Ten A-Z</option>
                            </select>
                        </form>

                        @php
                            $gridQuery = array_filter(array_merge($queryBase, [
                                'parent' => $parentSlug,
                                'child' => $childSlug,
                                'view' => 'grid',
                                'page' => null,
                            ]), fn ($value) => $value !== null && $value !== '');

                            $listQuery = array_filter(array_merge($queryBase, [
                                'parent' => $parentSlug,
                                'child' => $childSlug,
                                'view' => 'list',
                                'page' => null,
                            ]), fn ($value) => $value !== null && $value !== '');
                        @endphp
                        <a href="{{ route('catalog.categories', $gridQuery) }}" class="rounded-xl border px-3 py-2 text-xs font-semibold {{ $viewMode === 'grid' ? 'border-emerald-600 bg-emerald-600 text-white' : 'border-slate-200 text-slate-700 hover:border-slate-300' }}">Grid</a>
                        <a href="{{ route('catalog.categories', $listQuery) }}" class="rounded-xl border px-3 py-2 text-xs font-semibold {{ $viewMode === 'list' ? 'border-emerald-600 bg-emerald-600 text-white' : 'border-slate-200 text-slate-700 hover:border-slate-300' }}">List</a>
                    </div>
                </div>

                @if ($books->count() === 0)
                    <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-8 text-center shadow-sm">
                        <h3 class="text-lg font-bold text-slate-900">Khong tim thay sach phu hop</h3>
                        <p class="mt-2 text-sm text-slate-500">Hay thu noi rong bo loc hoac tim voi tu khoa khac.</p>
                        <a href="{{ $clearFilterUrl }}" class="mt-4 inline-flex rounded-xl bg-slate-900 px-4 py-2 text-sm font-semibold text-white transition hover:bg-emerald-700">Xoa bo loc</a>
                    </div>
                @else
                    @if ($viewMode === 'list')
                        <div class="space-y-3">
                            @foreach ($books as $book)
                                @php
                                    $cover = null;
                                    if (! empty($book->cover_image)) {
                                        $cover = str_starts_with($book->cover_image, 'http://') || str_starts_with($book->cover_image, 'https://')
                                            ? $book->cover_image
                                            : asset('storage/'.$book->cover_image);
                                    }
                                @endphp
                                <article class="grid gap-4 rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:grid-cols-[140px_1fr]">
                                    <a href="{{ route('catalog.book', $book->slug) }}" class="block h-36 overflow-hidden rounded-xl bg-slate-100">
                                        @if ($cover)
                                            <img src="{{ $cover }}" alt="{{ $book->title }}" class="h-full w-full object-cover">
                                        @else
                                            <div class="flex h-full items-center justify-center bg-gradient-to-br from-orange-100 to-amber-100 text-xs font-semibold text-slate-500">Khong co anh bia</div>
                                        @endif
                                    </a>
                                    <div class="space-y-2">
                                        <div class="flex flex-wrap items-center justify-between gap-2">
                                            <a href="{{ route('catalog.book', $book->slug) }}" class="text-lg font-black text-slate-900 hover:text-emerald-700">{{ $book->title }}</a>
                                            <span class="rounded-full px-2 py-1 text-xs font-semibold {{ $book->stock_quantity > 0 ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">{{ $book->stock_quantity > 0 ? 'Con hang' : 'Tam het' }}</span>
                                        </div>
                                        <p class="text-sm text-slate-600">{{ $book->authors->pluck('name')->join(', ') ?: 'Dang cap nhat tac gia' }}</p>
                                        <p class="line-clamp-2 text-sm text-slate-500">{{ $book->description ?: 'Chua co mo ta ngan cho dau sach nay.' }}</p>
                                        <div class="flex items-center justify-between">
                                            <div>
                                                <p class="text-xl font-black text-emerald-700">{{ number_format((float) ($book->discount_price ?? $book->price), 0, ',', '.') }}đ</p>
                                                @if ($book->discount_price)
                                                    <p class="text-xs text-slate-400 line-through">{{ number_format((float) $book->price, 0, ',', '.') }}đ</p>
                                                @endif
                                            </div>
                                            <a href="{{ route('catalog.book', $book->slug) }}" class="rounded-lg bg-slate-900 px-3 py-2 text-xs font-semibold text-white transition hover:bg-emerald-700">Xem chi tiet</a>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    @else
                        <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                            @foreach ($books as $book)
                                @php
                                    $cover = null;
                                    if (! empty($book->cover_image)) {
                                        $cover = str_starts_with($book->cover_image, 'http://') || str_starts_with($book->cover_image, 'https://')
                                            ? $book->cover_image
                                            : asset('storage/'.$book->cover_image);
                                    }
                                @endphp
                                <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                                    <a href="{{ route('catalog.book', $book->slug) }}" class="block h-52 overflow-hidden bg-slate-100">
                                        @if ($cover)
                                            <img src="{{ $cover }}" alt="{{ $book->title }}" class="h-full w-full object-cover">
                                        @else
                                            <div class="flex h-full items-center justify-center bg-gradient-to-br from-orange-100 to-amber-100 text-sm font-semibold text-slate-500">Khong co anh bia</div>
                                        @endif
                                    </a>

                                    <div class="space-y-2 p-4">
                                        <div class="flex items-center justify-between gap-2">
                                            <a href="{{ route('catalog.book', $book->slug) }}" class="line-clamp-2 text-base font-bold text-slate-900 hover:text-emerald-700">{{ $book->title }}</a>
                                            <span class="rounded-full px-2 py-0.5 text-[10px] font-semibold {{ $book->stock_quantity > 0 ? 'bg-emerald-100 text-emerald-700' : 'bg-amber-100 text-amber-700' }}">{{ $book->stock_quantity > 0 ? 'Con' : 'Het' }}</span>
                                        </div>
                                        <p class="line-clamp-1 text-sm text-slate-500">{{ $book->authors->pluck('name')->join(', ') ?: 'Dang cap nhat tac gia' }}</p>
                                        <div class="flex items-end justify-between">
                                            <div>
                                                <p class="text-lg font-black text-emerald-700">{{ number_format((float) ($book->discount_price ?? $book->price), 0, ',', '.') }}đ</p>
                                                @if ($book->discount_price)
                                                    <p class="text-xs text-slate-400 line-through">{{ number_format((float) $book->price, 0, ',', '.') }}đ</p>
                                                @endif
                                            </div>
                                            <a href="{{ route('catalog.book', $book->slug) }}" class="rounded-lg bg-slate-900 px-3 py-1.5 text-xs font-semibold text-white hover:bg-emerald-700">Xem chi tiet</a>
                                        </div>
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    @endif
                @endif

                <div class="mt-6">
                    {{ $books->links() }}
                </div>
            </section>
        </section>
    </main>

    <script>
        (function () {
            const minRange = document.getElementById('minRange');
            const maxRange = document.getElementById('maxRange');
            const minLabel = document.getElementById('minRangeLabel');
            const maxLabel = document.getElementById('maxRangeLabel');

            if (!minRange || !maxRange || !minLabel || !maxLabel) {
                return;
            }

            const formatVnd = (value) => Number(value).toLocaleString('vi-VN') + 'đ';

            const sync = () => {
                let minVal = parseInt(minRange.value, 10);
                let maxVal = parseInt(maxRange.value, 10);

                if (minVal > maxVal) {
                    if (document.activeElement === minRange) {
                        maxVal = minVal;
                        maxRange.value = String(maxVal);
                    } else {
                        minVal = maxVal;
                        minRange.value = String(minVal);
                    }
                }

                minLabel.textContent = formatVnd(minVal);
                maxLabel.textContent = formatVnd(maxVal);
            };

            minRange.addEventListener('input', sync);
            maxRange.addEventListener('input', sync);
            sync();
        })();
    </script>
</body>
</html>
