<?php

namespace App\Events\Post;


use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class CreatePostEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;


    public function __construct(public $post, public $user)
    {
        //
    }


    public function broadcastOn(): array
    {
        return [
            new PrivateChannel('channel-name'),
        ];
    }
}
