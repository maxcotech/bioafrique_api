@component('mail::message',['name'=>$name,'token' => $token,'email_address' => $email_address])
# Password Reset
Hi {{$name}},<br>
use the following token to reset your password, inorder to continue as an authenticated user in {{ config('app.name') }}
@component('mail::panel')
    {{$token}}
@endcomponent
@component('mail::subcopy')
Ignore this if this request was not initiated by you or for your account.<br>
Thanks, 
{{ config('app.name') }}
@endcomponent

@endcomponent
