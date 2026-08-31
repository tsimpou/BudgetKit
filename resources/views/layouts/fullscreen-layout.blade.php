<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>BudgetKit</title>
    <link rel="icon" type="image/svg+xml" href="/images/logo/logo-icon.svg">
    <link rel="icon" type="image/x-icon" href="/favicon.ico">

    <!-- Scripts -->
    <x-vite-assets />

    <!-- Apply dark mode immediately to prevent flash -->
    <x-common.dark-mode-init />
</head>

<body x-data>

    {{-- preloader --}}
    <x-common.preloader/>
    {{-- preloader end --}}

    @yield('content')

</body>

@stack('scripts')

</html>
