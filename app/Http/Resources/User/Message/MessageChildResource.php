<?php

namespace App\Http\Resources\User\Message;

use App\Http\Resources\User\ImageResource;
use App\Http\Resources\User\UserMediaResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageChildResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        return [
            'key' => $this->id,
            'message' => $this->message,
            'images' => ImageResource::collection($this->multiMedias),
            'user' => new UserMediaResource($this->user),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }

}
