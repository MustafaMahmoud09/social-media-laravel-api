<?php

namespace App\Http\Resources\User\System\PhoneNumber;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PhoneNumberResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        return [
            'key' => $this->id,
            'phone_number' => $this->phone_number,
            'call' => $this->callIcon->call,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
