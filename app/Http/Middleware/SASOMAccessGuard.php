<?php

namespace App\Http\Middleware;

use App\Models\Store;
use App\Traits\HasHttpResponse;
use App\Traits\HasRoles;
use App\Traits\HasStore;
use App\Traits\HasStoreRoles;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SASOMAccessGuard
{
    use HasRoles,HasHttpResponse,HasStore,HasStoreRoles;
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    /**
     * restricts access to super admin, store owner and store staffs
     */
    public function handle(Request $request, Closure $next, string $permission = null)
    {
        $user = $request->user();
        $user_type = isset($user)? $user->user_type : null;
        if(($this->isSuperAdmin($user_type, true) || ($this->isAdmin() && $user->hasPermissionTo($permission)) || $this->isStoreOwner($user_type)) && $this->isUserActive($user)){
            return $next($request);
        } else if($this->isStoreStaff($user_type) && $this->isUserActive($user)){
            $store_index = $this->getStoreIndexFromRequest($request);
            if(isset($store_index)){
                $store = Store::where($store_index['key'],$store_index['value'])->first();
                if(isset($store) && $this->isActiveStoreManager($user->id,$store->id)){
                    return $next($request);
                } 
            }
        }
        return $this->notAuthorized("You are not authorized to make this request. Please contact a superior or an admin.");
    }
}
