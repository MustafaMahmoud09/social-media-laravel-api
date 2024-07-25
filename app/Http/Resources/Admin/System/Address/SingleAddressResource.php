<?php

namespace App\Http\Resources\Admin\System\Address;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SingleAddressResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'key' => $this->id,
            'position' => $this->position,
            'code' => $this->zip_code,
            'created_at' => $this->created_at
        ];
    }
}
