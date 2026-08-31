<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>BudgetKit</title>
    <link rel="icon" type="image/svg+xml" href="/images/logo/logo-icon.svg">
    <x-vite-assets />
</head>
<body class="min-h-screen bg-gradient-to-br from-[#667eea] to-[#4ecdc4] text-white pb-24">

    {{-- Top Bar --}}
    <header class="fixed top-0 left-0 right-0 z-50 bg-[#667eea]/80 backdrop-blur-lg">
        <div class="max-w-lg mx-auto px-4 h-14 flex items-center justify-between">
            @hasSection('topbar-left')
                @yield('topbar-left')
            @else
                <div class="flex items-center gap-2.5">
                    <img src="/images/logo/logo-icon.svg" alt="BudgetKit" width="26" height="26" class="flex-shrink-0">
                    <span class="text-white font-bold text-lg tracking-tight">BudgetKit</span>
                </div>
            @endif
            <div class="flex items-center gap-2">
                @yield('topbar-right')
            </div>
        </div>
    </header>

    {{-- Content below fixed top bar --}}
    <div class="pt-14">
        @yield('content')
    </div>

    {{-- Bottom Navigation --}}
    <nav class="fixed bottom-0 left-0 right-0 bg-white/95 backdrop-blur-lg border-t border-gray-200 z-50">
        <div class="flex items-center justify-around h-16 max-w-lg mx-auto">
            <a href="{{ route('home') }}"
               class="flex flex-col items-center gap-0.5 {{ request()->routeIs('home') ? 'text-[#667eea]' : 'text-gray-400' }}">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M12 3l9 8h-3v10h-5v-6H11v6H6V11H3l9-8z"/></svg>
                <span class="text-[10px] font-semibold">{{ __('nav.nav_home') }}</span>
            </a>
            <a href="{{ route('budget.index') }}"
               class="flex flex-col items-center gap-0.5 {{ request()->routeIs('budget.*') ? 'text-[#667eea]' : 'text-gray-400' }}">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M3 3h18v2H3V3zm0 4h18v14H3V7zm2 2v10h14V9H5zm2 2h4v2H7v-2zm6 0h4v2h-4v-2zm-6 4h4v2H7v-2zm6 0h4v2h-4v-2z"/></svg>
                <span class="text-[10px] font-semibold">{{ __('nav.nav_budget') }}</span>
            </a>
            {{-- FAB --}}
            <a href="{{ route('transactions.create') }}" class="relative -top-4">
                <div class="w-14 h-14 rounded-full bg-gradient-to-br from-[#667eea] to-[#4ecdc4] flex items-center justify-center shadow-lg shadow-[#667eea]/30">
                    <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path stroke-linecap="round" d="M12 5v14m-7-7h14"/></svg>
                </div>
            </a>
            <a href="{{ route('transactions.index') }}"
               class="flex flex-col items-center gap-0.5 {{ request()->routeIs('transactions.*') ? 'text-[#667eea]' : 'text-gray-400' }}">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M3 13h2v-2H3v2zm0 4h2v-2H3v2zm0-8h2V7H3v2zm4 4h14v-2H7v2zm0 4h14v-2H7v2zM7 7v2h14V7H7z"/></svg>
                <span class="text-[10px] font-semibold">{{ __('nav.nav_transactions') }}</span>
            </a>
            <a href="{{ route('stats.index') }}"
               class="flex flex-col items-center gap-0.5 {{ request()->routeIs('stats.*') ? 'text-[#667eea]' : 'text-gray-400' }}">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24"><path d="M5 9.2h3V19H5V9.2zM10.6 5h2.8v14h-2.8V5zm5.6 8H19v6h-2.8v-6z"/></svg>
                <span class="text-[10px] font-semibold">{{ __('nav.nav_stats') }}</span>
            </a>
        </div>
    </nav>

    @stack('scripts')



</body>
</html>
