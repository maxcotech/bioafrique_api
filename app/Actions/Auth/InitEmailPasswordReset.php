<?php

namespace App\Actions\Auth;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Exceptions\ValidationError;
use App\Models\User;
use App\Traits\HasEmailNotifications;
use App\Traits\StringFormatter;

class InitEmailPasswordReset extends Action
{
    use HasEmailNotifications,StringFormatter;
    protected $request;
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    protected function validate()
    {
        $val = Validator::make($this->request->all(), [
            'email' => 'required|email|exists:users,email'
        ]);
        return $this->valResult($val);
    }

    protected function onSendResetToken(){
        $email = $this->request->email;
        $user = User::where('email',$email)->first();
        $this->sendEmailPasswordReset($user);
        return $this->successMessage("Password reset token has been sent to ".$this->obscureTextPart($email));
    }

    public function execute()
    {
        try {
            $val = $this->validate();
            if ($val['status'] !== "success") return $this->resp($val);
            return $this->onSendResetToken();
        } 
        catch(ValidationError $e){
            return  $this->validationError($e->getMessage());
        }
        catch (\Exception $e) {
            return $this->internalError($e->getMessage());
        }
    }
}
