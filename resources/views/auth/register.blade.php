<x-layouts.guest title="Đăng ký - Catbook">
    <div class="grid w-full max-w-5xl overflow-hidden rounded-3xl border border-white/10 bg-white/5 shadow-2xl shadow-black/30 backdrop-blur xl:grid-cols-2">
        <section class="hidden flex-col justify-between bg-[linear-gradient(160deg,#22c55e_0%,#06b6d4_100%)] p-10 text-slate-950 xl:flex">
            <div>
                <p class="text-sm font-semibold uppercase tracking-[0.3em] text-black/60">Catbook AI</p>
                <h1 class="mt-6 max-w-md text-5xl font-black leading-tight">Tạo tài khoản để mua sách và dùng AI tư vấn.</h1>
                <p class="mt-4 max-w-md text-base text-black/75">Đăng ký xong là có thể lưu địa chỉ, đánh giá sách và chat với trợ lý AI.</p>
            </div>
            <div class="rounded-2xl bg-black/10 p-5 text-sm text-black/80">
                <p class="font-semibold text-black">Dành cho đồ án</p>
                <p class="mt-2">Luồng đăng ký này tự kích hoạt tài khoản customer để bạn demo dễ hơn, còn status trong CSDL vẫn giữ được để mở rộng duyệt thủ công sau.</p>
            </div>
        </section>

        <section class="p-8 sm:p-10 lg:p-12">
            <div class="mx-auto max-w-md">
                <div class="mb-8">
                    <p class="text-sm font-semibold uppercase tracking-[0.25em] text-cyan-300/90">Bắt đầu ngay</p>
                    <h2 class="mt-3 text-3xl font-bold text-white">Đăng ký Catbook</h2>
                    <p class="mt-2 text-sm text-slate-300">Tạo tài khoản khách hàng để lưu giỏ hàng, đơn hàng và lịch sử chat AI.</p>
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
        </section>
    </div>
</x-layouts.guest>
