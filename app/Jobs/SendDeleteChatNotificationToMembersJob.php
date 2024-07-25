<?php

namespace App\Jobs;

use App\Events\Chat\DeleteChatEvent;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendDeleteChatNotificationToMembersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public $members, public $chatId)
    {
        //
    } //end __construct

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        //send delete chat notification to all chat members
        foreach ($this->members as $member) {

            //send delete notification here
            broadcast(
                new DeleteChatEvent(
                    chatId: $this->chatId,
                    userId: $member->id
                )
            );

        } //end foreach

    } //end handle

}//end SendDeleteChatNotificationToMembersJob
