<?php
namespace App\Actions\Auth;

use Illuminate\Http\Request;
use App\Actions\Action;
use App\Exceptions\ValidationError;
use App\Models\OneTimePassword;
use App\Models\User;
use App\Traits\HasEmailNotifications;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class CompleteEmailVerification extends Action{
    use HasEmailNotifications;

    protected $request;
    public function __construct(Request $request){
        $this->request=$request;
    }

    protected function validate(){
        $val = Validator::make($this->request->all(),[
            'email' => 'required|email|exists:users,email',
            'password' => 'required|string'
        ]);
        return $this->valResult($val);
    }

    protected function onCompleteVerification(){
        $input_pass = $this->request->password;
        $encrypted_pass = $this->encryptData($input_pass,$this->getEmailTokenPassphrase());
        $user_acct = User::where('email',$this->request->email)->first();
        $otp_model = OneTimePassword::where('password',$encrypted_pass)
        ->where('email',$user_acct->email)
        ->where('purpose',OneTimePassword::purposes[0])->first();
        if(!isset($otp_model)) throw new ValidationError("The token you entered is invalid.");
        if($otp_model->isExpired()) throw new ValidationError('The token you entered has expired, please request for new token and try again.');
        DB::transaction(function()use(&$user_acct,&$otp_model){
            $user_acct->email_verified_at = now();
            $user_acct->save();
            $otp_model->delete();
        });
    }
    
    public function execute(){
        try{
            $val = $this->validate();
            if($val['status'] !== "success") return $this->resp($val);
            $this->onCompleteVerification();
            return $this->successMessage('Email verification completed successfully.');
        }
        catch(ValidationError $e){
            return $this->validationError($e->getMessage());
        }
        catch(\Exception $e){
            return $this->internalError($e->getMessage());
        }
    }

}
    