@extends('layouts.app')

@section('content')

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800 dark:text-white/90">{{ __('settings.settings') }}</h1>
</div>

{{-- Flash --}}
@if(session('success'))
    <div class="mb-4 px-4 py-3 rounded-lg bg-green-50 border border-green-200 text-green-700 dark:bg-green-900/20 dark:border-green-800 dark:text-green-400 text-sm">
        {{ session('success') }}
    </div>
@endif

<div class="max-w-2xl space-y-6">

    {{-- Form impostazioni --}}
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800">
            <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">{{ __('settings.settings') }}</h3>
        </div>
        <div class="p-6">
            <form action="{{ route('settings.update') }}" method="POST">
                @csrf

                {{-- Lingua --}}
                <div class="mb-5">
                    <label for="settings-locale" class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-1.5">
                        {{ __('settings.language') }}
                    </label>
                    <select id="settings-locale" name="locale" autocomplete="language"
                            class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-800 dark:text-white/90 focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500">
                        @foreach($locales as $code => $label)
                            <option value="{{ $code }}" {{ $locale === $code ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Valuta --}}
                <div class="mb-8">
                    <label for="settings-currency" class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-1.5">
                        {{ __('settings.currency') }}
                    </label>
                    <select id="settings-currency" name="currency"
                            class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-800 dark:text-white/90 focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500">
                        @foreach($currencies as $code => $label)
                            <option value="{{ $code }}" {{ $currencyCode === $code ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                </div>

                <button type="submit"
                        class="px-5 py-2.5 rounded-lg bg-brand-500 hover:bg-brand-600 text-white text-sm font-semibold transition">
                    {{ __('settings.save_settings') }}
                </button>
            </form>
        </div>
    </div>

    {{-- Export --}}
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800">
            <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">{{ __('import.export_csv') }}</h3>
        </div>
        <div class="p-6">
            <p class="text-sm text-gray-500 dark:text-gray-400 mb-4">
                {{ __('import.export_description') }}
            </p>
            <a href="{{ route('export.csv') }}"
               class="inline-flex items-center gap-2 px-5 py-2.5 rounded-lg border border-gray-300 dark:border-gray-700 text-gray-700 dark:text-gray-300 text-sm font-medium hover:bg-gray-50 dark:hover:bg-gray-800 transition">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 10v6m0 0l-3-3m3 3l3-3M3 17V7a2 2 0 012-2h6l2 2h6a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2z"/>
                </svg>
                {{ __('import.export_csv') }}
            </a>
        </div>
    </div>

</div>

@endsection
