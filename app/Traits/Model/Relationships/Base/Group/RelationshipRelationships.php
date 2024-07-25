<?php

namespace App\Traits\Model\Relationships\Base\Group;

use App\Models\Admin;
use App\Models\UserRelationship;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait RelationshipRelationships
{

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }

    public function UserRelationships(): HasMany
    {
        return $this->hasMany(UserRelationship::class);
    }
}
