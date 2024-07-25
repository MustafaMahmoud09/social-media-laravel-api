<?php

namespace App\Traits\Model\Relationships\Base\Group;

use App\Models\Gender;
use App\Traits\Model\Relationships\Trait\UserRelation;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait GenderUserRelationships
{
    use UserRelation;

    function gender(): BelongsTo
    {
        return $this->belongsTo(Gender::class);
    }
}
