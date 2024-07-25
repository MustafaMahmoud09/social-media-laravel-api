<?php

namespace App\Events\Chat;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UpdateMessageEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public $message)
    {
        //
    }

    public function broadcastWith(): array
    {
        return [
            'message' => $this->message
        ];
    }//end broadcastWith


    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'message.updated';
    }//end broadcastAs


    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('chat-info.' . $this->message->chatId)
        ];
    }//end broadcastOn

}//end UpdateMessageEvent
