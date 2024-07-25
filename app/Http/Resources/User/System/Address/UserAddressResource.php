<?php

namespace App\Http\Resources\User\System\Address;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserAddressResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'key' => $this->pivot->id,
            'type' => $this->pivot->type,
            'address' => $this->position,
            'code' => $this->zip_code,
            'created_at' => $this->pivot->created_at,
            'updated_at' => $this->pivot->updated_at
        ];
    }
}
