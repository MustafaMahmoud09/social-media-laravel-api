<?php

namespace App\Http\Resources\Admin\System\Gender;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class GenderResource extends JsonResource
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
            'gender' => $this->gender,
            'admin_name' => $this->admin->name,
            'operation' => $this->admin->id ==  auth()->guard('admin-api')->user()->id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
