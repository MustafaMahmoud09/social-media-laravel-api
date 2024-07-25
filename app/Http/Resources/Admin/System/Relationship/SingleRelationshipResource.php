<?php

namespace App\Http\Resources\Admin\System\Relationship;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SingleRelationshipResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'key' => $this->id,
            'title' => $this->title,
            'admin' => $this->admin->name,
            'created_at' => $this->created_at,
        ];
    }
}
