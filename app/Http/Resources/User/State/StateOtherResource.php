<?php

namespace App\Http\Resources\User\State;

use App\Http\Resources\User\ImageResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class StateOtherResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        return [
            'key' => $this->id,
            'state' => $this->state,
            'theme' => $this->theme,
            'image' => new ImageResource($this->multiMedia),
            'created_at' => $this->created_at
        ];
    }

}
