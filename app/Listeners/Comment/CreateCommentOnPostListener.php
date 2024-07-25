<?php

namespace App\Listeners\Comment;

use App\Events\Comment\CreateCommentOnPostEvent;
use App\Models\Post;
use App\Models\User;
use App\Notifications\Comment\CreateCommentOnPostNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class CreateCommentOnPostListener implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct()
    {
        //
    }


    public function handle(CreateCommentOnPostEvent $event): void
    {
        $post = $event->comment->post;

        User::find($post->user_id)
            ->notify(
                new CreateCommentOnPostNotification(
                    $event->comment,
                    $event->user
                )
            );
    }
}
