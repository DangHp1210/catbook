<x-layouts.guest title="Đăng nhập - Catbook">
    <div class="grid w-full max-w-5xl overflow-hidden rounded-3xl border border-white/10 bg-white/5 shadow-2xl shadow-black/30 backdrop-blur xl:grid-cols-2">
        <section class="hidden flex-col justify-between bg-[linear-gradient(160deg,#f59e0b_0%,#ef4444_100%)] p-10 text-slate-950 xl:flex">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-black/60">Catbook AI</p>
                <h1 class="mt-6 max-w-md text-5xl font-black leading-tight">Đăng nhập để quản lý sách, đơn hàng và chat AI.</h1>
                <p class="mt-4 max-w-md text-base text-black/75">Một cổng duy nhất cho khách hàng, nhân viên và admin của website bán sách trực tuyến.</p>
            </div>
            <div class="rounded-2xl bg-black/10 p-5 text-sm text-black/80">
                <p class="font-semibold text-black">Điểm nhấn</p>
                <p class="mt-2">Gợi ý sách theo lịch sử chat, quản lý tài khoản, theo dõi đơn hàng và hỗ trợ khách hàng bằng AI.</p>
            </div>
        </section>

        <section class="p-8 sm:p-10 lg:p-12">
            <div class="mx-auto max-w-md">
                <div class="mb-8">
                    <p class="text-sm font-semibold uppercase tracking-[0.25em] text-amber-300/90">Chào mừng trở lại</p>
                    <h2 class="mt-3 text-3xl font-bold text-white">Đăng nhập Catbook</h2>
                    <p class="mt-2 text-sm text-slate-300">Dùng email và mật khẩu để vào trang quản trị hoặc tài khoản mua sách.</p>
                </div>

                @if ($errors->any())
                    <div class="mb-5 rounded-2xl border border-rose-400/30 bg-rose-500/10 p-4 text-sm text-rose-200">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login.store') }}" class="space-y-5">
                    @csrf
                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-200" for="email">Email</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus class="w-full rounded-2xl border border-white/10 bg-slate-900/80 px-4 py-3 text-white outline-none transition focus:border-amber-300/70 focus:ring-4 focus:ring-amber-300/15" placeholder="you@example.com">
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-slate-200" for="password">Mật khẩu</label>
                        <input id="password" name="password" type="password" required class="w-full rounded-2xl border border-white/10 bg-slate-900/80 px-4 py-3 text-white outline-none transition focus:border-amber-300/70 focus:ring-4 focus:ring-amber-300/15" placeholder="••••••••">
                    </div>

                    <label class="flex items-center gap-3 text-sm text-slate-300">
                        <input type="checkbox" name="remember" class="h-4 w-4 rounded border-slate-500 bg-slate-900 text-amber-400 focus:ring-amber-300/20">
                        Ghi nhớ đăng nhập
                    </label>

                    <button type="submit" class="w-full rounded-2xl bg-amber-400 px-4 py-3 font-semibold text-slate-950 transition hover:bg-amber-300">Đăng nhập</button>
                </form>

                <p class="mt-6 text-center text-sm text-slate-300">
                    Chưa có tài khoản?
                    <a href="{{ route('register') }}" class="font-semibold text-amber-300 hover:text-amber-200">Đăng ký ngay</a>
                </p>
            </div>
        </section>
    </div>
</x-layouts.guest>
