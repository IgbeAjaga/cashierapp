<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
    /*
     * GLOBAL MIDDLEWARE
     * Runs on every request
     */
    $middleware->append(\Illuminate\Http\Middleware\HandleCors::class);
  $middleware->append(\Illuminate\Http\Middleware\TrustProxies::class);
   $middleware->append(\Illuminate\Foundation\Http\Middleware\TrimStrings::class);
    $middleware->append(\Illuminate\Foundation\Http\Middleware\ConvertEmptyStringsToNull::class);
    $middleware->append(\Illuminate\Foundation\Http\Middleware\ValidatePostSize::class);
    $middleware->append(\Illuminate\Foundation\Http\Middleware\CheckForMaintenanceMode::class);

    /*
     * ROUTE MIDDLEWARE (aliases)
     */
    $middleware->alias([
         'auth'       => \Illuminate\Auth\Middleware\Authenticate::class,
        'verified'   => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class,

        'approved'   => \App\Http\Middleware\EnsureUserIsApproved::class,
        'superadmin' => \App\Http\Middleware\EnsureSuperAdmin::class,
        'admin'      => \App\Http\Middleware\EnsureAdmin::class,
    ]);
})
    ->withExceptions(function (Exceptions $exceptions): void {
        // You can customize exception handling here if needed
    })
    ->create();
