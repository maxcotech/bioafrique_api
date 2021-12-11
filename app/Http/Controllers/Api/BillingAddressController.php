<?php

namespace App\Http\Controllers\Api;

use App\Actions\BillingAddress\ChangeCurrentAddress;
use App\Actions\BillingAddress\CreateBillingAddress;
use App\Actions\BillingAddress\DeleteBillingAddress;
use App\Actions\BillingAddress\GetBillingAddresses;
use App\Actions\BillingAddress\UpdateBillingAddress;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class BillingAddressController extends Controller
{
    public function create(Request $request){
        return (new CreateBillingAddress($request))->execute();
    }
    public function update(Request $request){
        return (new UpdateBillingAddress($request))->execute();
    }
    public function delete(Request $request,$address_id){
        return (new DeleteBillingAddress($request,$address_id))->execute();
    }
    public function index(Request $request){
        return (new GetBillingAddresses($request))->execute();
    }
    public function changeCurrentAddress(Request $request,$address_id){
        return (new ChangeCurrentAddress($request,$address_id))->execute();
    }
}
