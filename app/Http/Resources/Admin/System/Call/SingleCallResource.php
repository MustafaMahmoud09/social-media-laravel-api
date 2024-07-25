<?php

namespace App\Http\Resources\Admin\System\Call;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SingleCallResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        return [
            'key' => $this->id,
            'country' => $this->country,
            'call' => $this->call,
            'admin' => $this->admin->name,
            'created_at' => $this->craeted_at,
        ];
    }

}
