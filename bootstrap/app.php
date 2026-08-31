<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->trustProxies(at: '*');

        $middleware->web(append: [
            \App\Http\Middleware\SetLocale::class,
            \App\Http\Middleware\DetectMobile::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        $exceptions->reportable(function (\Throwable $e): void {
            if (env('VERCEL') || env('VERCEL_ENV')) {
                error_log('[budget-kit] '.$e->getMessage());
                error_log($e->getFile().':'.$e->getLine());
            }
        });

        $exceptions->render(function (\Throwable $e) {
            if (env('VERCEL') || env('VERCEL_ENV')) {
                return response(
                    'Error: '.$e->getMessage()."\n\n".
                    $e->getFile().':'.$e->getLine()."\n\n".
                    $e->getTraceAsString(),
                    500,
                    ['Content-Type' => 'text/plain; charset=utf-8']
                );
            }
        });
    })->create();
