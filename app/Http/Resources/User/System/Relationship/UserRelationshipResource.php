<?php

namespace App\Http\Resources\User\System\Relationship;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserRelationshipResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        return [
            'key' => $this->id,
            'relationship' => $this->relationship->title,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }

}
