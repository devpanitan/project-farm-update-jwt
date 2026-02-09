<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
    )
    ->withProviders([
        App\Providers\BroadcastServiceProvider::class,
    ])
    ->withMiddleware(function (Middleware $middleware): void {
        // Trust all proxies using the correct positional argument syntax.
        $middleware->trustProxies('*');

        $middleware->web(prepend: [
            \App\Http\Middleware\Cors::class,
        ]);

        $middleware->api(prepend: [
            \App\Http\Middleware\Cors::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
