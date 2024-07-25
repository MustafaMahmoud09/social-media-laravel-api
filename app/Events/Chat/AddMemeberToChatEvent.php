<?php

namespace App\Events\Chat;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AddMemeberToChatEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public $member,public $chatId)
    {
        //
    }

    public function broadcastWith(): array
    {
        return [
            'new_member' => $this->member
        ];
    }//end broadcastWith


    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'member.created';
    }//end broadcastAs


    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('chat-info.' . $this->chatId)
        ];
    }//end broadcastOn

}//end AddMemeberToChatEvent
