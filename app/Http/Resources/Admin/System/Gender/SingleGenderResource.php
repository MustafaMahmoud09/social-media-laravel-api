<?php

namespace App\Http\Resources\Admin\System\Gender;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SingleGenderResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'key' => $this->id,
            'gender' => $this->gender,
            'created_at' => $this->created_at
        ];
    }

}
