<?php

namespace App\Events\Chat;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class MessageSendEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public function __construct(public $message, public $chatId)
    {
        //
    }



    public function broadcastWith(): array
    {
        return [
            'new_message' => $this->message
        ];
    }//end broadcastWith


    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'message.created';
    }//end broadcastAs


    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('chat-info.' . $this->message->chatId)
        ];
    }//end broadcastOn

}//end MessageSendEvent
