<?php

namespace App\Http\Resources\User\Comment;

use App\Http\Resources\User\ImageResource;
use App\Http\Resources\User\UserMediaResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentChildResource extends JsonResource
{

    public function toArray(Request $request): array
    {

        return [
            'key' => $this->id,
            'comment' => $this->comment,
            'images' => ImageResource::collection($this->multiMedias),
            'user' => new UserMediaResource($this->user),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }

}
