<?php

namespace App\Http\Middleware;

use App\Traits\HasHttpResponse;
use App\Traits\HasRoles;
use App\Traits\HasUserStatus;
use Closure;
use Illuminate\Http\Request;

class StoreOwnerAccessGuard
{
    use HasHttpResponse,HasRoles,HasUserStatus;
    protected $response;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $this->request = $request;
        if($this->isStoreOwner() && $this->isUserActive($this->request->user())){
            return $next($request);
        } else {
            return $this->notAuthorized('You are not eligible to carry out this operation. please contact the super admin.');
        }
    }
}
