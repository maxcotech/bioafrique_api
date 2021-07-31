<?php

namespace App\Listeners;

use App\Events\AnswerPosted;
use App\Models\User;
use App\Services\PushServices;
use App\Traits\HasPushNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendNewAnswerPushNotification implements ShouldQueue
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
     * @param  AnswerPosted  $event
     * @return void
     */
    protected function getRecipientDevice($answer){
        $user=User::find($answer->question->user->id);
        $player_ids=$this->filterDeviceFromUsers([$user]);
        return $player_ids;
    }
    public function handle(AnswerPosted $event)
    {
        $answer=$event->answer;
        $player_ids=$this->getRecipientDevice($answer);
        if(count($player_ids) == 0) return false;
        (new PushServices())
        ->setHeadings('Plasma Tutorials')
        ->setSubTitle('Admin replied your question.')
        ->setContents($answer->answer)
        ->setRoute('/course-selection')
        ->sendPushNotification($player_ids);
    }
}
