<?php

namespace App\Http\Resources\User;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserMediaResource extends JsonResource
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
            'documment' => ($this->documentation) ? true : false,
            'profile' => $path
        ];
    }

}
