<?php

namespace App\Notifications\Comment;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CreateCommentReplayOnCommentNotification extends Notification
{
    use Queueable;


    public function __construct(public $comment,public $user)
    {
    }


    public function via(object $notifiable): array
    {
        return ['database'];
    }


    public function toArray(object $notifiable): array
    {
        return [
            'id' => $this->comment->id,
            'content' => $this->comment->comment,
            'type' => 'replay on comment',
            'user' => $this->user->name,
            'created_at' => $this->comment->created_at,
        ];
    }
}
