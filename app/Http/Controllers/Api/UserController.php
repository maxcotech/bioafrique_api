<?php

namespace App\Http\Controllers\Api;

use App\Actions\User\DeleteUser;
use App\Actions\User\GetUserProfile;
use App\Actions\User\GetUsers;
use App\Actions\User\UpdateCurrency;
use App\Actions\User\UpdateUserStatus;
use App\Http\Controllers\Controller;
use App\Traits\HasHttpResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    use HasHttpResponse;
    public function show(){
        $user = Auth::user();
        return $this->successWithData($user);
    }
    public function getUserProfile(Request $request){
        return (new GetUserProfile($request))->execute();
    }

    public function updateUserCurrency(Request $request){
        return (new UpdateCurrency($request))->execute();
    }

    public function index(Request $request){
        return (new GetUsers($request))->execute();
    }

    public function updateUserStatus(Request $request){
        return (new UpdateUserStatus($request))->execute();
    }

    public function delete(Request $request,$user_id){
        return (new DeleteUser($request,$user_id))->execute();
    }
}
