<?php

namespace App\Http\Resources\Admin\System\Social;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SingleSocialResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        return [
            'key' => $this->id,
            'name' => $this->title,
            'description' => $this->description,
            'path' => getBaseUrlFile() . $this->path,
            'created_at' => $this->created_at
        ];
    }

}
