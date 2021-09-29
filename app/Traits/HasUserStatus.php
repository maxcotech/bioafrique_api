<?php
namespace App\Traits;

use Illuminate\Support\Facades\Auth;

trait HasUserStatus{

    protected $status_list = [
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

    protected function getActiveUserId(){
        return $this->status_list['active'];
    }
    protected function getInactiveUserId(){
        return $this->status_list['inactive'];
    }
    protected function getReadOnlyUserId(){
        return $this->status_list['read_only'];
    }
    protected function isValue($key,$value){
        return $this->status_list[$key] == $value;
    }

}
    