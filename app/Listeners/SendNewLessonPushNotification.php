<?php

namespace App\Listeners;

use App\Events\CourseLessonPosted;
use App\Models\User;
use App\Services\PushServices;
use App\Traits\HasPushNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendNewLessonPushNotification implements ShouldQueue
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
     * @param  CourseLessonPosted  $event
     * @return void
     */


    public function handle(CourseLessonPosted $event)
    {
        $lesson=$event->lesson;
        $player_ids=$this->getDevicesFromUsers(1);
        if(count($player_ids) == 0) return false;
        (new PushServices())
        ->setHeadings('Plasma Tutorials')
        ->setSubTitle('New Lesson Posted.')
        ->setContents('New lesson on '.$lesson->lesson_title.' was just posted.')
        ->setRoute('/course-selection')
        ->sendPushNotification($player_ids);

    }
}
