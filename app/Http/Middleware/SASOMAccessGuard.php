<?php

namespace App\Http\Middleware;

use App\Traits\HasHttpResponse;
use App\Traits\HasRoles;
use Closure;
use Illuminate\Http\Request;

class SASOMAccessGuard
{
    use HasRoles,HasHttpResponse;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    /**
     * restricts access to super admin, store owner and store manager
     */
    public function handle(Request $request, Closure $next)
    {
        $user = $request->user();
        $user_type = isset($user)? $user->user_type : null;
        if($this->isSuperAdmin($user_type) || $this->isStoreOwner($user_type) || $this->isStoreManager($user_type)){
            return $next($request);
        }
        return $this->notAuthorized("You are not authorized to make this request. Please contact a superior or an admin.");
    }
}
