<?php

namespace App\Http\Resources\Admin\System\Call;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CallResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        return [
            'key' => $this->id,
            'country' => $this->country,
            'call' => $this->call,
            'admin' => $this->admin->name,
            'opereation' => auth()->guard(getAdminGuard())->user()->id == $this->admin->id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at
        ];
    }

}
