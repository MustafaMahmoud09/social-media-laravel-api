<?php

namespace App\Jobs;

use App\Events\Chat\LastMessageEvent;
use App\Http\Resources\User\Chat\ChatRootResource;
use App\Models\Chat;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendChatToMembersJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(public $chat)
    {
        //
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {

        //get chat users
        $chatUsers = $this->chat->users()->get();

        //send new chat to all member in chat
        foreach ($chatUsers as $chatUser) {

            //send new chat here
            broadcast(
                new LastMessageEvent(
                    lastMessage: new ChatRootResource(Chat::find($this->chat->id), $chatUser->id),
                    chatId: $this->chat->id,
                    userId: $chatUser->id
                )
            );
        } //end foreach

    }//end handle

}//end SendChatToMembersJob
