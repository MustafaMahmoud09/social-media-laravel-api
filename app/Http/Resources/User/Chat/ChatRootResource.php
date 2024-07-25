<?php

namespace App\Http\Resources\User\Chat;

use App\Http\Resources\User\ImageResource;
use App\Http\Resources\User\Message\MessageChildResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\DB;

class ChatRootResource extends JsonResource
{

    public $userId = -1;

    public function __construct($resource,$userId)
    {
        parent::__construct($resource);
        if($userId){
            $this->userId = $userId;
        }//end if

    }//end __construct


    public function toArray(Request $request): array
    {

        if($this->userId == -1){
             $userAuthId = auth()->guard(getUserGuard())->user()->id;
        }//end if
        else{
            $userAuthId = $this->userId;
        }
        $image = null;
        //chat private
        if (!$this->type) {
            //get profile other user
            $user = $this->users()->wherePivot('user_id', '!=', $userAuthId)->first();
            $chatName = $user->name;
            $image = $user->profiles()->orderBy('created_at', 'desc')->first();
            $image = new ImageResource($image);
        } else {
            $chatName = $this->title;
            //get Chat Image
            $image = $this->image;
            $image =  new ImageResource($image);
        }
        //number messages unseen = count all messages - count messages seen in chat
        $numberMessageUnseen = $this->getNumberMessageUnseen($this->messages(), $userAuthId);

        return [
            'key' => $this->id,
            'name' => $chatName,
            'chat_type' => $this->type,
            'image' => $image,
            'last_message' => new MessageChildResource(
                $this->messages()->orderBy('created_at', 'desc')->first()
            ),
            'count_messages_unseen' => $numberMessageUnseen,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }//end toArray

    private function getNumberMessageUnseen($messages, $userId)
    {
        //get all messages
        $messages = $messages->orderBy('created_at', 'desc')->get();
        $count = 0;
        //for loop on user message
        foreach ($messages as $message) {

            if($message->user_id == $userId){
                continue;
            }//end if

            $isSeen = $message
                ->userWatches()
                ->wherePivot('user_id', $userId)
                ->exists();
            //check user seen or no
            if ($isSeen) {
                break;
            }
            $count++;
        }
        return $count;
    } //end getNumberMessageUnseen

}//end ChatRootResource
