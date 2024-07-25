<?php

namespace App\Providers;

use App\Events\Comment\CreateCommentOnPostEvent;
use App\Events\Comment\CreateCommentReplayOnCommentEvent;
use App\Events\Comment\CreateReactOnCommentEvent;
use App\Events\Post\CreatePostEvent;
use App\Events\Post\CreateReactOnPostEvent;
use App\Listeners\Comment\CreateCommentOnPostListener;
use App\Listeners\Comment\CreateCommentReplayOnCommentListener;
use App\Listeners\Comment\CreateReactOnCommentListener;
use App\Listeners\Post\CreatePostListener;
use App\Listeners\Post\CreateReactOnPostListener;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;

class EventServiceProvider extends ServiceProvider
{

    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        CreatePostEvent::class => [
            CreatePostListener::class
        ],
        CreateCommentOnPostEvent::class => [
            CreateCommentOnPostListener::class
        ],
        CreateCommentReplayOnCommentEvent::class => [
            CreateCommentReplayOnCommentListener::class
        ],
        CreateReactOnCommentEvent::class => [
            CreateReactOnCommentListener::class
        ],
        CreateReactOnPostEvent::class => [
            CreateReactOnPostListener::class
        ]
    ];


    public function boot(): void
    {
        //
    }


    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
