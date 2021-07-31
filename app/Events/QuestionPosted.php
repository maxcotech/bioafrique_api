<?php

namespace App\Events;

use App\Models\LessonQuestion;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class QuestionPosted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $question;
    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(LessonQuestion $question)
    {
        $this->question=$question;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('channel-name');
    }
}
