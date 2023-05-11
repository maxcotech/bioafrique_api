<?php

namespace App\Http\Controllers\Api;

use App\Actions\StoreStaff\ChangeStaffPosition;
use App\Actions\StoreStaff\GetStoreStaffs;
use App\Actions\StoreStaff\GetStoreStaffType;
use App\Actions\StoreStaff\RemoveStoreStaff;
use App\Actions\StoreStaff\ToggleStaffStatus;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class StoreStaffController extends Controller
{
    public function getStoreStaffType(Request $request){
        return (new GetStoreStaffType($request))->execute();
    }

    public function getStoreStaffs(Request $request){
        return (new GetStoreStaffs($request))->execute();
    }

    public function changeStaffPosition(Request $request){
        return (new ChangeStaffPosition($request))->execute();
    }

    public function toggleStaffStatus(Request $request,$staff_id){
        return (new ToggleStaffStatus($request,$staff_id))->execute();
    }

    public function removeStoreStaff(Request $request,$staff_id){
        return (new RemoveStoreStaff($request,$staff_id))->execute();
    }
}
