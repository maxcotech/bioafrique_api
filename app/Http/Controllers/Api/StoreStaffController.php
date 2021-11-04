<?php

namespace App\Http\Controllers\Api;

use App\Actions\StoreStaff\GetStoreStaffType;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StoreStaffController extends Controller
{
    public function getStoreStaffType(Request $request){
        return (new GetStoreStaffType($request))->execute();
    }
}
