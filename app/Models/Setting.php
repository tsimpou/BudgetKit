<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $primaryKey = 'key';

    protected $keyType = 'string';

    public $incrementing = false;

    protected $fillable = ['key', 'value'];

    /** @var array<string, string>|null */
    private static ?array $cache = null;

    // Retrieve a setting value by key, returning $default if not found.
    public static function get(string $key, string $default = ''): string
    {
        static::loadCache();

        return static::$cache[$key] ?? $default;
    }

    // Upsert a setting value by key.
    public static function set(string $key, string $value): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value]);
        static::$cache = null;
    }

    private static function loadCache(): void
    {
        if (static::$cache !== null) {
            return;
        }

        try {
            static::$cache = static::query()->pluck('value', 'key')->all();
        } catch (\Throwable) {
            static::$cache = [];
        }
    }
}
