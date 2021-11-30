<?php

namespace App\Http\Controllers\Api;

use App\Actions\User\GetUserProfile;
use App\Actions\User\UpdateCurrency;
use App\Http\Controllers\Controller;
use App\Traits\HasHttpResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    use HasHttpResponse;
    public function show(Request $request){
        $user = Auth::user();
        return $this->successWithData($user);
    }
    public function getUserProfile(Request $request){
        return (new GetUserProfile($request))->execute();
    }

    public function updateUserCurrency(Request $request){
        return (new UpdateCurrency($request))->execute();
    }
}
