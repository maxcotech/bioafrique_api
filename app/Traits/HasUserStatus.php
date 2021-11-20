<?php
namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait HasUserStatus{

    protected $user_status_list = [
        'active' => 1,
        'inactive' => 0,
        'read_only' => 2,
    ];

    protected function isUserActive($user_acct = null){
        $user = isset($user_acct)? $user_acct : Auth::user();
        if(isset($user)){
            return $this->isValue('active',$user->account_status);
        } 
        return false;
    }

    protected function getUserStatusText($status){
        foreach($this->user_status_list as $key => $value){
            if($status == $value){
                return $key;
            }
        }
        return "N/A";
    }

    protected function getActiveUserId(){
        return $this->user_status_list['active'];
    }
    protected function getInactiveUserId(){
        return $this->user_status_list['inactive'];
    }
    protected function getReadOnlyUserId(){
        return $this->user_status_list['read_only'];
    }
    protected function isValue($key,$value){
        return $this->user_status_list[$key] == $value;
    }

}
    