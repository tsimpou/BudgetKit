<?php

namespace App\Http\Middleware;

use App\Models\Setting;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

// Reads the locale saved in the settings table (e.g. 'it', 'en') and sets it as the
// application locale for every request, so all __() translations use the right language.
// The try/catch handles the case where the settings table does not exist yet (e.g. before migrations).
// Supported locales: 'it' (default), 'en' — defined in config/budget.php → locales.
class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $locale = env('APP_LOCALE');

        if (! $locale) {
            try {
                $locale = Setting::get('locale', config('budget.default_locale', 'en'));
            } catch (\Throwable) {
                $locale = config('budget.default_locale', 'en');
            }
        }

        App::setLocale($locale);

        return $next($request);
    }
}
