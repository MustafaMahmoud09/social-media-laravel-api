<?php

namespace App\Http\Resources\Admin\User;

use Exception;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserDocumentResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        $adminAuthId = auth()->guard(getAdminGuard())->user()->id;
        try {
            $path = getBaseUrlFile() . $this->profiles()->orderBy('created_at', 'desc')->first()->path;
        } catch (Exception) {
            $path = null;
        }
        return [
            'key' => $this->id,
            'name' => $this->name,
            'email' => $this->email,
            'profile' => $path,
            'documment' => ($this->documentation) ? true : false,
            'followers_count' => $this->followers_count,
            'followings_count' => $this->followings_count,
            'operation' => ($this->documentation) ? $this->documentation()->first()->admin_id == $adminAuthId : null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    } //end toArray

}
