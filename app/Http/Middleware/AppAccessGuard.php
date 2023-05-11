<?php

namespace App\Http\Middleware;

use App\Traits\HasHttpResponse;
use Closure;
use Illuminate\Http\Request;

class AppAccessGuard
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
        $user=$request->user();
        if(!isset($user)) return $this->validationError('invalid user access prohibited');
        if($user->account_status != 1){
            return $this->notAuthorized('Sorry, Your account has been suspended.');
        }
        return $next($request);
    }
}
