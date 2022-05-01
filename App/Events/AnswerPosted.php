<?php

namespace App\Events;

use App\Models\QuestionAnswer;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AnswerPosted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    public $answer;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(QuestionAnswer $answer)
    {
        $this->answer = $answer;
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
