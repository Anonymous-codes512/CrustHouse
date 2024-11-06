<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class LogExecutionTime
{
    public function handle(Request $request, Closure $next)
    {
        $startTime = microtime(true);

        $response = $next($request);

        $endTime = microtime(true);
        $executionTime = $endTime - $startTime;

        // Log or print execution time
        Log::info('Total Execution Time: ' . number_format($executionTime, 2) . 's');

        return $response;
    }
}
