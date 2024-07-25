<?php

namespace App\Http\Resources\User\Post;

use App\Http\Resources\User\ImageResource;
use App\Http\Resources\User\UserMediaResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostShowResource extends JsonResource
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
            'post' => $this->post,
            'images' => ImageResource::collection($this->multiMedias),
            'user' => new UserMediaResource($this->user),
            'base_post' => new PostEditResource($this->postShare),
            'react_count' => $userReact->count(),
            'user_reacted' => $userReact ->exists(),
            'react_type' => $userReact->pluck('type')->first(),
            'react_id' => $reactId,
            'created_at' => $this->created_at,
            'update_at' => $this->updated_at
        ];
    }
}
