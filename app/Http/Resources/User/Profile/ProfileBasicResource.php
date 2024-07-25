<?php

namespace App\Http\Resources\User\Profile;

use App\Http\Resources\User\ImageResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileBasicResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        return [
            'key' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'profile' => new ImageResource($this->profiles()->orderBy('created_at', 'desc')->first()),
            'cover' => new ImageResource($this->covers()->orderBy('created_at', 'desc')->first()),
            'created_at' => $this->created_at,
        ];
    }
}
