<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Catbook | {{ $book->title }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="cb-site text-slate-800">
    <x-navbar />

    <main class="cb-page">
        @if (session('success'))
            <div class="mb-4 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700">
                {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="mb-4 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-700">
                {{ session('error') }}
            </div>
        @endif

        <div class="mb-4 text-sm text-slate-500">
            <a href="{{ route('home') }}" class="hover:text-orange-600">Trang chu</a>
            <span class="mx-1">/</span>
            <a href="{{ route('catalog.categories') }}" class="hover:text-orange-600">Danh muc</a>
            <span class="mx-1">/</span>
            <span class="text-slate-700">{{ $book->title }}</span>
        </div>

        <section class="grid gap-6 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm lg:grid-cols-[340px_1fr]">
            <div class="overflow-hidden rounded-xl border border-slate-200 bg-slate-100">
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
                    <div class="flex h-[460px] items-center justify-center bg-gradient-to-br from-orange-100 to-amber-100 text-sm font-semibold text-slate-500">Khong co anh bia</div>
                @endif
            </div>

            <article>
                <h1 class="text-3xl font-black text-slate-900">{{ $book->title }}</h1>
                <p class="mt-2 text-sm text-slate-600">Tac gia: {{ $book->authors->pluck('name')->join(', ') ?: 'Dang cap nhat' }}</p>
                <p class="mt-1 text-sm text-slate-600">Danh muc: {{ $book->categories->pluck('name')->join(', ') ?: 'Dang cap nhat' }}</p>
                <p class="mt-1 text-sm text-slate-600">Nha xuat ban: {{ $book->publisher?->name ?? 'Dang cap nhat' }}</p>

                <div class="mt-5 rounded-xl border border-orange-100 bg-orange-50 p-4">
                    <p class="text-sm text-slate-500">Gia ban</p>
                    <div class="mt-1 flex items-end gap-3">
                        <p class="text-3xl font-black text-orange-600">{{ number_format((float) ($book->discount_price ?? $book->price), 0, ',', '.') }}đ</p>
                        @if ($book->discount_price)
                            <p class="text-sm text-slate-400 line-through">{{ number_format((float) $book->price, 0, ',', '.') }}đ</p>
                        @endif
                    </div>
                    <p class="mt-2 text-sm {{ $book->stock_quantity > 0 ? 'text-emerald-600' : 'text-rose-600' }}">
                        {{ $book->stock_quantity > 0 ? 'Con hang: '.$book->stock_quantity.' quyen' : 'Tam het hang' }}
                    </p>
                </div>

                <div class="mt-5 flex flex-wrap gap-3">
                    @auth
                        <form method="POST" action="{{ route('cart.store', $book->slug) }}" class="flex flex-wrap items-center gap-2">
                            @csrf
                            <input
                                type="number"
                                name="quantity"
                                min="1"
                                max="{{ max(1, $book->stock_quantity) }}"
                                value="1"
                                class="w-20 rounded-xl border border-slate-300 px-3 py-2 text-sm outline-none focus:border-orange-400"
                            >
                            <button type="submit" class="min-w-[180px] rounded-xl bg-orange-600 px-6 py-3 text-sm font-bold text-red shadow-md shadow-orange-200 transition hover:bg-orange-700 hover:shadow-lg focus:outline-none focus:ring-2 focus:ring-orange-400 focus:ring-offset-2">
                                Thêm vào giỏ hàng
                            </button>
                        </form>
                        <a href="{{ route('cart.index') }}" class="rounded-xl border border-slate-300 bg-white px-5 py-2.5 text-sm font-semibold text-slate-700 hover:border-orange-300 hover:text-orange-700">Xem gio hang</a>
                    @else
                        <a href="{{ route('login') }}" class="rounded-xl bg-orange-500 px-5 py-2.5 text-sm font-semibold text-white hover:bg-orange-600">Dang nhap de mua</a>
                    @endauth
                </div>

                <div class="mt-6 grid gap-3 rounded-xl border border-slate-200 bg-slate-50 p-4 text-sm text-slate-600 sm:grid-cols-2">
                    <p>ISBN: {{ $book->isbn ?? 'N/A' }}</p>
                    <p>So trang: {{ $book->page_count ?? 'Dang cap nhat' }}</p>
                    <p>Ngon ngu: {{ $book->language ?? 'Dang cap nhat' }}</p>
                    <p>Nam xuat ban: {{ $book->publication_year ?? 'Dang cap nhat' }}</p>
                </div>

                <div class="mt-6">
                    <h2 class="text-lg font-bold text-slate-900">Mo ta sach</h2>
                    <p class="mt-2 whitespace-pre-line text-sm leading-7 text-slate-600">{{ $book->description ?: 'Chua co mo ta chi tiet cho san pham nay.' }}</p>
                </div>

                <div class="mt-6">
                    <h2 class="text-lg font-bold text-slate-900">Danh gia</h2>
                    <p class="mt-2 text-sm text-slate-600">
                        Diem trung binh: {{ number_format((float) ($book->reviews_avg_rating ?? 0), 1) }}/5
                        ({{ $book->reviews_count }} danh gia)
                    </p>
                </div>
            </article>
        </section>

        <section class="mt-8 rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <div class="mb-4 flex items-center justify-between">
                <h2 class="text-xl font-black text-slate-900">Sach lien quan</h2>
                <a href="{{ route('catalog.categories') }}" class="text-sm font-semibold text-orange-600 hover:text-orange-700">Xem them</a>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                @forelse ($relatedBooks as $related)
                    <article class="overflow-hidden rounded-xl border border-slate-200 bg-white transition hover:-translate-y-1 hover:shadow-md">
                        <a href="{{ route('catalog.book', $related->slug) }}" class="block h-44 overflow-hidden bg-slate-100">
                            @php
                                $relatedCover = null;
                                if (! empty($related->cover_image)) {
                                    $relatedCover = str_starts_with($related->cover_image, 'http://') || str_starts_with($related->cover_image, 'https://')
                                        ? $related->cover_image
                                        : asset('storage/'.$related->cover_image);
                                }
                            @endphp
                            @if ($relatedCover)
                                <img src="{{ $relatedCover }}" alt="{{ $related->title }}" class="h-full w-full object-cover">
                            @else
                                <div class="flex h-full items-center justify-center bg-gradient-to-br from-orange-100 to-amber-100 text-xs font-semibold text-slate-500">No Cover</div>
                            @endif
                        </a>
                        <div class="p-3">
                            <a href="{{ route('catalog.book', $related->slug) }}" class="line-clamp-2 text-sm font-bold text-slate-900 hover:text-orange-700">{{ $related->title }}</a>
                            <p class="mt-1 text-xs text-slate-500">{{ $related->authors->pluck('name')->first() ?: 'Dang cap nhat tac gia' }}</p>
                            <p class="mt-2 text-sm font-bold text-orange-600">{{ number_format((float) ($related->discount_price ?? $related->price), 0, ',', '.') }}đ</p>
                        </div>
                    </article>
                @empty
                    <p class="text-sm text-slate-500 sm:col-span-2 lg:col-span-4">Chua co sach lien quan.</p>
                @endforelse
            </div>
        </section>
    </main>
</body>
</html>
