<?php

namespace App\Http\Resources\User;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ImageResource extends JsonResource
{

    public function toArray(Request $request): array
    {

        return [
            'key' => $this->id,
            'path' => getBaseUrlFile() . $this->path,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    } //end toArray

}//end ImageResource
