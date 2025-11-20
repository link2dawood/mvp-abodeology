<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        $middleware->web([
            \App\Http\Middleware\SecurityHeaders::class,
        ]);

        // Register API middleware aliases
        $middleware->alias([
            'jwt.auth' => \App\Http\Middleware\JwtAuth::class,
            'role' => \App\Http\Middleware\CheckRole::class,
            'role.web' => \App\Http\Middleware\CheckWebRole::class,
            'ownership' => \App\Http\Middleware\CheckOwnership::class,
            'user.ownership' => \App\Http\Middleware\CheckUserOwnership::class,
            'api.ratelimit' => \App\Http\Middleware\ApiRateLimit::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        //
    })->create();
