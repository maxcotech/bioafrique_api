<?php

namespace App\Http\Controllers\Api;

use App\Actions\StoreStaffToken\CreateStoreStaffToken;
use App\Actions\StoreStaffToken\DeleteStaffToken;
use App\Actions\StoreStaffToken\GetStoreStaffTokens;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StoreStaffTokenController extends Controller
{
    public function create(Request $request){
        return (new CreateStoreStaffToken($request))->execute();
    }
    public function index(Request $request){
        return (new GetStoreStaffTokens($request))->execute();
    }
    public function delete(Request $request,$id){
        return (new DeleteStaffToken($request,$id))->execute();
    }
}
