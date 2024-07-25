<?php

namespace App\Http\Resources\User\System\Gender;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserGenderResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        return [
            'key' => $this->id,
            'gender' => $this->gender->gender
        ];
    }


}
