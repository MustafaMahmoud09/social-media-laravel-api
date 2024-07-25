<?php

namespace App\Listeners\Post;

use App\Events\Post\CreatePostEvent;
use App\Models\User;
use App\Notifications\Post\CreatePostNotification;
use Exception;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CreatePostListener implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct()
    {
    }

    public function handle(CreatePostEvent $event): void
    {
        $user = $event->user;
        $followers = $user->followers;

        foreach ($followers as $follower) {

            User::find($follower->id)
                ->notify(new CreatePostNotification($event->post, $user));
        }
    }
}
