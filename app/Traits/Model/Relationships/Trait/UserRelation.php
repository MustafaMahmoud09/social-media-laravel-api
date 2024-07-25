<?php

namespace App\Traits\Model\Relationships\Trait;

use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait UserRelation
{
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
