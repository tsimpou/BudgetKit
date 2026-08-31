@extends('layouts.app')

@section('content')

<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800 dark:text-white/90">{{ __('auth.profile') }}</h1>
</div>

<div class="max-w-2xl space-y-6">

    {{-- Info personali --}}
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800">
            <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">{{ __('auth.personal_info') }}</h3>
        </div>
        <div class="p-6">

            @if(session('success_info'))
                <div class="mb-4 px-4 py-3 rounded-lg bg-green-50 border border-green-200 text-green-700 dark:bg-green-900/20 dark:border-green-800 dark:text-green-400 text-sm">
                    {{ session('success_info') }}
                </div>
            @endif

            <form action="{{ route('profile.update-info') }}" method="POST">
                @csrf

                <div class="mb-5">
                    <label for="profile-name" class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-1.5">
                        {{ __('auth.name') }} <span class="text-red-500">*</span>
                    </label>
                    <input id="profile-name" type="text" name="name" value="{{ old('name', $user->name) }}" required autocomplete="name"
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-800 dark:text-white/90 focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500">
                    @error('name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div class="mb-8">
                    <label for="profile-email" class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-1.5">
                        Email <span class="text-red-500">*</span>
                    </label>
                    <input id="profile-email" type="email" name="email" value="{{ old('email', $user->email) }}" required autocomplete="email"
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-800 dark:text-white/90 focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500">
                    @error('email')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <button type="submit"
                    class="px-5 py-2.5 rounded-lg bg-brand-500 hover:bg-brand-600 text-white text-sm font-semibold transition">
                    {{ __('transactions.save') }}
                </button>
            </form>
        </div>
    </div>

    {{-- Cambio password --}}
    <div class="rounded-2xl border border-gray-200 bg-white dark:border-gray-800 dark:bg-white/[0.03]">
        <div class="px-6 py-5 border-b border-gray-100 dark:border-gray-800">
            <h3 class="text-base font-semibold text-gray-800 dark:text-white/90">{{ __('auth.change_password') }}</h3>
        </div>
        <div class="p-6">

            @if(session('success_password'))
                <div class="mb-4 px-4 py-3 rounded-lg bg-green-50 border border-green-200 text-green-700 dark:bg-green-900/20 dark:border-green-800 dark:text-green-400 text-sm">
                    {{ session('success_password') }}
                </div>
            @endif

            <form action="{{ route('profile.update-password') }}" method="POST">
                @csrf

                <div class="mb-5">
                    <label for="profile-current-password" class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-1.5">
                        {{ __('auth.current_password') }} <span class="text-red-500">*</span>
                    </label>
                    <input id="profile-current-password" type="password" name="current_password" required autocomplete="current-password"
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-800 dark:text-white/90 focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500">
                    @error('current_password')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div class="mb-5">
                    <label for="profile-new-password" class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-1.5">
                        {{ __('auth.new_password') }} <span class="text-red-500">*</span>
                    </label>
                    <input id="profile-new-password" type="password" name="password" required autocomplete="new-password"
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-800 dark:text-white/90 focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500">
                    @error('password')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <div class="mb-8">
                    <label for="profile-confirm-password" class="block text-sm font-medium text-gray-700 dark:text-gray-400 mb-1.5">
                        {{ __('auth.confirm_password') }} <span class="text-red-500">*</span>
                    </label>
                    <input id="profile-confirm-password" type="password" name="password_confirmation" required autocomplete="new-password"
                        class="w-full px-4 py-3 rounded-lg border border-gray-300 dark:border-gray-700 bg-white dark:bg-gray-900 text-gray-800 dark:text-white/90 focus:outline-none focus:ring-2 focus:ring-brand-500/30 focus:border-brand-500">
                </div>

                <button type="submit"
                    class="px-5 py-2.5 rounded-lg bg-brand-500 hover:bg-brand-600 text-white text-sm font-semibold transition">
                    {{ __('auth.change_password') }}
                </button>
            </form>
        </div>
    </div>

</div>

@endsection
