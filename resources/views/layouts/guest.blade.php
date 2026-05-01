@php
    $guestTitle = $title ?? config('app.name', 'CatBook');
@endphp

<x-layouts.guest :title="$guestTitle">
    @if (isset($slot))
        {{ $slot }}
    @else
        @yield('content')
    @endif
</x-layouts.guest>
