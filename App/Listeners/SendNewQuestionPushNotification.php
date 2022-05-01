<?php

namespace App\Listeners;

use App\Events\QuestionPosted;
use App\Services\PushServices;
use App\Traits\HasPushNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendNewQuestionPushNotification implements ShouldQueue
{
    use HasPushNotification;
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
     * @param  QuestionPosted  $event
     * @return void
     */
    public function handle(QuestionPosted $event)
    {
        $question = $event->question;
        $player_ids=$this->getDevicesFromUsers(2);
        if(count($player_ids) == 0) return false;
        (new PushServices())
        ->setHeadings('Plasma Tutorials')
        ->setSubTitle($question->user->full_name)
        ->setContents($question->question)
        ->setRoute('/admin-dashboard')
        ->sendPushNotification($player_ids);
    }
}
