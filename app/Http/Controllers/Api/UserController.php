<?php

namespace App\Http\Controllers\Api;

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
}
