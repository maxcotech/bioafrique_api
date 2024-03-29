<?php
namespace App\Traits;

use Illuminate\Http\Request;

trait HasRoles{

    protected $roles = [
        'customer' => 1,
        'store_staff' => 10,
        'store_owner' => 12,
        'admin' => 20,
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
        $req = isset($request) ? $request: request();
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

    public function isSuperAdmin($user_type = null, $strict = false){
        $type = (isset($user_type)) ? $user_type: $this->getUserRole();
        if($strict == true){
            return $this->isRole('super_admin',$type);
        }
        return ($this->isRole('super_admin',$type) || $this->isRole('admin',$type));
    }
    
    public function isStoreOwner($user_type = null){
        $type = (isset($user_type)) ? $user_type: $this->getUserRole();
        return $this->isRole('store_owner',$type);
    }

    public function isStoreStaff($user_type = null){
        $type = (isset($user_type)) ? $user_type: $this->getUserRole();
        return $this->isRole('store_staff',$type);
    }
    public function isCustomer($user_type = null){
        $type = (isset($user_type)) ? $user_type: $this->getUserRole();
        return $this->isRole('customer',$type);
    }

    public function isAdmin($user_type = null){
        $type = (isset($user_type)) ? $user_type: $this->getUserRole();
        return $this->isRole('admin',$type);
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
    public function getStoreStaffRoleId(){
        return $this->roles['store_staff'];
    }
    public function getStoreOwnerRoleId(){
        return $this->roles['store_owner'];
    }
    public function getSuperAdminRoleId(){
        return $this->roles['super_admin'];
    }

    public function getAdminRoleId(){
        return $this->roles['admin'];
    }

}
    