<?php

namespace App\Http\Resources\Admin\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class AdminResource extends JsonResource
{

    public function toArray(Request $request): array
    {

        return [
            'key' => $this->id,
            'name' => $this->name,
            'admin_type' => $this->roles->first()->name,
            'email' => $this->email,
            'ssn' => $this->ssn,
            'phone_number' => $this->phone,
            'birth_date' => $this->birth_date,
            'descriptio' => $this->description,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
