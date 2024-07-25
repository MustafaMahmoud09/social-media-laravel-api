<?php

namespace App\Http\Resources\User\Comment;

use App\Http\Resources\User\ImageResource;
use App\Http\Resources\User\UserMediaResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentRootResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        $userId = auth()->guard(getUserGuard())->user()->id;
        $userReact = $this->userReacts()->where('user_id', $userId);

        if($userReact->exists()){
            $reactId = $this->userReacts()->where('user_id', $userId)->first()->pivot->id;
        }else{
            $reactId = null;
        }

        return [
            'key' => $this->id,
            'comment' => $this->comment,
            'images' => ImageResource::collection($this->multiMedias),
            'user' => new UserMediaResource($this->user),
            'child_comments' => CommentChildResource::collection($this->comments),
            'react_count' => $userReact->count(),
            'user_reacted' => $userReact ->exists(),
            'react_type' => $userReact->pluck('type')->first(),
            'react_id' => $reactId,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }

}
