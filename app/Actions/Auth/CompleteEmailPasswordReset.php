<?php

namespace App\Actions\Auth;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Exceptions\ValidationError;
use App\Models\OneTimePassword;
use App\Models\User;
use App\Traits\HasEmailNotifications;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class CompleteEmailPasswordReset extends Action
{
    use HasEmailNotifications;
    protected $request;
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    protected function validate()
    {
        $val = Validator::make($this->request->all(), [
            'token' => 'required|string',
            'email' => 'required|email|exists:users,email',
            'new_password' => 'required|string|min:8',
            'confirm_password' => 'required|same:new_password'
        ]);
        return $this->valResult($val);
    }

    protected function onResetPassword(){
        $email = $this->request->email;
        $encrypted_token = $this->encryptData($this->request->token,$this->getEmailTokenPassphrase());
        $user = User::where('email',$email)->first();
        $token_obj = OneTimePassword::where('password',$encrypted_token)
        ->where('email',$email)
        ->where('receiver_type',OneTimePassword::receiver_types[1])
        ->where('purpose',OneTimePassword::purposes[1])->first();
        if(!isset($token_obj)) throw new ValidationError("The token you entered is invalid.");
        if($token_obj->isExpired()) throw new ValidationError("The token you entered has expired");
        DB::transaction(function()use(&$user,&$token_obj){
            $user->password = Hash::make($this->request->new_password);
            $user->save();
            $token_obj->delete();
        });
    }

    public function execute()
    {
        try {
            $val = $this->validate();
            if ($val['status'] !== "success") return $this->resp($val);
            $this->onResetPassword();
            return $this->successMessage('Password has been successfully changed.');
        } catch (\Exception $e) {
            return $this->internalError($e->getMessage());
        }
    }
}
