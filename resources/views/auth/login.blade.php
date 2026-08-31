@extends('layouts.fullscreen-layout')

@section('content')
<div class="relative z-1 bg-white p-6 sm:p-0 dark:bg-gray-900">
    <div class="relative flex h-screen w-full flex-col justify-center sm:p-0 lg:flex-row dark:bg-gray-900">

        <!-- Form -->
        <div class="flex w-full flex-1 flex-col lg:w-1/2">
            <div class="mx-auto flex w-full max-w-md flex-1 flex-col justify-center">
                <div class="mb-5 sm:mb-8">
                    <h1 class="text-title-sm sm:text-title-md mb-2 font-semibold text-gray-800 dark:text-white/90">
                        {{ __('auth.sign_in') }}
                    </h1>
                    <p class="text-sm text-gray-500 dark:text-gray-400">
                        {{ __('auth.sign_in_subtitle') }}
                    </p>
                </div>

                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <div class="space-y-5">

                        <!-- Email -->
                        <div>
                            <label for="login-email" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                Email <span class="text-red-500">*</span>
                            </label>
                            <input id="login-email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="email"
                                placeholder="nome@email.com"
                                class="h-11 w-full rounded-lg border px-4 py-2.5 text-sm text-gray-800 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90 dark:placeholder:text-white/30 @error('email') border-red-500 @else border-gray-300 @enderror">
                            @error('email')
                                <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Password -->
                        <div>
                            <label for="login-password" class="mb-1.5 block text-sm font-medium text-gray-700 dark:text-gray-400">
                                Password <span class="text-red-500">*</span>
                            </label>
                            <div x-data="{ showPassword: false }" class="relative">
                                <input id="login-password" :type="showPassword ? 'text' : 'password'" name="password" required autocomplete="current-password"
                                    placeholder="••••••••"
                                    class="h-11 w-full rounded-lg border border-gray-300 bg-transparent py-2.5 pr-11 pl-4 text-sm text-gray-800 placeholder:text-gray-400 focus:outline-none focus:ring-2 focus:ring-brand-500/20 focus:border-brand-500 dark:border-gray-700 dark:bg-gray-900 dark:text-white/90">
                                <span @click="showPassword = !showPassword"
                                    class="absolute top-1/2 right-4 -translate-y-1/2 cursor-pointer text-gray-500 dark:text-gray-400">
                                    <svg x-show="!showPassword" class="fill-current" width="20" height="20" viewBox="0 0 20 20"><path fill-rule="evenodd" clip-rule="evenodd" d="M10.0002 13.8619C7.23361 13.8619 4.86803 12.1372 3.92328 9.70241C4.86804 7.26761 7.23361 5.54297 10.0002 5.54297C12.7667 5.54297 15.1323 7.26762 16.0771 9.70243C15.1323 12.1372 12.7667 13.8619 10.0002 13.8619ZM10.0002 4.04297C6.48191 4.04297 3.49489 6.30917 2.4155 9.4593C2.3615 9.61687 2.3615 9.78794 2.41549 9.94552C3.49488 13.0957 6.48191 15.3619 10.0002 15.3619C13.5184 15.3619 16.5055 13.0957 17.5849 9.94555C17.6389 9.78797 17.6389 9.6169 17.5849 9.45932C16.5055 6.30919 13.5184 4.04297 10.0002 4.04297ZM9.99151 7.84413C8.96527 7.84413 8.13333 8.67606 8.13333 9.70231C8.13333 10.7286 8.96527 11.5605 9.99151 11.5605H10.0064C11.0326 11.5605 11.8646 10.7286 11.8646 9.70231C11.8646 8.67606 11.0326 7.84413 10.0064 7.84413H9.99151Z" fill="#98A2B3"/></svg>
                                    <svg x-show="showPassword" class="fill-current" width="20" height="20" viewBox="0 0 20 20"><path fill-rule="evenodd" clip-rule="evenodd" d="M4.63803 3.57709C4.34513 3.2842 3.87026 3.2842 3.57737 3.57709C3.28447 3.86999 3.28447 4.34486 3.57737 4.63775L4.85323 5.91362C3.74609 6.84199 2.89363 8.06395 2.4155 9.45936C2.3615 9.61694 2.3615 9.78801 2.41549 9.94558C3.49488 13.0957 6.48191 15.3619 10.0002 15.3619C11.255 15.3619 12.4422 15.0737 13.4994 14.5598L15.3625 16.4229C15.6554 16.7158 16.1302 16.7158 16.4231 16.4229C16.716 16.13 16.716 15.6551 16.4231 15.3622L4.63803 3.57709Z" fill="#98A2B3"/></svg>
                                </span>
                            </div>
                        </div>

                        <!-- Remember me + Forgot password -->
                        <div class="flex items-center justify-between">
                            <label class="flex cursor-pointer items-center gap-2 text-sm text-gray-700 dark:text-gray-400">
                                <input type="checkbox" name="remember" class="rounded border-gray-300 text-brand-500 focus:ring-brand-500">
                                {{ __('auth.remember_me') }}
                            </label>
                            <a href="{{ route('password.request') }}" class="text-sm text-brand-500 hover:text-brand-600 dark:text-brand-400">
                                {{ __('auth.forgot_password_link') }}
                            </a>
                        </div>

                        <!-- Submit -->
                        <button type="submit"
                            class="flex w-full items-center justify-center rounded-lg bg-brand-500 px-4 py-3 text-sm font-medium text-white transition hover:bg-brand-600">
                            {{ __('auth.sign_in') }}
                        </button>
                    </div>
                </form>

                <div class="mt-5">
                    <p class="text-center text-sm text-gray-700 dark:text-gray-400">
                        {{ __('auth.no_account') }}
                        <a href="{{ route('register') }}" class="text-brand-500 hover:text-brand-600 dark:text-brand-400">
                            {{ __('auth.sign_up') }}
                        </a>
                    </p>
                </div>
            </div>
        </div>

        <!-- Right panel -->
        <div class="bg-brand-950 relative hidden h-full w-full items-center lg:grid lg:w-1/2 dark:bg-white/5">
            <div class="z-1 flex items-center justify-center">
                <x-common.common-grid-shape />
                <div class="flex max-w-xs flex-col items-center gap-4">
                    <img src="/images/logo/logo-icon.svg" alt="BudgetKit" width="64" height="64" />
                    <span class="text-3xl font-bold text-white tracking-tight">BudgetKit</span>
                    <p class="text-center text-sm text-gray-400">{{ __('auth.app_tagline') }}</p>
                </div>
            </div>
        </div>

        <!-- Dark mode toggler -->
        <div class="fixed right-6 bottom-6 z-50">
            <button class="bg-brand-500 hover:bg-brand-600 inline-flex size-14 items-center justify-center rounded-full text-white transition-colors"
                @click.prevent="$store.theme.toggle()">
                <svg class="hidden fill-current dark:block" width="20" height="20" viewBox="0 0 20 20"><path fill-rule="evenodd" clip-rule="evenodd" d="M9.99998 1.5415C10.4142 1.5415 10.75 1.87729 10.75 2.2915V3.5415C10.75 3.95572 10.4142 4.2915 9.99998 4.2915C9.58577 4.2915 9.24998 3.95572 9.24998 3.5415V2.2915C9.24998 1.87729 9.58577 1.5415 9.99998 1.5415Z" fill="currentColor"/></svg>
                <svg class="fill-current dark:hidden" width="20" height="20" viewBox="0 0 20 20"><path d="M17.4547 11.97L18.1799 12.1611C18.265 11.8383 18.1265 11.4982 17.8401 11.3266C17.5538 11.1551 17.1885 11.1934 16.944 11.4207L17.4547 11.97Z" fill="currentColor"/></svg>
            </button>
        </div>
    </div>
</div>
@endsection
