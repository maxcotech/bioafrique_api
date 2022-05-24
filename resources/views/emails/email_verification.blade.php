@component('mail::message',['name'=>$name,'token' => $token,'email_address' => $email_address])
# Email Verification
Hi {{$name}},<br>
use the following one time password to verify your email, inorder to continue as authenticated user in {{ config('app.name') }}
@component('mail::panel')
    {{$token}}
@endcomponent
@component('mail::subcopy')
Ignore this if this request was not initiated by you or for your account.<br>
Thanks, 
{{ config('app.name') }}
@endcomponent

@endcomponent
