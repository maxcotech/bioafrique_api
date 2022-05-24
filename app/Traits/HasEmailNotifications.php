<?php
namespace App\Traits;

use App\Exceptions\ValidationError;
use App\Mail\SendEmailVerificationCode;
use App\Mail\SendResetPasswordToken;
use App\Models\OneTimePassword;
use Illuminate\Support\Facades\Mail;

trait HasEmailNotifications{
    use TokenGenerator,HasEncryption;
    protected $token_validity_duration_in_mins = 30;

    protected function getEmailTokenPassphrase(){
        return env('EMAIL_TOKEN_PASSPHRASE');
    }

    public function generateEmailToken($tokenLength = 6){
        while(true){
            $token = $this->createNumberToken($tokenLength);
            $encrypted_token = $this->encryptData($token,$this->getEmailTokenPassphrase());
            if(!OneTimePassword::where('password',$encrypted_token)->exists()){
                return [
                    'encrypted' => $encrypted_token,
                    'raw_token' => $token
                ];
            }
        }
    }

    public function sendVerificationEmail($user){
       $this->sendUserEmail($user,OneTimePassword::purposes[0]);
    }

    public function sendEmailPasswordReset($user){
        $this->sendUserEmail($user,OneTimePassword::purposes[1]);
    }

    protected function sendUserEmail($user,$purpose){
        $name = ($user->first_name == null)? "Dear":$user->first_name;
        $token_data = $this->generateEmailToken();
        OneTimePassword::where('email',$user->email)->where('purpose',$purpose)->delete();
        switch($purpose){
            case OneTimePassword::purposes[0]: Mail::to($user->email)->send(new SendEmailVerificationCode(
                $user->email,$name,$token_data['raw_token']
            )); break;
            case OneTimePassword::purposes[1]: Mail::to($user->email)->send(new SendResetPasswordToken(
                $user->email,$name,$token_data['raw_token']
            )); break;
            default: throw new ValidationError('invalid email purpose initiated.');
        }
        OneTimePassword::create([
            'purpose' => $purpose,
            'receiver_type' => OneTimePassword::receiver_types[1],
            'email' => $user->email,
            'password' => $token_data['encrypted'],
            'expiry' => now()->addMinutes($this->token_validity_duration_in_mins)
        ]);
    }


}
    