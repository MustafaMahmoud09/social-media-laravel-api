<?php

namespace App\Http\Resources\Admin\System\Address;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AddressResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'key' => $this->id,
            'position' => $this->position,
            'code' => $this->zip_code,
            'admin' => $this->admin->name,
            'operation' => auth()->guard(getAdminGuard())->user()->id == $this->admin->id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
