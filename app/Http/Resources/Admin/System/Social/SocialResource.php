<?php

namespace App\Http\Resources\Admin\System\Social;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class SocialResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        return [
            'key' => $this->id,
            'name' => $this->title,
            'path' => getBaseUrlFile() . $this->path,
            'admin' => $this->admin->name,
            'operation' => auth()->guard(getAdminGuard())->user()->id == $this->admin_id,
            'created_at' => $this->created_at,
            'update_at' => $this->updated_at
        ];
    }
}
