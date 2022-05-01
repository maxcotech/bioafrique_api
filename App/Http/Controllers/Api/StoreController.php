<?php

namespace App\Http\Controllers\Api;

use App\Actions\Store\GetDashboardData;
use App\Actions\Store\AddUserToStore;
use App\Actions\Store\CreateStore;
use App\Actions\Store\DeleteStore;
use App\Actions\Store\GetStores;
use App\Actions\Store\GetWallet;
use App\Actions\Store\SearchStore;
use App\Actions\Store\UpdateStore;
use App\Actions\Store\UpdateStoreStatus;
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
    public function index(Request $request){
        return (new GetStores($request))->execute();
    }
    public function updateStoreStatus(Request $request){
        return (new UpdateStoreStatus($request))->execute();
    }
    public function delete(Request $request,$store_id){
        return (new DeleteStore($request,$store_id))->execute();
    }
    public function getWallet(Request $request){
        return (new GetWallet($request))->execute();
    }
    public function getDashboardData(Request $request){
        return (new GetDashboardData($request))->execute();
    }
}
