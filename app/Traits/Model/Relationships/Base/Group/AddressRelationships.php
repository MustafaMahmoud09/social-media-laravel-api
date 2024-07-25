<?php

namespace App\Traits\Model\Relationships\Base\Group;

use App\Models\User;
use App\Traits\Model\Relationships\Trait\AdminRelation;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

trait AddressRelationships
{
    use AdminRelation;

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_address')->withPivot('id','type','created_at','updated_at');
    }

}
