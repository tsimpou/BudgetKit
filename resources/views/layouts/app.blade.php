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

    <x-common.dark-mode-init />
    
</head>

<body x-data>


    <div class="min-h-screen xl:flex">
        @include('layouts.backdrop')
        @include('layouts.sidebar')

        <div class="flex-1 transition-all duration-300 ease-in-out"
            :class="{
                'xl:ml-[290px]': $store.sidebar.isExpanded || $store.sidebar.isHovered,
                'xl:ml-[90px]': !$store.sidebar.isExpanded && !$store.sidebar.isHovered,
                'ml-0': $store.sidebar.isMobileOpen
            }">
            <!-- app header start -->
            @include('layouts.app-header')
            <!-- app header end -->
            <div class="p-4 mx-auto max-w-(--breakpoint-2xl) md:p-6">
                <turbo-frame id="page-content" data-turbo-action="advance">
                    @yield('content')
                </turbo-frame>
                <x-footer />
            </div>
        </div>

    </div>

    @stack('scripts')
</body>

</html>
