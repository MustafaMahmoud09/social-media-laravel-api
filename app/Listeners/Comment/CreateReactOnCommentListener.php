<?php

namespace App\Listeners\Comment;

use App\Events\Comment\CreateReactOnCommentEvent;
use App\Models\User;
use App\Notifications\Comment\CreateReactOnCommentNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CreateReactOnCommentListener implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(CreateReactOnCommentEvent $event): void
    {
        $comment = $event->comment;

        User::find($comment->user_id)
        ->notify(
            new CreateReactOnCommentNotification(
                $event->react,
                $event->comment,
                $event->user
            )
        );
    }
}
