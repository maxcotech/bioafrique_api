<?php
namespace App\Traits;

use App\Models\Permission;
use App\Models\UserPermission;
use Exception;
use Illuminate\Support\Facades\DB;

trait HasPermissions{
    public function getAllPermissionNames(){
        return [
            'manage.admins',
            'manage.users',
            'manage.products',
            'manage.stores',
            'manage.brands',
            'manage.funds',
            'view.funds',
            'manage.categories',
            'manage.locations',
            'manage.currencies',
            'manage.widgets',
            'manage.admin_preferences',
            'view.billing_details',
            'manage.user_permissions'
        ];
    }

    public function hasPermissionTo($names){
        if($names == null) return true;
        if(is_string($names)){
            if(str_contains($names,"-")){
                $exploded_names = explode("-",$names);
                return $this->permissions()->whereIn('name',$exploded_names)->exists();
            }
            return $this->permissions()->where('name',$names)->exists();
        } elseif (is_array($names)){
            return $this->permissions()->whereIn('name',$names)->exists();
        } else {
            throw new Exception("Parameter passed for permissions not supported");
        }
    }

    public function hasAllPermissions(array $names){
        if(count($names) == 0) return true;
        $user_permissions = $this->permissions;
        foreach($names as $name){
            $has_permission = false;
            foreach($user_permissions as $permission){
                if($permission->name == $name){
                    $has_permission = true;
                }
            }
            if($has_permission == false){
                return false;
            }
        }
        return true;

    }



    public function syncPermissions($permission_names){
        DB::transaction(function()use($permission_names){
            UserPermission::where('user_id',$this->id)->delete();
            if(count($permission_names) > 0){
                $permissions = Permission::whereIn('name',$permission_names)->get();
                $upload_data = [];
                $time = now();
                foreach($permissions as $permission){
                    array_push($upload_data,[
                        'user_id' => $this->id,
                        'permission_id' => $permission->id,
                        'created_at' => $time,
                        'updated_at' => $time
                    ]);
                }
                UserPermission::insert($upload_data);
            }
        });
    }

    

}
    