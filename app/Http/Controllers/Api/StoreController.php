<?php

namespace App\Http\Controllers\Api;

use App\Actions\Store\AddUserToStore;
use App\Actions\Store\CreateStore;
use App\Actions\Store\SearchStore;
use App\Actions\Store\UpdateStore;
use App\Actions\Store\UploadStoreLogo;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StoreController extends Controller
{
    public function create(Request $request){
        return (new CreateStore($request))->execute();
    }
    public function update(Request $request){
        return (new UpdateStore($request))->execute();
    }
    public function search(Request $request){
        return (new SearchStore($request))->execute();
    }
    public function addUserToStore(Request $request){
        return (new AddUserToStore($request))->execute();
    }
    public function uploadStoreLogo(Request $request){
        return (new UploadStoreLogo($request))->execute();
    }
}
