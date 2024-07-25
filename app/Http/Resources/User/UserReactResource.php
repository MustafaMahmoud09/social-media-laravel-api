<?php

namespace App\Http\Resources\User;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserReactResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        try{
          $profile = getBaseUrlFile() . $this->profiles()->orderBy('created_at', 'desc')->first()->path;
        }catch(Exception){
            $profile = null;
        }
        return [
            'key' => $this->pivot->id,
            'user_id' => $this->id,
            'name' => $this->name,
            'description' => $this->description,
            'profile' => $profile,
            'documment' => ($this->documentation) ? true : false,
            'react_type' => $this->pivot->type,
        ];
    }
}
