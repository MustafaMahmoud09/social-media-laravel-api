<?php

namespace App\Listeners\Post;

use App\Events\Post\CreateReactOnPostEvent;
use App\Models\User;
use App\Notifications\Post\CreateReactOnPostNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CreateReactOnPostListener implements ShouldQueue
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
    public function handle(CreateReactOnPostEvent $event): void
    {
        $post = $event->post;

        User::find($post->user_id)
            ->notify(
                new CreateReactOnPostNotification(
                    react: $event->react,
                    post: $event->post,
                    user: $event->user
                )
            );
    }
}
