<?php

namespace App\Listeners;

use App\Events\NewPayment;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendNewPaymentPushNotification
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  NewPayment  $event
     * @return void
     */
    public function handle(NewPayment $event)
    {
        //
    }
}
