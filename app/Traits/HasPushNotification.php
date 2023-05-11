<?php
namespace App\Traits;

use App\Models\User;

trait HasPushNotification{
    protected function filterDeviceFromUsers($users){
        $player_ids=[];
        if(!isset($users) || empty($users)) return [];
        foreach($users as $user){
            $device_record=$user->userDevice;
            if(isset($device_record)){
                array_push($player_ids,$device_record->device_id);
            }
        }
        return $player_ids;
    }
    protected function getDevicesFromUsers($user_type = 1){
        $query=User::with('userDevice');
        if(isset($user_type) && !empty($user_type)){
            $query->where('user_type',$user_type);
        }
        $users=$query->where('account_status',1)
        ->get();
        $player_ids=$this->filterDeviceFromUsers($users);
        return $player_ids;
    }


}
