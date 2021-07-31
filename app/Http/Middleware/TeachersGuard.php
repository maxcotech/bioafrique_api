<?php

namespace App\Http\Middleware;

use App\Traits\HasHttpResponse;
use Closure;
use Illuminate\Http\Request;

class TeachersGuard
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
        if(!isset($user)) return $this->validationError('Invalid user access is prohibited.');
        if($user->user_type != 2){
            return $this->notAuthorized();
        }
        return $next($request);
    }

}
