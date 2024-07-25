<?php

namespace App\Events\Chat;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LastMessageEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Create a new event instance.
     */
    public function __construct(public $lastMessage,public $chatId,public $userId)
    {
        //
    }

    public function broadcastWith(): array
    {
        return [
            'chat_id' => $this->chatId,
            'last_message' => $this->lastMessage
        ];
    }//end broadcastWith


    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'chat.unseen.count';
    }//end broadcastAs


    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('user-chat.'.$this->userId),
        ];
    }//end broadcastOn

}//end LastMessageEvent
