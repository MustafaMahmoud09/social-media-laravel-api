<?php

namespace App\Http\Resources\User\Message;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class MessageSeenResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        try {
            $path = getBaseUrlFile() . $this->profiles()->orderBy('created_at', 'desc')->first()->path;
        } catch (Exception) {
            $path = null;
        }
        return [
            'key' => $this->id,
            'name' => $this->name,
            'descriprtion' => $this->description,
            'profile' => $path,
            'created_at' => $this->pivot->created_at
        ];
    }//end toArray

}
