<?php

namespace App\Listeners\Comment;

use App\Events\Comment\CreateCommentReplayOnCommentEvent;
use App\Models\User;
use App\Notifications\Comment\CreateCommentReplayOnCommentNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CreateCommentReplayOnCommentListener implements ShouldQueue
{
    use InteractsWithQueue;


    public function __construct()
    {
        //
    }

    public function handle(CreateCommentReplayOnCommentEvent $event): void
    {
        $comment = $event->comment->commentReplay;

        User::find($comment->user_id)
            ->notify(
                new CreateCommentReplayOnCommentNotification(
                    $event->comment,
                    $event->user
                )
            );
    }
}
