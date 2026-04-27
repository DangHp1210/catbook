<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Catbook | Mua sách trực tuyến thông minh</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="catbook-landing min-h-screen">
    <div class="border-b border-orange-200 bg-gradient-to-r from-orange-500 via-orange-400 to-amber-400 text-white">
        <div class="catbook-container flex flex-wrap items-center justify-between gap-2 py-2 text-xs font-semibold tracking-wide">
            <p>Freeship toàn quốc cho đơn từ 299.000đ</p>
            <p>Tặng mã NEWBOOK10 cho khách hàng mới</p>
        </div>
    </div>

    <x-navbar />

    <main class="catbook-container space-y-8 pb-14 pt-6">
        <section class="cb-card p-4 sm:p-5">
            <div class="flex flex-wrap items-end justify-between gap-3">
                <div>
                    <p class="cb-kicker">Tìm kiếm sách</p>
                    <h2 class="mt-2 text-xl font-extrabold text-slate-900">Tìm nhanh theo tên sách, tác giả hoặc ISBN</h2>
                </div>
            </div>

            <form method="GET" action="{{ route('catalog.categories') }}" class="mt-4 grid gap-2 sm:grid-cols-[1fr_auto]">
                <label for="home-search" class="sr-only">Tìm sách</label>
                <input
                    id="home-search"
                    name="q"
                    type="text"
                    value="{{ request('q') }}"
                    placeholder="Ví dụ: Clean Code, Martin Fowler, 978..."
                    class="w-full rounded-xl border border-slate-300 bg-white px-4 py-2.5 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-orange-400"
                >
                <button type="submit" class="rounded-xl bg-orange-500 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-orange-600">
                    Tìm kiếm
                </button>
            </form>
        </section>

        <section class="cb-hero">
            <article class="cb-hero-panel">
                <p class="cb-kicker">
                    <span class="cb-pulse inline-block h-2 w-2 rounded-full bg-orange-500"></span> Catbook 2026
                </p>
                <h1 class="cb-hero-title mt-4">
                    Nhà sách trực tuyến hiện đại cho người học tập và phát triển mỗi ngày
                </h1>
                <p class="mt-4 max-w-2xl text-base leading-8 text-slate-600">
                    Catbook giúp bạn tìm nhanh cuốn sách phù hợp với mục tiêu cá nhân, từ công nghệ, kinh doanh đến văn học.
                    Kết hợp kho sách thực tế và gợi ý AI để quyết định mua sách dễ hơn.
                </p>

                <div class="mt-6 flex flex-wrap gap-3">
                    @auth
                        <a href="{{ route('catalog.categories') }}" class="cb-button-primary">Khám phá sách</a>
                    @endauth
                </div>

                <div class="cb-hero-grid mt-6">
                    <article class="cb-stat">
                        <p class="text-xs uppercase tracking-[0.16em] text-slate-500">Đầu sách</p>
                        <p class="cb-stat-value">{{ number_format($stats['books']) }}</p>
                        <p class="text-xs text-slate-500">Sẵn sàng giao</p>
                    </article>
                    <article class="cb-stat">
                        <p class="text-xs uppercase tracking-[0.16em] text-slate-500">Tác giả</p>
                        <p class="cb-stat-value">{{ number_format($stats['authors']) }}</p>
                        <p class="text-xs text-slate-500">Trong hệ thống</p>
                    </article>
                    <article class="cb-stat">
                        <p class="text-xs uppercase tracking-[0.16em] text-slate-500">Danh mục</p>
                        <p class="cb-stat-value">{{ number_format($stats['categories']) }}</p>
                        <p class="text-xs text-slate-500">Dễ tìm kiếm</p>
                    </article>
                </div>
            </article>

            <aside class="cb-shell">
                <h2 class="text-lg font-extrabold text-slate-900">Danh mục nổi bật</h2>
                <p class="mt-1 text-sm text-slate-500">Chọn nhanh theo nhu cầu học tập và phát triển.</p>
                <div class="mt-4 grid gap-2 sm:grid-cols-2">
                    @forelse ($topCategories as $category)
                        <a href="{{ route('catalog.category', $category->slug) }}" class="flex items-center justify-between rounded-xl border border-slate-200 bg-white px-3 py-2 text-sm font-medium text-slate-700 transition hover:border-orange-300 hover:bg-orange-50">
                            <span>{{ $category->name }}</span>
                            <span class="rounded-full bg-slate-100 px-2 py-0.5 text-xs text-slate-500">{{ $category->books_count }}</span>
                        </a>
                    @empty
                        <p class="rounded-xl border border-dashed border-slate-300 bg-white px-3 py-3 text-sm text-slate-500 sm:col-span-2">Chưa có danh mục để hiển thị.</p>
                    @endforelse
                </div>

                <div class="mt-5 rounded-xl border border-cyan-200 bg-cyan-50 px-4 py-3 text-sm text-cyan-900">
                    <p class="font-bold">Gợi ý từ Catbook AI</p>
                    <p class="mt-1">Hỏi theo mục tiêu: “Mình cần sách Laravel thực chiến cho đồ án trong 2 tháng”.</p>
                </div>
            </aside>
        </section>

        <section class="cb-card p-5 sm:p-6">
            <div class="mb-4 flex items-end justify-between gap-3">
                <div>
                    <p class="cb-kicker">Best seller</p>
                    <h2 class="cb-section-title mt-2">Sách nổi bật tuần này</h2>
                </div>
                <a href="{{ route('catalog.categories') }}" class="text-sm font-bold text-orange-600 hover:text-orange-700">Xem toàn bộ</a>
            </div>

            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                @forelse ($featuredBooks as $book)
                    <article class="cb-product-card">
                        <a href="{{ route('catalog.book', $book->slug) }}" class="cb-product-thumb">
                            @php
                                $cover = null;
                                if (! empty($book->cover_image)) {
                                    $cover = str_starts_with($book->cover_image, 'http://') || str_starts_with($book->cover_image, 'https://')
                                        ? $book->cover_image
                                        : asset('storage/'.$book->cover_image);
                                }
                            @endphp
                            @if ($cover)
                                <img src="{{ $cover }}" alt="{{ $book->title }}">
                            @else
                                <div class="flex h-full items-center justify-center text-sm font-semibold text-slate-500">Không có ảnh bìa</div>
                            @endif
                        </a>

                        <div class="space-y-2 p-4">
                            <a href="{{ route('catalog.book', $book->slug) }}" class="line-clamp-2 min-h-12 text-base font-extrabold text-slate-900 hover:text-orange-700">{{ $book->title }}</a>
                            <p class="line-clamp-1 text-sm text-slate-500">{{ $book->authors->pluck('name')->take(2)->join(', ') ?: 'Đang cập nhật tác giả' }}</p>
                            <div class="flex items-end justify-between">
                                <div>
                                    <p class="text-lg font-black text-orange-600">{{ number_format((float) ($book->discount_price ?? $book->price), 0, ',', '.') }}đ</p>
                                    @if ($book->discount_price)
                                        <p class="text-xs text-slate-400 line-through">{{ number_format((float) $book->price, 0, ',', '.') }}đ</p>
                                    @endif
                                </div>
                                <a href="{{ route('catalog.book', $book->slug) }}" class="rounded-lg bg-orange-50 px-2 py-1 text-xs font-bold text-orange-700 hover:bg-orange-100">Chi tiết</a>
                            </div>
                        </div>
                    </article>
                @empty
                    <p class="rounded-xl border border-dashed border-slate-300 bg-slate-50 p-5 text-sm text-slate-500 sm:col-span-2 lg:col-span-3 xl:col-span-4">Chưa có dữ liệu sách nổi bật. Hãy thêm dữ liệu bảng books để hiển thị sản phẩm.</p>
                @endforelse
            </div>
        </section>

        <section class="grid gap-6 lg:grid-cols-[1.2fr_0.8fr]">
            <article class="cb-card p-5 sm:p-6">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="cb-section-title">Sách mới cập nhật</h2>
                    <span class="rounded-full bg-cyan-100 px-3 py-1 text-xs font-bold uppercase tracking-wide text-cyan-700">New</span>
                </div>

                <div class="space-y-3">
                    @forelse ($newArrivals as $book)
                        <div class="flex items-center justify-between rounded-xl border border-slate-200 bg-white px-4 py-3 transition hover:border-orange-300 hover:bg-orange-50">
                            <div>
                                <a href="{{ route('catalog.book', $book->slug) }}" class="font-bold text-slate-900 hover:text-orange-700">{{ $book->title }}</a>
                                <p class="text-xs text-slate-500">{{ $book->authors->pluck('name')->first() ?: 'Đang cập nhật tác giả' }}</p>
                            </div>
                            <p class="text-sm font-black text-orange-600">{{ number_format((float) ($book->discount_price ?? $book->price), 0, ',', '.') }}đ</p>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">Chưa có dữ liệu sách mới.</p>
                    @endforelse
                </div>
            </article>

            <article class="cb-card p-5 sm:p-6">
                <div class="mb-4 flex items-center justify-between">
                    <h2 class="cb-section-title">Tác giả được quan tâm</h2>
                    <span class="rounded-full bg-rose-100 px-3 py-1 text-xs font-bold uppercase tracking-wide text-rose-700">Top</span>
                </div>

                <div class="space-y-3">
                    @forelse ($topAuthors as $author)
                        <div class="rounded-xl border border-slate-200 bg-white px-4 py-3 transition hover:border-orange-300 hover:bg-orange-50">
                            <p class="font-bold text-slate-900">{{ $author->name }}</p>
                            <p class="text-xs text-slate-500">{{ $author->books_count }} đầu sách</p>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500">Chưa có dữ liệu tác giả.</p>
                    @endforelse
                </div>
            </article>
        </section>
    </main>
</body>
</html>
