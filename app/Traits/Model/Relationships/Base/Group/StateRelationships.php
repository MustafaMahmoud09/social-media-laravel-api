<?php

namespace App\Traits\Model\Relationships\Base\Group;

use App\Models\MultiMediaState;
use App\Models\User;
use App\Traits\Model\Relationships\Trait\UserRelation;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

trait StateRelationships
{

    use UserRelation;

    public function multiMedia(): HasOne
    {
        return $this->hasOne(MultiMediaState::class);
    }

    public function userWatches(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'state_user_watches')->withPivot('id','type');
    }
}
