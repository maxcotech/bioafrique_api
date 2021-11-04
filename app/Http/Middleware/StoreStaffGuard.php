<?php

namespace App\Http\Middleware;

use App\Traits\HasHttpResponse;
use App\Traits\HasRoles;
use App\Traits\HasStore;
use App\Traits\HasUserStatus;
use Closure;
use Illuminate\Http\Request;

class StoreStaffGuard
{
    use HasRoles,HasHttpResponse,HasStore,HasUserStatus;
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
        $this->request = $request;
        if(($this->isStoreStaff() || $this->isStoreOwner()) && $this->userHasStore() && $this->isUserActive($request->user())){
            return $next($request);
        } else {
            return $this->notAuthorized('You are not authorized to carry out this operation, please contact super admin or your superior.');
        }
    }
}
