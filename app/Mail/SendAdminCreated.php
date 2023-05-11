<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class SendAdminCreated extends Mailable
{
    use Queueable, SerializesModels;
    public $email_address;
    public $name;
    public $password;

    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($email_address,$name,$password)
    {
        $this->email_address = $email_address;
        $this->name = $name;
        $this->password = $password;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject("Admin Account Created")->markdown('emails.admin_created');
    }
}
