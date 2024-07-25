<?php

namespace App\Http\Resources\User\System\PhoneNumber;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class IconResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        return [
            'key' => $this->id,
            'country' => $this->country,
            'call' => $this->call
        ];
    }
}
