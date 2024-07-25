<?php

namespace App\Http\Resources\User\Comment;

use App\Http\Resources\User\ImageResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentEditResource extends JsonResource
{

    public function toArray(Request $request): array
    {

        return [
            'key' => $this->id,
            'comment' => $this->comment,
            'images' => ImageResource::collection($this->multiMedias),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }

}
