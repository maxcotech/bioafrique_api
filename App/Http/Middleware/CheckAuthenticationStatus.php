<?php

namespace App\Http\Middleware;

use App\Traits\HasHttpResponse;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CheckAuthenticationStatus
{
    use HasHttpResponse;

    protected $request;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        Log::alert('Middleware: check authentication status');
        $this->request = $request;
        if(Auth::user() == null){
            return $this->notAuthorized('You need to login in order to proceed.');
        }
        return $next($request);
    }
}
