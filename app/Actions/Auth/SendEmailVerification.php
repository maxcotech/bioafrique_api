<?php

namespace App\Actions\Auth;

use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use App\Actions\Action;
use App\Exceptions\ValidationError;
use App\Models\User;
use App\Traits\HasEmailNotifications;

class SendEmailVerification extends Action
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
            'email' => 'required|email|exists:users,email'
        ]);
        return $this->valResult($val);
    }

    protected function onSendEmail(){
        $user = User::where('email',$this->request->email)->first();
        if(isset($user)){
            $this->sendVerificationEmail($user);
        } else {
            throw new ValidationError('Invalid Email Address.');
        }
    }

    public function execute()
    {
        try {
            $val = $this->validate();
            if ($val['status'] !== "success") return $this->resp($val);
            $this->onSendEmail();
            return $this->successMessage('Verification Email code sent successfully.');
        } 
        catch(ValidationError $e){
            return $this->validationError($e->getMessage());
        }
        catch (\Exception $e) {
            return $this->internalError($e->getMessage());
        }
    }
}
