<?php

namespace App\Http\Controllers\Api;

use App\Actions\Auth\GetUserIPAddress;
use App\Actions\Auth\LoginUser;
use App\Actions\Auth\RegisterUser;
use App\Http\Controllers\Controller;
use App\Traits\HasHttpResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cookie;

class AuthController extends Controller
{
    use HasHttpResponse;
    public function register(Request $request){
        return (new RegisterUser($request))->execute();
    }
    public function login(Request $request){
        return (new LoginUser($request))->execute();
    }
    public function logout(Request $request){
        $request->user()->token()->revoke();
        $cookie = Cookie::forget('_token');
        return $this->successMessage('Logout was successful')->withCookie($cookie);
    }
    public function getUserIpAddress(Request $request){
        return (new GetUserIPAddress($request))->execute();
    }
}
