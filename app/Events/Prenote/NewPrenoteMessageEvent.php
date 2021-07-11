<?php

namespace App\Events\Prenote;

use App\Models\Prenote\PrenoteMessage;
use Illuminate\Broadcasting\PrivateChannel;

class NewPrenoteMessageEvent
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
            'callsign' => $this->message->callsign,
            'departure_airfield' => $this->message->departure_airfield,
            'departure_sid' => $this->message->departure_sid,
            'destination_airfield' => $this->message->destination_airfield,
            'sending_controller' => $this->message->controller_position_id,
            'target_controller' => $this->message->target_controller_position_id,
            'expires_at' => $this->message->expires_at->toDateTimeString(),
        ];
    }

    public function broadcastOn()
    {
        return [new PrivateChannel('prenote-messages')];
    }

    public function broadcastAs()
    {
        return 'prenote-message.received';
    }
}
