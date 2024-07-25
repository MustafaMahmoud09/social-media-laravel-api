<?php

namespace App\Http\Resources\User\Message;

use App\Http\Resources\User\ImageResource;
use App\Http\Resources\User\UserMediaResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageRootResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        $userAuthId = auth()->guard(getUserGuard())->user()->id;
        $reacts = $this->userReacts();

        //check user react on message or no
        $isReact =  $reacts->wherePivot('user_id', $userAuthId)->exists();
        $reactKey = null;
        $reactType = null;
        if ($isReact) {
            $react = $reacts->wherePivot('user_id', $userAuthId)->first();
            $reactKey = $react->pivot->id;
            $reactType = $react->pivot->type;
        }
        return [
            'key' => $this->id,
            'message' => $this->message,
            'images' => ImageResource::collection($this->multiMedias),
            'base_message' => new MessageChildResource($this->baseMessage),
            'user' => new UserMediaResource($this->user),
            //'user_type' => $userAuthId == $this->user->id,
            'react_count' => $reacts->count(),
            'user_is_react' => $reacts->wherePivot('user_id', $userAuthId)->exists(),
            'react_key' => $reactKey,
            'react_type' => $reactType,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }//end toArray

}//end MessageRootResource
