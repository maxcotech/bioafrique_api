<?php
namespace App\Traits;

use Illuminate\Http\Request;

trait HasRoles{

    protected $roles = [
        'customer' => 1,
        'store_worker' => 10,
        'store_manager' => 11,
        'store_owner' => 12,
        'super_admin' => 24
    ];
    public function getUserRoleByKey($keyParam){
        foreach($this->roles as $key => $value){
            if($key == $keyParam){
                return $value;
            }
        }
        return null;
    }

    public function isInRoles($val){
        foreach($this->roles as $key => $value){
            if($value === $val){
                return true;
            }
        }
        return false;
    }

    public function getRoleTextById($role){
        foreach($this->roles as $key => $value){
            if($value == $role){
                return $key;
            }
        }
        return "N/A";
    }

    public function getUserRole(Request $request = null){
        $req = isset($request) ? $request: $this->request;
        if(isset($req)){
            $user = $req->user();
            if(isset($user)){
                return $user->user_type;
            } else {
                throw new \Exception('Could not retrieve user account');
            }
        } else {
            throw new \Exception('Could not get request instance.');
        }
    }

    public function isSuperAdmin($user_type = null){
        $type = (isset($user_type)) ? $user_type: $this->getUserRole();
        return $this->isRole('super_admin',$type);
    }
    public function isStoreOwner($user_type = null){
        $type = (isset($user_type)) ? $user_type: $this->getUserRole();
        return $this->isRole('store_owner',$type);
    }

    public function isStoreWorker($user_type = null){
        $type = (isset($user_type)) ? $user_type: $this->getUserRole();
        return $this->isRole('store_worker',$type);
    }
    public function isCustomer($user_type = null){
        $type = (isset($user_type)) ? $user_type: $this->getUserRole();
        return $this->isRole('customer',$type);
    }
    public function isStoreManager($user_type = null){
        $type = (isset($user_type)) ? $user_type: $this->getUserRole();
        return $this->isRole('store_manager',$type);
    }

    protected function isRole($key,$user_type){
        if($this->roles[$key] == $user_type){
            return true;
        } else {
            return false;
        }
    }
    public function getCustomerRoleId(){
        return $this->roles['customer'];
    }
    public function getStoreWorkerRoleId(){
        return $this->roles['store_worker'];
    }
    public function getStoreManagerRoleId(){
        return $this->roles['store_manager'];
    }
    public function getStoreOwnerRoleId(){
        return $this->roles['store_owner'];
    }
    public function getSuperAdminRoleId(){
        return $this->roles['super_admin'];
    }

}
    