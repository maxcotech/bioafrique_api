<?php

namespace App\Http\Controllers\Api;

use App\Actions\ShippingGroup\CreateShippingGroup;
use App\Actions\ShippingGroup\DeleteShippingGroup;
use App\Actions\ShippingGroup\GetShippingGroups;
use App\Actions\ShippingGroup\UpdateShippingGroup;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class ShippingGroupController extends Controller
{
    public function create(Request $request){
        return (new CreateShippingGroup($request))->execute();
    }

    public function index(Request $request){
        return (new GetShippingGroups($request))->execute();
    }

    public function update(Request $request){
        return (new UpdateShippingGroup($request))->execute();
    }

    public function delete(Request $request,$group_id){
        return (new DeleteShippingGroup($request,$group_id))->execute();
    }
}
