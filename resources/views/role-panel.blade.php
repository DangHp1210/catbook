<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} - Catbook</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="cb-site text-slate-900">
    <main class="cb-page">
        <x-navbar />

        @php
            $themeClasses = match ($theme ?? 'amber') {
                'rose' => 'from-rose-500/25 to-rose-600/10 border-rose-300/30 text-rose-200',
                'cyan' => 'from-cyan-500/25 to-cyan-600/10 border-cyan-300/30 text-cyan-200',
                default => 'from-amber-500/25 to-amber-600/10 border-amber-300/30 text-amber-200',
            };
        @endphp

        <section class="mt-6 rounded-3xl border border-slate-200 bg-white p-8 shadow-sm">
            <h1 class="text-3xl font-bold text-slate-900">{{ $title }}</h1>
            <p class="mt-3 text-base text-slate-600">{{ $description }}</p>
            <p class="mt-5 text-sm text-slate-500">Tài khoản hiện tại: {{ auth()->user()->full_name }} ({{ auth()->user()->role }})</p>
        </section>
    </main>
</body>
</html>
