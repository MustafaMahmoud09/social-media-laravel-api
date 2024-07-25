<?php

namespace App\Jobs;

use App\Events\Chat\LastMessageEvent;
use App\Events\Chat\MemberSeenMessageEvent;
use App\Http\Resources\User\Chat\ChatRootResource;
use App\Http\Resources\User\Message\MessageSeenResource;
use App\Models\Chat;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class WatcheMessageJop implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public function __construct(public $chat, public $userAuthId)
    {
    }

    public function handle(): void
    {
        //GET CHAT MESSAGES FROM LAST CREATD
        $messages = $this->chat->messages()
            ->orderBy('created_at', 'desc')
            ->get();

        foreach ($messages as $message) {

            if($message->user_id == $this->userAuthId){
                  continue;
            }//end if

            //check user seen message or no
            $userWatceMessage = $message->userWatches()
                ->wherePivot('user_id', $this->userAuthId)
                ->first();
            if ($userWatceMessage) {
                break;
            }

            //user see message now , insert this
            DB::table('message_user_watches')->insert(
                [
                    'user_id' => $this->userAuthId,
                    'message_id' => $message->id,
                    'created_at' => now(),
                    'updated_at' => now()
                ]
            );

            $userWatceMessage = $message->userWatches()
            ->wherePivot('user_id', $this->userAuthId)
            ->first();

            //send new memeber is seen messge to creater message
            broadcast(new MemberSeenMessageEvent(new MessageSeenResource($userWatceMessage),$message->id));

            //send unseen message count in chat to current user
            broadcast(new LastMessageEvent(new ChatRootResource(Chat::find($this->chat->id), $this->userAuthId),$this->chat->id,  $this->userAuthId));

        }//end foreach

    }//end handle

}//end WatcheMessageJop
