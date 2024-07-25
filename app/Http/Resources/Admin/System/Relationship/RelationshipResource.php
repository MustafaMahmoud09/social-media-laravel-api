<?php

namespace App\Http\Resources\Admin\System\Relationship;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RelationshipResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        return [
            'key' => $this->id,
            'title' => $this->title,
            'admin' => $this->admin->name,
            'operation' => $this->admin->id == auth()->guard(getAdminGuard())->user()->id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }
}
