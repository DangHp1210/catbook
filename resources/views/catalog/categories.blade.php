<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Catbook | Danh muc sach</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-[#f7f8fc] text-slate-800">
    <x-navbar />

    <main class="mx-auto max-w-6xl px-4 pb-12 pt-6 sm:px-6 lg:px-8">
        <div class="mb-6 rounded-2xl border border-slate-200 bg-white px-5 py-4 shadow-sm">
            <p class="text-xs uppercase tracking-[0.16em] text-slate-500">Danh muc</p>
            <h1 class="mt-1 text-2xl font-black text-slate-900">{{ $heading }}</h1>
            @if (! empty($keyword))
                <p class="mt-2 text-sm text-slate-600">
                    Ket qua cho tu khoa:
                    <span class="font-semibold text-orange-600">"{{ $keyword }}"</span>
                </p>
            @endif
        </div>

        <section class="grid gap-6 lg:grid-cols-[280px_1fr]">
            <aside class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm">
                <a href="{{ route('catalog.categories') }}" class="mb-2 block rounded-xl px-3 py-2 text-sm font-semibold transition {{ $selectedCategory ? 'text-slate-700 hover:bg-slate-100' : 'bg-orange-50 text-orange-700' }}">
                    Tat ca sach
                </a>

                <div class="space-y-1">
                    @forelse ($categories as $category)
                        <a href="{{ route('catalog.category', $category->slug) }}" class="flex items-center justify-between rounded-xl px-3 py-2 text-sm transition {{ $selectedCategory && $selectedCategory->id === $category->id ? 'bg-orange-50 text-orange-700' : 'text-slate-700 hover:bg-slate-100' }}">
                            <span>{{ $category->name }}</span>
                            <span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs text-slate-500">{{ $category->books_count }}</span>
                        </a>
                    @empty
                        <p class="rounded-xl bg-slate-50 px-3 py-2 text-sm text-slate-500">Chua co danh muc.</p>
                    @endforelse
                </div>
            </aside>

            <section>
                <div class="mb-4 flex items-center justify-between">
                    <p class="text-sm text-slate-600">Tong {{ $books->total() }} dau sach</p>
                    @if ($selectedCategory || ! empty($keyword))
                        <a href="{{ route('catalog.categories') }}" class="text-sm font-semibold text-orange-600 hover:text-orange-700">Bo tat ca bo loc</a>
                    @endif
                </div>

                <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
                    @forelse ($books as $book)
                        <article class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm transition hover:-translate-y-1 hover:shadow-lg">
                            <a href="{{ route('catalog.book', $book->slug) }}" class="block h-52 overflow-hidden bg-slate-100">
                                @php
                                    $cover = null;
                                    if (! empty($book->cover_image)) {
                                        $cover = str_starts_with($book->cover_image, 'http://') || str_starts_with($book->cover_image, 'https://')
                                            ? $book->cover_image
                                            : asset('storage/'.$book->cover_image);
                                    }
                                @endphp
                                @if ($cover)
                                    <img src="{{ $cover }}" alt="{{ $book->title }}" class="h-full w-full object-cover">
                                @else
                                    <div class="flex h-full items-center justify-center bg-gradient-to-br from-orange-100 to-amber-100 text-sm font-semibold text-slate-500">Khong co anh bia</div>
                                @endif
                            </a>

                            <div class="space-y-2 p-4">
                                <a href="{{ route('catalog.book', $book->slug) }}" class="line-clamp-2 text-base font-bold text-slate-900 hover:text-orange-700">{{ $book->title }}</a>
                                <p class="line-clamp-1 text-sm text-slate-500">{{ $book->authors->pluck('name')->join(', ') ?: 'Dang cap nhat tac gia' }}</p>
                                <div class="flex items-end justify-between">
                                    <div>
                                        <p class="text-lg font-black text-orange-600">{{ number_format((float) ($book->discount_price ?? $book->price), 0, ',', '.') }}đ</p>
                                        @if ($book->discount_price)
                                            <p class="text-xs text-slate-400 line-through">{{ number_format((float) $book->price, 0, ',', '.') }}đ</p>
                                        @endif
                                    </div>
                                    <a href="{{ route('catalog.book', $book->slug) }}" class="rounded-lg bg-slate-900 px-3 py-1.5 text-xs font-semibold text-white hover:bg-slate-800">Xem chi tiet</a>
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-6 text-sm text-slate-500 sm:col-span-2 xl:col-span-3">
                            Khong co sach phu hop voi bo loc hien tai.
                        </div>
                    @endforelse
                </div>

                <div class="mt-6">
                    {{ $books->links() }}
                </div>
            </section>
        </section>
    </main>
</body>
</html>
