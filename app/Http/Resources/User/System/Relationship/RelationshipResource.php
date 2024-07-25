<?php

namespace App\Http\Resources\User\System\Relationship;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RelationshipResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        return [
            'key' => $this->id,
            'relationship' => $this->title
        ];
    }

}
