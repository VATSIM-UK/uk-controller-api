<?php

namespace App\Events\Prenote;

use App\Events\HighPriorityBroadcastEvent;
use App\Models\Prenote\PrenoteMessage;
use Illuminate\Broadcasting\PrivateChannel;

class PrenoteAcknowledgedEvent extends HighPriorityBroadcastEvent
{
    private PrenoteMessage $message;

    public function __construct(PrenoteMessage $message)
    {
        $this->message = $message;
    }

    public function broadcastWith()
    {
        return [
            'id' => $this->message->id,
        ];
    }

    public function broadcastOn()
    {
        return [new PrivateChannel('prenote-messages')];
    }

    public function broadcastAs()
    {
        return 'prenote-message.acknowledged';
    }
}
