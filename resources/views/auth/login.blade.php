<x-layouts.guest title="Đăng nhập | CatBook">
    <div class="w-full max-w-md rounded-3xl border border-slate-200 bg-white p-8 shadow-[0_20px_60px_rgba(13,27,16,0.08)] sm:p-10">
        <div class="mb-8 text-center">
            <p class="text-sm font-semibold uppercase tracking-[0.25em] text-emerald-700">Chào mừng trở lại</p>
            <h2 class="mt-3 text-3xl font-bold text-slate-900">Đăng nhập CatBook</h2>
            <p class="mt-3 text-xs leading-6 text-slate-600">Đăng nhập để tiếp tục mua sắm, đồng bộ giỏ hàng và theo dõi trạng thái đơn hàng của bạn.</p>
        </div>

        @if ($errors->any())
            <div class="mb-5 rounded-2xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-700">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('login.store') }}" class="space-y-5">
            @csrf
            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700" for="email">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required autofocus class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10" placeholder="you@example.com">
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-slate-700" for="password">Mật khẩu</label>
                <input id="password" name="password" type="password" required class="w-full rounded-2xl border border-slate-200 bg-white px-4 py-3 text-slate-900 outline-none transition focus:border-emerald-500 focus:ring-4 focus:ring-emerald-500/10" placeholder="••••••••">
            </div>

            <label class="flex items-center gap-3 text-sm text-slate-600">
                <input type="checkbox" name="remember" class="h-4 w-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500/20">
                Ghi nhớ đăng nhập
            </label>

            <button type="submit" class="w-full rounded-full bg-slate-900 px-4 py-3 font-semibold text-white transition hover:bg-emerald-700">Đăng nhập</button>
        </form>

        <p class="mt-6 text-center text-sm text-slate-600">
            Chưa có tài khoản?
            <a href="{{ route('register') }}" class="font-semibold text-emerald-700 hover:text-emerald-800">Đăng ký ngay</a>
        </p>
    </div>
</x-layouts.guest>
