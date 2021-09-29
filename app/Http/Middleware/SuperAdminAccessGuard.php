<?php

namespace App\Http\Middleware;

use App\Traits\HasHttpResponse;
use App\Traits\HasRoles;
use Closure;
use Illuminate\Http\Request;

class SuperAdminAccessGuard
{
    use HasRoles,HasHttpResponse;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $user_type = $this->getUserRole($request);
        if($this->isSuperAdmin($user_type)){
            return $next($request);
        } else {
            return $this->notAuthorized("You are not authorized to carry out this operation, please contact super admin.");
        }
    }
}
