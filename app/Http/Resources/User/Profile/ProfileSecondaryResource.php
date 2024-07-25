<?php

namespace App\Http\Resources\User\Profile;

use App\Http\Resources\User\System\Address\UserAddressResource;
use App\Http\Resources\User\System\Gender\UserGenderResource;
use App\Http\Resources\User\System\PhoneNumber\PhoneNumberResource;

use App\Http\Resources\User\System\Relationship\UserRelationshipResource;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ProfileSecondaryResource extends JsonResource
{

    public function toArray(Request $request): array
    {
        return [
            'key' => $this->id,
            'gender' => new UserGenderResource($this->userGender),
            'phone_number' => PhoneNumberResource::collection($this->phoneNumbers),
            'address' => UserAddressResource::collection($this->address),
            'relationship' => new UserRelationshipResource($this->relationship),
        ];
    }

}
