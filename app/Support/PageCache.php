<?php

namespace App\Support;

use Illuminate\Support\Facades\Auth;

class PageCache
{
    /** @var array<string, array{value: mixed, expires: int}> */
    private static array $store = [];

    public static function remember(string $key, int $seconds, callable $callback): mixed
    {
        $fullKey = self::prefix().$key;
        $now = time();

        if (isset(self::$store[$fullKey]) && self::$store[$fullKey]['expires'] > $now) {
            return self::$store[$fullKey]['value'];
        }

        $value = $callback();
        self::$store[$fullKey] = ['value' => $value, 'expires' => $now + $seconds];

        return $value;
    }

    public static function forget(string ...$keys): void
    {
        $prefix = self::prefix();

        foreach ($keys as $key) {
            unset(self::$store[$prefix.$key]);
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
