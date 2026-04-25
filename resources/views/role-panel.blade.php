<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} - Catbook</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-950 text-slate-100">
    <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_right,_rgba(34,197,94,0.12),_transparent_25%),radial-gradient(circle_at_bottom_left,_rgba(14,165,233,0.12),_transparent_30%)]"></div>
    <main class="relative mx-auto max-w-6xl px-4 py-10 sm:px-6 lg:px-8">
        <x-navbar :dark="true" />

        @php
            $themeClasses = match ($theme ?? 'amber') {
                'rose' => 'from-rose-500/25 to-rose-600/10 border-rose-300/30 text-rose-200',
                'cyan' => 'from-cyan-500/25 to-cyan-600/10 border-cyan-300/30 text-cyan-200',
                default => 'from-amber-500/25 to-amber-600/10 border-amber-300/30 text-amber-200',
            };
        @endphp

        <section class="mt-6 rounded-3xl border bg-gradient-to-br p-8 backdrop-blur {{ $themeClasses }}">
            <h1 class="text-3xl font-bold">{{ $title }}</h1>
            <p class="mt-3 text-base text-slate-200">{{ $description }}</p>
            <p class="mt-5 text-sm text-slate-300">Tài khoản hiện tại: {{ auth()->user()->full_name }} ({{ auth()->user()->role }})</p>
        </section>
    </main>
</body>
</html>
