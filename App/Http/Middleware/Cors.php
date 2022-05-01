<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class Cors
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        /*$origin = $_SERVER['HTTP_REFERER'] ?? "*";
        Log::alert($origin);
        return $next($request)
            ->header('Access-Control-Allow-Origin', $origin)
            ->header('Access-Control-Allow-Methods', 'GET, POST, PUT, DELETE, OPTIONS');*/
        Log::alert($_SERVER['HTTP_ORIGIN'] ?? "No referer set");
        return $next($request);
    }
}
