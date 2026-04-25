<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard - Catbook</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-950 text-slate-100">
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,_rgba(34,197,94,0.12),_transparent_25%),radial-gradient(circle_at_bottom_left,_rgba(14,165,233,0.12),_transparent_30%)]"></div>
    <main class="relative mx-auto max-w-6xl px-4 py-10 sm:px-6 lg:px-8">
        <x-navbar :dark="true" />

        <div class="mb-8 mt-6 rounded-3xl border border-white/10 bg-white/5 px-6 py-5 backdrop-blur">
            <p class="text-xs font-semibold uppercase tracking-[0.3em] text-slate-400">Catbook</p>
            <h1 class="mt-2 text-2xl font-bold">Dashboard</h1>
        </div>

        <section class="grid gap-6 lg:grid-cols-3">
            <div class="rounded-3xl border border-white/10 bg-white/5 p-6 lg:col-span-2">
                <p class="text-sm text-slate-400">Xin chào</p>
                <h2 class="mt-2 text-3xl font-bold text-white">{{ auth()->user()->full_name }}</h2>
                <p class="mt-3 text-slate-300">Email: {{ auth()->user()->email }}</p>
                <p class="mt-2 text-slate-300">Vai trò: {{ auth()->user()->role }}</p>
                <p class="mt-2 text-slate-300">Trạng thái: {{ auth()->user()->status }}</p>
            </div>

            <div class="rounded-3xl border border-white/10 bg-white/5 p-6">
                <p class="text-sm text-slate-400">Các bước tiếp theo</p>
                <ul class="mt-4 space-y-3 text-sm text-slate-300">
                    <li>1. Kết nối trang quản trị sách.</li>
                    <li>2. Áp middleware `role` cho toàn bộ CRUD theo vai trò.</li>
                    <li>3. Hoàn thiện chức năng riêng cho Admin / Staff / Customer.</li>
                </ul>
            </div>
        </section>
    </main>
</body>
</html>
