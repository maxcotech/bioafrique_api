<?php

namespace App\Http\Middleware;

use App\Traits\HasAccessCookie;
use App\Traits\HasHttpResponse;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class SetAccessCookie
{
    use HasAccessCookie,HasHttpResponse;
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
        Log::alert('Accessing setAccessCookie');
        $this->request = $request;
        if($this->isEligibleForNewCookie()){
            $cookie = $this->saveCookie();
            $cookie_payload = $this->getCookiePayload($cookie);
            $this->request->headers->add([
                "X-basic_access" => $cookie->cookie_value
            ]);
            $resp = $next($this->request);
            return $resp->withCookie($cookie_payload);
        } else {
            return $next($this->request);
        }
    }
}
