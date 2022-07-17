<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Traits\HasPermissions;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    use HasPermissions;
    /**
     * Run the database seeds.
     *
     * @return void
     */

    public function permissionExists($existing,$permission){
        if(count($existing) > 0){
            foreach($existing as $existing_perm){
                if($existing_perm->name == $permission){
                    return true;
                }
            }
        }
        return false;
    }
    public function run()
    {
        $permission_names = $this->getAllPermissionNames();
        $existing = Permission::whereIn('name',$permission_names)->get();
        $upload_data = [];
        $time = now();
        foreach($permission_names as $permission){
            if(!$this->permissionExists($existing,$permission)){
                array_push($upload_data,[
                    'name' => $permission,
                    'created_at' => $time,
                    'updated_at' => $time
                ]);
            }
        }
        if(count($upload_data) > 0){
            Permission::insert($upload_data);
        }
    }
}
