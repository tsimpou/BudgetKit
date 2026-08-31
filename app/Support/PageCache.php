<?php

namespace App\Support;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class PageCache
{
    public static function remember(string $key, int $seconds, callable $callback): mixed
    {
        return Cache::remember(self::prefix().$key, $seconds, $callback);
    }

    public static function forget(string ...$keys): void
    {
        $prefix = self::prefix();

        foreach ($keys as $key) {
            Cache::forget($prefix.$key);
        }
    }

    public static function forgetMonth(int $year, int $month): void
    {
        self::forget(
            "cats:{$year}:{$month}",
            "summary:{$year}:{$month}",
            "spending:{$year}:{$month}",
            'rta',
            'trend',
        );
    }

    private static function prefix(): string
    {
        return 'bk:'.(Auth::id() ?? '0').':';
    }
}
