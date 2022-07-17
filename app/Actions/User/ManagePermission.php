<?php

namespace App\Actions\User;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\User;

class ManagePermission extends Action
{
    protected Request $request;
    protected User $user;
    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->user = $request->user();
    }

    protected function validate()
    {
        $val = Validator::make($this->request->all(), [
            'permissions' => 'required|json',
            'id' => 'required|integer|exists:users,id'
        ]);
        return $this->valResult($val);
    }

    protected function userCanAssignPermissions($permissions){
        if(!$this->user->isSuperAdmin(null,true)){
            if($this->user->id !== $this->request->id && $this->user->hasAllPermissions($permissions)){
                return true;
            }
            return false;
        }
        return true;
    }

    public function execute()
    {
        try {
            $val = $this->validate();
            if ($val['status'] !== "success") return $this->resp($val);
            $permissions = json_decode($this->request->permissions,true);
            $user = User::find($this->request->id);
            if($this->userCanAssignPermissions($permissions)){
                $user->syncPermissions($permissions);
                return $this->successMessage("Permissions Updated for the selected user");
            } else {
                return $this->notAuthorized("Either you tried to assign permissions to yourself, or you tried to assign permission that you dont have to the selected user.");
            }
            
        } 
        catch (\Exception $e) {
            return $this->internalError($e->getMessage());
        }
    }
}
