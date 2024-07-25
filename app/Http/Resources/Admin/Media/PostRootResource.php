<?php

namespace App\Http\Resources\Admin\Media;

use App\Http\Resources\Admin\ImageResource;
use App\Http\Resources\Admin\UserMediaResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostRootResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        return [
            'key' => $this->id,
            'post' => $this->post,
            'image' => ImageResource::collection($this->multiMedias),
            'react_count' =>  $this->user_reacts_count,
            'user' => new UserMediaResource($this->user),
            'base_post' => new PostChildResource($this->postShare),
            'created_at' => $this->created_at
        ];
    }

}
