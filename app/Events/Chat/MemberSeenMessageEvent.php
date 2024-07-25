<?php

namespace App\Events\Chat;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MemberSeenMessageEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public $seener,public $messageId)
    {
        //
    }

    public function broadcastWith(): array
    {
        return [
            'new-seener' => $this->seener
        ];
    }//end broadcastWith


    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'message.seened';
    }//end broadcastAs


    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('seeners-info.' . $this->messageId)
        ];
    }//end broadcastOn

}//end MemberSeenMessageEvent
