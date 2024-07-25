<?php

namespace App\Http\Resources\User\Chat;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ChatMemberResource extends JsonResource
{

    public function toArray(Request $request): array
    {

        try {
            $path = getBaseUrlFile() . $this->profiles()->orderBy('created_at', 'desc')->first()->path;
        } catch (Exception) {
            $path = null;
        }
        return [
            'key' => $this->pivot->id,
            'user_id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'profile' => $path,
            'type' => $this->pivot->type
        ];
    }
}
