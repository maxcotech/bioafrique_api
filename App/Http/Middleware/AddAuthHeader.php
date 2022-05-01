<?php

namespace App\Http\Middleware;

use App\Traits\HasHttpResponse;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AddAuthHeader
{
    use HasHttpResponse;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        Log::alert('Middleware: Add Auth Header if token exists');
        if($request->bearerToken() == null){
            if($request->hasCookie('_token')){
                $request->headers->add([
                    'Authorization' => 'Bearer '.$request->cookie('_token')
                ]);
            }
        }
        return $next($request);
    }
}
