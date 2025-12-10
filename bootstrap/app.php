<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

use \App\Http\Middleware\TimeMiddleware;
use \App\Http\Middleware\PaymentAuthorizationMiddleware;
use \App\Http\Middleware\AdminMiddleware;
use \App\Http\Middleware\SetLocale;
use App\Http\Middleware\FarmMiddleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api_milisearch.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->web(append: [
            SetLocale::class,
        ]);

        $middleware->alias([
            'time' => TimeMiddleware::class,
            'PaymentAuthorizationMiddleware' => PaymentAuthorizationMiddleware::class,
            'admin' => AdminMiddleware::class,            
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
