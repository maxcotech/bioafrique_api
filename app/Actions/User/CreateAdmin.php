<?php

namespace App\Actions\User;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Models\User;
use App\Traits\HasEmailNotifications;
use App\Traits\HasRoles;
use App\Traits\TokenGenerator;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CreateAdmin extends Action
{
    use TokenGenerator, HasRoles, HasEmailNotifications;
    protected $request;
    protected User $user;
    public function __construct(Request $request)
    {
        $this->request = $request;
        $this->user = $request->user();
    }

    protected function validate()
    {
        $val = Validator::make($this->request->all(), [
            'first_name' => "required|string",
            'last_name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'phone_number' => 'nullable|numeric',
            'telephone_code' => 'nullable|string',
            'password' => 'nullable|string|min:8'
        ]);
        return $this->valResult($val);
    }

    protected function onCreateAdmin(){
        DB::transaction(function(){
            $data = $this->request->all(['first_name','last_name','email','telephone_code','password','phone_number']);
            $password = $this->request->input('password',$this->generatePassword());
            $data['password'] = Hash::make($password);
            $data['user_type'] = $this->getAdminRoleId();
            $user = User::create($data);
            $this->sendNewAdminEmail($user,$password);
        });
    }

    public function execute()
    {
        try {
            $val = $this->validate();
            if ($val['status'] !== "success") return $this->resp($val);
            $this->onCreateAdmin();
            return $this->successMessage("New Admin Account Created Successfully.");
        } catch (\Exception $e) {
            return $this->internalError($e->getMessage());
        }
    }
}
