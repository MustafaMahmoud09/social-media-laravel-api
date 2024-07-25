<?php

namespace App\Notifications\Post;

use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class CreatePostNotification extends Notification
{
    use Queueable;

    public function __construct(public $post,public $user)
    {
        //
    }

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toArray(object $notifiable): array
    {
        return [
            'id' => $this->post->id,
            'content' => $this->post->post,
            'type' => 'post created',
            'user' => $this->user->name,
            'created_at' => $this->post->created_at,
        ];
    }
}
