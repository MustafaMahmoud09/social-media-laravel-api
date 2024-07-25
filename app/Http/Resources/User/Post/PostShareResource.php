<?php

namespace App\Http\Resources\User\Post;

use App\Http\Resources\User\ImageResource;
use App\Http\Resources\User\UserMediaResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostShareResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        return [
            'key' => $this->id,
            'post' => $this->post,
            'images' => ImageResource::collection($this->multiMedias),
            'user' => new UserMediaResource($this->user),
            'base_post' => new PostEditResource($this->postShare),
            'created_at' => $this->created_at,
            'update_at' => $this->updated_at
        ];
    }

}
