<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>CatBook | Mua sách trực tuyến thông minh</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <!-- Tailwind CSS fallback (Xóa dòng này nếu Vite của bạn đã config sẵn Tailwind) -->
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-white font-sans text-slate-800">

    <!-- Bạn có thể tách phần <nav> này thành file <x-navbar /> của bạn -->
    <nav class="sticky top-0 z-40 bg-white/80 backdrop-blur-md border-b border-emerald-100">
        <div class="max-w-7xl mx-auto px-4 h-16 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <div class="bg-emerald-600 p-1.5 rounded-lg">
                    <svg class="text-white w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                </div>
                <span class="text-2xl font-bold text-emerald-700 tracking-tight">CatBook</span>
            </div>

            <div class="hidden md:flex items-center flex-1 max-w-md mx-8">
                <form method="GET" action="{{ route('catalog.categories') }}" class="relative w-full">
                    <input 
                        name="q"
                        type="text" 
                        value="{{ request('q') }}"
                        placeholder="Tìm sách, tác giả hoặc ISBN..." 
                        class="w-full bg-emerald-50 border-none rounded-full py-2 pl-10 pr-4 focus:ring-2 focus:ring-emerald-500 transition-all outline-none"
                    />
                    <button type="submit" class="absolute left-3 top-2.5 text-emerald-500 w-5 h-5">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                    </button>
                </form>
            </div>

            <div class="flex items-center gap-5">
                <div class="hidden md:flex gap-6 font-medium text-slate-600">
                    <a href="#" class="hover:text-emerald-600 transition-colors">Trang chủ</a>
                    <a href="{{ route('catalog.categories') }}" class="hover:text-emerald-600 transition-colors">Thể loại</a>
                </div>
                <div class="flex items-center gap-3">
                    <button class="p-2 text-slate-600 hover:bg-emerald-50 rounded-full transition-colors relative">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        <span class="absolute top-0 right-0 bg-emerald-600 text-white text-[10px] w-4 h-4 rounded-full flex items-center justify-center">0</span>
                    </button>
                    @auth
                        <!-- Menu Dropdown khi đã đăng nhập -->
                        <div class="relative group">
                            <button class="flex items-center gap-2 p-1 pr-2 rounded-full border border-emerald-100 hover:bg-emerald-50 transition-colors">
                                <div class="w-8 h-8 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 font-bold">
                                    {{ substr(auth()->user()->name, 0, 1) }}
                                </div>
                                <span class="text-sm font-medium text-slate-700 hidden sm:block">{{ auth()->user()->name }}</span>
                                <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </button>

                            <!-- Dropdown Content (hiện khi hover) -->
                            <div class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-slate-100 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200 z-50 pt-2">
                                <div class="py-2 bg-white rounded-xl border border-emerald-50">
                                    <a href="#" class="block px-4 py-2 text-sm text-slate-700 hover:bg-emerald-50 hover:text-emerald-700">Trang cá nhân</a>
                                    <a href="#" class="block px-4 py-2 text-sm text-slate-700 hover:bg-emerald-50 hover:text-emerald-700">Đơn hàng của tôi</a>
                                    <hr class="my-1 border-slate-100">
                                    <!-- Form Đăng xuất chuẩn Laravel -->
                                    <form method="POST" action="{{ route('logout') }}">
                                        @csrf
                                        <button type="submit" class="w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 hover:text-red-700 transition-colors">
                                            Đăng xuất
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- Nút Đăng nhập / Đăng ký khi chưa đăng nhập -->
                        <div class="flex items-center gap-1 pl-2 border-l border-emerald-100">
                            <a href="{{ route('login') }}" class="text-sm font-medium text-slate-600 hover:text-emerald-600 transition-colors px-3 py-2 hidden sm:block">Đăng nhập</a>
                            <a href="{{ route('register') }}" class="text-sm font-medium text-white bg-emerald-600 hover:bg-emerald-700 transition-colors px-4 py-2 rounded-full shadow-sm">Đăng ký</a>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
    </nav>
    <!-- Kết thúc Navbar -->

    <main class="space-y-12 pb-14">
        <!-- Hero Section -->
        <section class="relative py-16 md:py-24 overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-full bg-emerald-50/50 -z-10 skew-y-3 origin-top-left"></div>
            <div class="max-w-7xl mx-auto px-4 grid md:grid-cols-2 gap-12 items-center">
                <div class="space-y-6">
                    <div class="inline-flex items-center gap-2 px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full text-sm font-semibold">
                        <span class="w-2 h-2 bg-emerald-500 rounded-full animate-pulse"></span>
                        <span>Catbook 2026</span>
                    </div>
                    <h1 class="text-4xl md:text-5xl lg:text-6xl font-extrabold text-slate-900 leading-tight">
                        Nhà sách trực tuyến <br/>
                        <span class="text-emerald-600">cho tương lai xanh.</span>
                    </h1>
                    <p class="text-lg text-slate-600 max-w-lg">
                        Catbook giúp bạn tìm nhanh cuốn sách phù hợp với mục tiêu cá nhân. Kết hợp kho sách thực tế và trợ lý AI để quyết định dễ dàng hơn.
                    </p>
                    
                    <div class="flex flex-wrap gap-4 pt-4">
                        @auth
                            <a href="{{ route('catalog.categories') }}" class="bg-emerald-600 text-white px-8 py-3 rounded-full font-bold hover:bg-emerald-700 transition-all shadow-lg shadow-emerald-200 flex items-center gap-2">
                                Khám phá sách
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </a>
                        @else
                            <a href="#" class="bg-emerald-600 text-white px-8 py-3 rounded-full font-bold hover:bg-emerald-700 transition-all shadow-lg shadow-emerald-200">
                                Đăng nhập ngay
                            </a>
                        @endauth
                    </div>

                    <!-- Stats -->
                    <div class="grid grid-cols-3 gap-6 pt-6 border-t border-emerald-100 mt-6">
                        <div>
                            <p class="text-2xl font-black text-emerald-600">{{ number_format($stats['books'] ?? 0) }}</p>
                            <p class="text-xs text-slate-500 uppercase tracking-wide mt-1">Đầu sách</p>
                        </div>
                        <div>
                            <p class="text-2xl font-black text-emerald-600">{{ number_format($stats['authors'] ?? 0) }}</p>
                            <p class="text-xs text-slate-500 uppercase tracking-wide mt-1">Tác giả</p>
                        </div>
                        <div>
                            <p class="text-2xl font-black text-emerald-600">{{ number_format($stats['categories'] ?? 0) }}</p>
                            <p class="text-xs text-slate-500 uppercase tracking-wide mt-1">Danh mục</p>
                        </div>
                    </div>
                </div>

                <div class="relative hidden md:block">
                    <div class="w-full h-[450px] bg-emerald-200 rounded-3xl overflow-hidden shadow-2xl rotate-3 relative">
                        <img 
                            src="https://images.unsplash.com/photo-1512820790803-83ca734da794?auto=format&fit=crop&q=80&w=1000" 
                            alt="Book collection" 
                            class="w-full h-full object-cover"
                        />
                    </div>
                    <div class="absolute -bottom-6 -left-6 bg-white p-4 rounded-2xl shadow-xl flex items-center gap-4 animate-bounce">
                        <div class="bg-emerald-500 p-2 rounded-lg">
                            <svg class="text-white w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        </div>
                        <div>
                            <p class="text-xs text-slate-500 font-medium">Hỗ trợ bởi AI</p>
                            <p class="text-sm font-bold text-slate-800">Luôn sẵn sàng</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Categories -->
        <section class="max-w-7xl mx-auto px-4">
            <div class="flex items-center justify-between mb-8">
                <h2 class="text-2xl font-bold text-slate-900">Danh mục nổi bật</h2>
                <a href="{{ route('catalog.categories') }}" class="text-emerald-600 font-semibold flex items-center gap-1 hover:underline">
                    Xem tất cả 
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                </a>
            </div>
            
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                @forelse ($topCategories ?? [] as $category)
                    <a href="{{ route('catalog.category', $category->slug) }}" class="group bg-emerald-50 hover:bg-emerald-600 p-6 rounded-3xl text-center transition-all cursor-pointer flex flex-col items-center justify-center border border-transparent hover:border-emerald-500">
                        <h3 class="font-bold text-emerald-700 group-hover:text-white transition-colors">{{ $category->name }}</h3>
                        <p class="text-emerald-600/60 group-hover:text-white/80 text-sm mt-1 transition-colors">{{ $category->books_count }} cuốn</p>
                    </a>
                @empty
                    <div class="col-span-2 md:col-span-4 rounded-2xl border border-dashed border-slate-300 bg-slate-50 py-10 text-center text-sm text-slate-500">
                        Chưa có danh mục để hiển thị.
                    </div>
                @endforelse
            </div>
            
            <div class="mt-8 rounded-2xl border border-emerald-100 bg-emerald-50/50 p-5 flex items-start gap-4">
                <div class="bg-white p-2 rounded-full shadow-sm text-emerald-500">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                </div>
                <div>
                    <p class="font-bold text-emerald-800">Gợi ý từ Catbook AI</p>
                    <p class="mt-1 text-sm text-emerald-600">Bạn có thể mở cửa sổ Chat ở góc dưới màn hình và hỏi: “Mình cần sách Laravel thực chiến cho đồ án trong 2 tháng”.</p>
                </div>
            </div>
        </section>

        <!-- Featured Books -->
        <section class="py-12 bg-slate-50 mt-12 border-y border-slate-100">
            <div class="max-w-7xl mx-auto px-4">
                <div class="mb-10 flex items-end justify-between gap-3">
                    <div>
                        <p class="text-sm font-bold text-emerald-600 uppercase tracking-widest">Best Seller</p>
                        <h2 class="text-3xl font-extrabold text-slate-900 mt-2">Sách nổi bật tuần này</h2>
                    </div>
                    <a href="{{ route('catalog.categories') }}" class="text-sm font-bold text-emerald-600 hover:text-emerald-700">Xem toàn bộ</a>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                    @forelse ($featuredBooks ?? [] as $book)
                        @php
                            $cover = null;
                            if (! empty($book->cover_image)) {
                                $cover = str_starts_with($book->cover_image, 'http://') || str_starts_with($book->cover_image, 'https://')
                                    ? $book->cover_image
                                    : asset('storage/'.$book->cover_image);
                            }
                        @endphp
                        <article class="group bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl transition-all border border-slate-100 relative flex flex-col">
                            <div class="absolute top-3 right-3 z-10">
                                <button class="bg-white/90 p-2 rounded-full text-slate-400 hover:text-red-500 shadow-sm transition-colors">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 21.35l-1.45-1.32C5.4 15.36 2 12.28 2 8.5 2 5.42 4.42 3 7.5 3c1.74 0 3.41.81 4.5 2.09C13.09 3.81 14.76 3 16.5 3 19.58 3 22 5.42 22 8.5c0 3.78-3.4 6.86-8.55 11.54L12 21.35z"/></svg>
                                </button>
                            </div>
                            <a href="{{ route('catalog.book', $book->slug) }}" class="h-64 overflow-hidden relative block bg-slate-100">
                                @if ($cover)
                                    <img src="{{ $cover }}" alt="{{ $book->title }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                                @else
                                    <div class="flex h-full items-center justify-center text-sm font-semibold text-slate-400">Không có ảnh bìa</div>
                                @endif
                                <div class="absolute bottom-0 left-0 w-full p-4 translate-y-full group-hover:translate-y-0 transition-transform bg-gradient-to-t from-black/60 to-transparent">
                                    <span class="block w-full bg-white text-center text-emerald-600 py-2 rounded-lg font-bold text-sm shadow-md">Xem chi tiết</span>
                                </div>
                            </a>
                            <div class="p-5 flex-1 flex flex-col">
                                <div class="flex gap-1 mb-2">
                                    @for ($i = 0; $i < 5; $i++)
                                        <svg class="w-3.5 h-3.5 text-yellow-400" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path></svg>
                                    @endfor
                                </div>
                                <a href="{{ route('catalog.book', $book->slug) }}" class="font-bold text-slate-900 hover:text-emerald-600 transition-colors line-clamp-2 min-h-[40px]">{{ $book->title }}</a>
                                <p class="text-slate-500 text-sm mt-1 mb-3 flex-1 line-clamp-1">{{ $book->authors->pluck('name')->take(2)->join(', ') ?: 'Đang cập nhật' }}</p>
                                <div class="flex items-end justify-between mt-auto">
                                    <div>
                                        <span class="text-emerald-600 font-extrabold text-lg">{{ number_format((float) ($book->discount_price ?? $book->price), 0, ',', '.') }}đ</span>
                                        @if ($book->discount_price)
                                            <span class="text-xs text-slate-400 line-through block">{{ number_format((float) $book->price, 0, ',', '.') }}đ</span>
                                        @endif
                                    </div>
                                    <button class="bg-emerald-50 text-emerald-600 w-8 h-8 rounded-full flex items-center justify-center hover:bg-emerald-600 hover:text-white transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg>
                                    </button>
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="col-span-full rounded-2xl border border-dashed border-slate-300 bg-white p-10 text-center text-sm text-slate-500">
                            Chưa có dữ liệu sách nổi bật. Hãy thêm dữ liệu bảng books để hiển thị.
                        </div>
                    @endforelse
                </div>
            </div>
        </section>

        <!-- New Arrivals & Top Authors -->
        <section class="max-w-7xl mx-auto px-4 grid gap-8 lg:grid-cols-[1.2fr_0.8fr]">
            <!-- New Books -->
            <article class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-100 shadow-sm">
                <div class="mb-6 flex items-center justify-between border-b border-slate-100 pb-4">
                    <h2 class="text-xl font-bold text-slate-900">Sách mới cập nhật</h2>
                    <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-bold uppercase tracking-wide text-emerald-700">Mới</span>
                </div>
                <div class="space-y-3">
                    @forelse ($newArrivals ?? [] as $book)
                        <div class="flex items-center justify-between rounded-xl border border-slate-100 bg-white px-4 py-3 transition hover:border-emerald-300 hover:bg-emerald-50 group">
                            <div>
                                <a href="{{ route('catalog.book', $book->slug) }}" class="font-bold text-slate-800 group-hover:text-emerald-700 transition-colors">{{ $book->title }}</a>
                                <p class="text-xs text-slate-500 mt-0.5">{{ $book->authors->pluck('name')->first() ?: 'Đang cập nhật tác giả' }}</p>
                            </div>
                            <p class="text-sm font-black text-emerald-600 whitespace-nowrap ml-4">{{ number_format((float) ($book->discount_price ?? $book->price), 0, ',', '.') }}đ</p>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500 text-center py-4">Chưa có dữ liệu sách mới.</p>
                    @endforelse
                </div>
            </article>

            <!-- Top Authors -->
            <article class="bg-white p-6 sm:p-8 rounded-3xl border border-slate-100 shadow-sm">
                <div class="mb-6 flex items-center justify-between border-b border-slate-100 pb-4">
                    <h2 class="text-xl font-bold text-slate-900">Tác giả quan tâm</h2>
                    <span class="rounded-full bg-orange-100 px-3 py-1 text-xs font-bold uppercase tracking-wide text-orange-700">Top</span>
                </div>
                <div class="space-y-3">
                    @forelse ($topAuthors ?? [] as $author)
                        <div class="flex items-center justify-between rounded-xl border border-slate-100 bg-white px-4 py-3 transition hover:border-emerald-300 hover:bg-emerald-50">
                            <p class="font-bold text-slate-800">{{ $author->name }}</p>
                            <span class="bg-slate-100 text-slate-600 text-xs px-2.5 py-1 rounded-full font-medium">{{ $author->books_count }} sách</span>
                        </div>
                    @empty
                        <p class="text-sm text-slate-500 text-center py-4">Chưa có dữ liệu tác giả.</p>
                    @endforelse
                </div>
            </article>
        </section>
    </main>

    <!-- AI Chatbot UI -->
    <button id="chat-toggle-btn" class="fixed bottom-6 right-6 z-50 bg-emerald-600 hover:bg-emerald-700 text-white p-4 rounded-full shadow-2xl shadow-emerald-400/50 transition-all hover:scale-110 flex items-center gap-2 group">
        <div class="relative">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
            <span class="absolute -top-1 -right-1 w-3 h-3 bg-red-500 border-2 border-white rounded-full"></span>
        </div>
        <span class="max-w-0 overflow-hidden group-hover:max-w-xs transition-all duration-500 font-bold whitespace-nowrap">
            Hỏi AI tư vấn
        </span>
    </button>

    <div id="chat-window" class="hidden fixed bottom-6 right-6 z-[60] w-[90vw] md:w-[400px] h-[600px] bg-white rounded-2xl shadow-2xl flex-col overflow-hidden border border-emerald-100 animate-[slide-in_0.3s_ease-out]">
        <!-- Chat Header -->
        <div class="bg-emerald-600 p-4 text-white flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="bg-white/20 p-2 rounded-lg">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                </div>
                <div>
                    <p class="font-bold leading-tight">CatBook AI Assistant</p>
                    <div class="flex items-center gap-1.5 mt-0.5">
                        <span class="w-2 h-2 bg-emerald-300 rounded-full animate-pulse"></span>
                        <span class="text-[10px] opacity-80 uppercase tracking-widest font-bold">Online</span>
                    </div>
                </div>
            </div>
            <button id="chat-close" class="hover:bg-white/10 p-1.5 rounded-lg transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        <!-- Chat Messages -->
        <div id="chat-messages" class="flex-1 overflow-y-auto p-4 space-y-4 bg-emerald-50/20">
            <!-- Initial AI Message -->
            <div class="flex justify-start">
                <div class="max-w-[85%] p-3 rounded-2xl text-sm bg-white text-slate-700 rounded-tl-none shadow-sm border border-emerald-50">
                    Chào bạn! Mình là AI trợ lý của CatBook. Bạn đang tìm cuốn sách nào cho hôm nay?
                </div>
            </div>
        </div>

        <!-- Typing Indicator (Hidden by default) -->
        <div id="typing-indicator" class="hidden px-4 pb-4 bg-emerald-50/20">
            <div class="flex justify-start">
                <div class="bg-white p-3 rounded-2xl rounded-tl-none shadow-sm border border-emerald-50 flex gap-1">
                    <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full animate-bounce"></span>
                    <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full animate-bounce" style="animation-delay: 0.2s"></span>
                    <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full animate-bounce" style="animation-delay: 0.4s"></span>
                </div>
            </div>
        </div>

        <!-- Chat Input -->
        <div class="p-4 bg-white border-t border-emerald-50">
            <form id="chat-form" class="flex items-center gap-2">
                <input 
                    type="text" 
                    id="chat-input"
                    placeholder="Nhập câu hỏi của bạn..."
                    class="flex-1 bg-emerald-50/50 border border-emerald-100 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500 transition-all"
                    autocomplete="off"
                />
                <button type="submit" id="chat-submit" class="bg-emerald-600 text-white p-2.5 rounded-xl hover:bg-emerald-700 disabled:opacity-50 transition-all shadow-md active:scale-95">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                </button>
            </form>
            <p class="text-[10px] text-center text-slate-400 mt-2">Phản hồi bởi CatBook AI - Luôn sẵn sàng hỗ trợ bạn</p>
        </div>
    </div>

    <!-- Script điều khiển Chatbot bằng Vanilla JS -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const apiKey = ""; // ĐIỀN API KEY CỦA BẠN VÀO ĐÂY (Nên bảo mật trên server thực tế)
            
            const chatToggleBtn = document.getElementById('chat-toggle-btn');
            const chatWindow = document.getElementById('chat-window');
            const chatCloseBtn = document.getElementById('chat-close');
            const chatForm = document.getElementById('chat-form');
            const chatInput = document.getElementById('chat-input');
            const chatMessages = document.getElementById('chat-messages');
            const typingIndicator = document.getElementById('typing-indicator');
            const chatSubmitBtn = document.getElementById('chat-submit');

            // Mở / Đóng Chat Window
            chatToggleBtn.addEventListener('click', () => {
                chatWindow.classList.remove('hidden');
                chatWindow.classList.add('flex');
                chatInput.focus();
            });

            chatCloseBtn.addEventListener('click', () => {
                chatWindow.classList.add('hidden');
                chatWindow.classList.remove('flex');
            });

            // Thêm tin nhắn vào giao diện
            function appendMessage(text, isUser) {
                const msgDiv = document.createElement('div');
                msgDiv.className = `flex ${isUser ? 'justify-end' : 'justify-start'}`;
                
                const bubble = document.createElement('div');
                bubble.className = `max-w-[85%] p-3 rounded-2xl text-sm ${
                    isUser 
                    ? 'bg-emerald-600 text-white rounded-tr-none shadow-md' 
                    : 'bg-white text-slate-700 rounded-tl-none shadow-sm border border-emerald-50'
                }`;
                bubble.textContent = text;
                
                msgDiv.appendChild(bubble);
                chatMessages.appendChild(msgDiv);
                
                // Cuộn xuống cuối cùng
                chatMessages.scrollTop = chatMessages.scrollHeight;
            }

            // Gửi tin nhắn
            chatForm.addEventListener('submit', async (e) => {
                e.preventDefault();
                const text = chatInput.value.trim();
                if (!text) return;

                // 1. Hiển thị tin nhắn người dùng
                appendMessage(text, true);
                chatInput.value = '';
                chatInput.disabled = true;
                chatSubmitBtn.disabled = true;

                // 2. Hiển thị hiệu ứng AI đang gõ
                typingIndicator.classList.remove('hidden');
                chatMessages.scrollTop = chatMessages.scrollHeight;

                try {
                    // Gọi API Gemini
                    const response = await fetch(
                        `https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash-preview-09-2025:generateContent?key=${apiKey}`,
                        {
                            method: 'POST',
                            headers: { 'Content-Type': 'application/json' },
                            body: JSON.stringify({
                                contents: [{ parts: [{ text: `Bạn là trợ lý AI của cửa hàng sách CatBook. Hãy tư vấn nhiệt tình về sách. Câu hỏi: ${text}` }] }],
                                systemInstruction: { parts: [{ text: "Hãy trả lời ngắn gọn, lịch sự, gợi ý thêm các đầu sách liên quan." }] }
                            })
                        }
                    );

                    const data = await response.json();
                    let aiText = "Xin lỗi, mình đang gặp chút trục trặc. Bạn thử lại nhé!";
                    
                    if (data.candidates && data.candidates[0].content.parts[0].text) {
                        aiText = data.candidates[0].content.parts[0].text;
                    }
                    
                    appendMessage(aiText, false);
                } catch (error) {
                    console.error("Chat Error: ", error);
                    appendMessage("Hệ thống đang bận, bạn vui lòng quay lại sau nhé!", false);
                } finally {
                    typingIndicator.classList.add('hidden');
                    chatInput.disabled = false;
                    chatSubmitBtn.disabled = false;
                    chatInput.focus();
                }
            });
        });
    </script>

    <style>
        /* Thêm animation nhẹ cho khung chat */
        @keyframes slide-in {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        .animate-\[slide-in_0\.3s_ease-out\] {
            animation: slide-in 0.3s ease-out forwards;
        }
    </style>
</body>
</html>