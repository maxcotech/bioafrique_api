@component('mail::message',['name'=>$name,'password' => $password,'email_address' => $email_address])
# Welcome on board
Hi {{$name}},<br>
Welcome to {{ config('app.name') }}. An admin account has been created for you , please use the following details to login.
@component('mail::panel')
    <p>Password: {{$password}}</p>
    <p>Username(email): {{$email_address}}</p>
@endcomponent
@component('mail::subcopy')
If you do not know anything about this, send us a mail at support@cozeller.com<br>
Thanks, 
{{ config('app.name') }}
@endcomponent

@endcomponent
