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
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
           'log.execution' => \App\Http\Middleware\LogExecutionTime::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })
    ->withCommands(commands: [
        \App\Console\Commands\ClearLogs::class,  // Register the ClearLogs command
        \App\Console\Commands\SyncData::class,  // Register the SyncData command
        \App\Console\Commands\ListModels::class,
    ])->create();
