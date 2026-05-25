<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? config('app.name', 'CatBook') }}</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link rel="preload" as="image" href="{{ asset('images/logocatbook3.png') }}">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,700;0,900;1,700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">

    @vite(['resources/css/app.css', 'resources/js/app.js'])

    @yield('styles')
    <style>
        html, body { height: 100%; }
        body.cb-site { display: flex; flex-direction: column; min-height: 100vh; }
        main.cb-page { flex: 1 0 auto; }
    </style>
</head>
<body class="cb-site text-slate-800">

<x-navbar />

<main class="cb-page">
    @yield('content')
</main>

<x-footer />

</body>
</html>
