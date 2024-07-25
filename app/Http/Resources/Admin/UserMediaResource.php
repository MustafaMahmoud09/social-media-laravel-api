<?php

namespace App\Http\Resources\Admin;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserMediaResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        try {
            $path = getBaseUrlFile() . $this->profiles()->orderBy('created_at', 'desc')->first()->path;
        } catch (Exception) {
            $path = null;
        }
        return [
            'key' => $this->id,
            'name' => $this->name,
            'documment' => ($this->documentation) ? true : false,
            'profile' => $path
        ];
    }
}
