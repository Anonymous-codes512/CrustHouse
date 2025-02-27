<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
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
        \App\Console\Commands\ListModels::class,
        \App\Console\Commands\ClearLogs::class,
        \App\Console\Commands\SyncData::class,
        \App\Console\Commands\SyncUserData::class,
        \App\Console\Commands\SyncCategoryData::class,
        \App\Console\Commands\SyncProductData::class,
        \App\Console\Commands\SyncDealData::class,
        \App\Console\Commands\SyncRecipeData::class,
        \App\Console\Commands\SyncStockData::class,
        \App\Console\Commands\SyncRiderData::class,
        \App\Console\Commands\SyncOtherData::class,
    ])->create();