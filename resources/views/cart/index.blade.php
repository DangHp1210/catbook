<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Catbook | Giỏ hàng</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="cb-site text-slate-800">
    <x-navbar />

    <main class="cb-page">
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-3xl font-black text-slate-900">Giỏ hàng của bạn</h1>
            <a href="{{ route('catalog.categories') }}" class="text-sm font-semibold text-orange-600 hover:text-orange-700">Tiếp tục mua sách</a>
        </div>

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

        @if ($items->isEmpty())
            <div class="rounded-2xl border border-dashed border-slate-300 bg-white p-8 text-center text-slate-500">
                <p class="text-lg font-semibold text-slate-700">Giỏ hàng đang trống.</p>
                <p class="mt-2 text-sm">Hãy thêm vài cuốn sách bạn yêu thích để tiếp tục.</p>
            </div>
        @else
            <section class="grid gap-6 lg:grid-cols-[1fr_320px]">
                <div class="space-y-4">
                    @foreach ($items as $item)
                        <article class="rounded-2xl border border-slate-200 bg-white p-4 shadow-sm sm:p-5">
                            <div class="flex gap-4">
                                <a href="{{ route('catalog.book', $item->book->slug) }}" class="h-28 w-20 shrink-0 overflow-hidden rounded-lg bg-slate-100">
                                    @php
                                        $cover = null;
                                        if (! empty($item->book->cover_image)) {
                                            $cover = str_starts_with($item->book->cover_image, 'http://') || str_starts_with($item->book->cover_image, 'https://')
                                                ? $item->book->cover_image
                                                : asset('storage/'.$item->book->cover_image);
                                        }
                                    @endphp
                                    @if ($cover)
                                        <img src="{{ $cover }}" alt="{{ $item->book->title }}" class="h-full w-full object-cover">
                                    @else
                                        <div class="flex h-full items-center justify-center text-xs text-slate-500">No Cover</div>
                                    @endif
                                </a>

                                <div class="flex min-w-0 flex-1 flex-col justify-between gap-2">
                                    <div>
                                        <a href="{{ route('catalog.book', $item->book->slug) }}" class="line-clamp-2 text-base font-bold text-slate-900 hover:text-orange-700">{{ $item->book->title }}</a>
                                        <p class="mt-1 text-sm text-slate-500">{{ $item->book->authors->pluck('name')->first() ?: 'Đang cập nhật tác giả' }}</p>
                                    </div>

                                    <div class="flex flex-wrap items-center justify-between gap-2">
                                        <p class="text-sm text-slate-600">Số lượng: <span class="font-semibold">{{ $item->quantity }}</span></p>
                                        <p class="text-lg font-black text-orange-600">
                                            {{ number_format((float) $item->unit_price * $item->quantity, 0, ',', '.') }}đ
                                        </p>
                                    </div>

                                    <form method="POST" action="{{ route('cart.items.destroy', $item->id) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="rounded-lg border border-rose-200 bg-rose-50 px-3 py-1.5 text-xs font-semibold text-rose-600 transition hover:bg-rose-100">
                                            Xóa khỏi giỏ
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>

                <aside class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
                    <h2 class="text-lg font-black text-slate-900">Tóm tắt đơn hàng</h2>
                    <div class="mt-4 space-y-2 text-sm text-slate-600">
                        <div class="flex items-center justify-between">
                            <span>Tạm tính</span>
                            <span class="font-semibold text-slate-900">{{ number_format($subtotal, 0, ',', '.') }}đ</span>
                        </div>
                        <div class="flex items-center justify-between">
                            <span>Phí vận chuyển</span>
                            <span class="font-semibold text-slate-900">Tính ở bước thanh toán</span>
                        </div>
                        <div class="border-t border-slate-200 pt-3">
                            <div class="flex items-center justify-between text-base">
                                <span class="font-semibold text-slate-900">Tổng dự kiến</span>
                                <span class="text-xl font-black text-orange-600">{{ number_format($subtotal, 0, ',', '.') }}đ</span>
                            </div>
                        </div>
                    </div>

                    <a href="{{ route('checkout.show') }}" class="mt-5 block w-full rounded-xl bg-orange-500 px-4 py-3 text-center text-sm font-semibold text-red transition hover:bg-orange-600">
                        Tiến hành thanh toán
                    </a>
                    <p class="mt-2 text-xs text-slate-500">Miễn phí vận chuyển với đơn từ 299.000đ.</p>
                </aside>
            </section>
        @endif
    </main>
</body>
</html>
