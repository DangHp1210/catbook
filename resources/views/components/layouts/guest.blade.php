<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title ?? config('app.name', 'Catbook') }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen bg-slate-950 text-slate-100">
    <div class="relative isolate min-h-screen overflow-hidden">
        <div class="absolute inset-0 bg-[radial-gradient(circle_at_top_left,_rgba(251,191,36,0.22),_transparent_32%),radial-gradient(circle_at_right,_rgba(14,165,233,0.18),_transparent_28%),linear-gradient(180deg,#0f172a_0%,#020617_100%)]"></div>
        <div class="relative mx-auto min-h-screen max-w-6xl px-4 py-4 sm:px-6 lg:px-8">
            <x-navbar :dark="true" />
            <div class="flex min-h-[calc(100vh-6rem)] items-center justify-center py-8">
                {{ $slot }}
            </div>
        </div>
    </div>
</body>
</html>
