<?php

namespace App\Http\Middleware;

use App\Traits\HasHttpResponse;
use Closure;
use Illuminate\Http\Request;

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
        if($request->bearerToken() == null){
            if($request->hasCookie('_token')){
                $request->headers->add([
                    'Authorization' => 'Bearer '.$request->cookie('_token')
                ]);
                return $next($request);
            }else{
                return $this->notAuthorized('You need to login in order to proceed.');
            }
        }else{
            return $next($request);
        }
        
    }
}
