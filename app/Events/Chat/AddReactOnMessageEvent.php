<?php

namespace App\Events\Chat;

use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AddReactOnMessageEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public $react, public $messageId)
    {
        //
    }

    public function broadcastWith(): array
    {
        return [
            'react' => $this->react
        ];
    }//end broadcastWith


    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'react.added';
    }//end broadcastAs


    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('message-react-info.'.$this->messageId),
        ];
    }//end broadcastOn

}//end AddReactOnMessageEvent
