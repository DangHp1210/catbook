<x-layouts.guest title="Đăng ký - Catbook">
    <div class="w-full max-w-md rounded-3xl border border-white/10 bg-white/5 p-8 shadow-2xl shadow-black/30 backdrop-blur sm:p-10">
        <div class="mb-8 text-center">
            <p class="text-sm font-semibold uppercase tracking-[0.25em] text-cyan-300/90">Bắt đầu ngay</p>
            <h2 class="mt-3 text-3xl font-bold text-white">Đăng ký Catbook</h2>

        </div>

        @if ($errors->any())
            <div class="mb-5 rounded-2xl border border-rose-400/30 bg-rose-500/10 p-4 text-sm text-rose-200">
                {{ $errors->first() }}
            </div>
        @endif

        <form method="POST" action="{{ route('register.store') }}" class="space-y-5">
            @csrf
            <div>
                <label class="mb-2 block text-sm font-medium text-slate-200" for="full_name">Họ và tên</label>
                <input id="full_name" name="full_name" type="text" value="{{ old('full_name') }}" required autofocus class="w-full rounded-2xl border border-white/10 bg-slate-900/80 px-4 py-3 text-white outline-none transition focus:border-cyan-300/70 focus:ring-4 focus:ring-cyan-300/15" placeholder="Nguyễn Văn A">
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-slate-200" for="email">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required class="w-full rounded-2xl border border-white/10 bg-slate-900/80 px-4 py-3 text-white outline-none transition focus:border-cyan-300/70 focus:ring-4 focus:ring-cyan-300/15" placeholder="you@example.com">
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-slate-200" for="phone">Số điện thoại</label>
                <input id="phone" name="phone" type="text" value="{{ old('phone') }}" class="w-full rounded-2xl border border-white/10 bg-slate-900/80 px-4 py-3 text-white outline-none transition focus:border-cyan-300/70 focus:ring-4 focus:ring-cyan-300/15" placeholder="09xxxxxxxx">
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-slate-200" for="password">Mật khẩu</label>
                <input id="password" name="password" type="password" required class="w-full rounded-2xl border border-white/10 bg-slate-900/80 px-4 py-3 text-white outline-none transition focus:border-cyan-300/70 focus:ring-4 focus:ring-cyan-300/15" placeholder="Tối thiểu 8 ký tự">
            </div>

            <div>
                <label class="mb-2 block text-sm font-medium text-slate-200" for="password_confirmation">Xác nhận mật khẩu</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required class="w-full rounded-2xl border border-white/10 bg-slate-900/80 px-4 py-3 text-white outline-none transition focus:border-cyan-300/70 focus:ring-4 focus:ring-cyan-300/15" placeholder="Nhập lại mật khẩu">
            </div>

            <button type="submit" class="w-full rounded-2xl bg-cyan-400 px-4 py-3 font-semibold text-slate-950 transition hover:bg-cyan-300">Tạo tài khoản</button>
        </form>

        <p class="mt-6 text-center text-sm text-slate-300">
            Đã có tài khoản?
            <a href="{{ route('login') }}" class="font-semibold text-cyan-300 hover:text-cyan-200">Đăng nhập</a>
        </p>
    </div>
</x-layouts.guest>
