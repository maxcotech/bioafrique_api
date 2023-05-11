<?php
namespace App\Traits;

use App\Models\StoreStaff;
use Illuminate\Support\Facades\Log;

trait HasStoreRoles{
    use HasUserStatus;

    protected $store_roles = [
        'store_worker' => 10,
        'store_manager' => 11,
        'store_owner' => 12
    ];

    protected function inStoreStaffRoles($type){
        foreach($this->store_roles as $role){
            if($role == $type){
                return true;
            }
        }
        return false;
    }

    protected function getStoreRoleText($type){
        foreach($this->store_roles as $key => $value){
            if($value == $type){
                return $key;
            }
        }
        return "N/A";
    }

    protected function getStoreRoleId($user_id ,$store_id){
        $staff = StoreStaff::where('user_id',$user_id)
        ->where('store_id',$store_id)->first();
        if(isset($staff)){
            return $staff->staff_type;
        } 
        return null;
    }

    protected function getStoreWorkerId(){
        return $this->store_roles['store_worker'];
    }
    protected function getStoreManagerId(){
        return $this->store_roles['store_manager'];
    }

    protected function isStoreWorker($user_id,$store_id){
        return StoreStaff::where('user_id',$user_id)
        ->where('store_id',$store_id)
        ->where('staff_type',$this->store_roles['store_worker'])
        ->exists();
    }

    protected function isStoreManager($user_id,$store_id){
        return StoreStaff::where('user_id',$user_id)
        ->where('store_id',$store_id)
        ->where('staff_type',$this->store_roles['store_manager'])
        ->exists();
    }

    protected function isActiveStoreManager($user_id,$store_id){
        if(StoreStaff::where('user_id', $user_id)
        ->where('store_id', $store_id)
        ->where('staff_type', $this->store_roles['store_manager'])
        ->where('status', $this->getActiveUserId())
        ->exists()){
            return true;
        }
        return false;
    }

    protected function isActiveStoreWorker($user_id,$store_id){
        return StoreStaff::where('user_id',$user_id)
        ->where('store_id',$store_id)
        ->where('staff_type',$this->store_roles['store_worker'])
        ->where('status',$this->getActiveUserId())
        ->exists();
    }

}
    