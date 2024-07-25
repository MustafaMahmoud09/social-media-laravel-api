<?php

namespace App\Traits\Model\Relationships\Base\Group;
use App\Models\ChatImage;
use App\Models\User;
use App\Traits\Model\Relationships\Trait\MessageRelation;
use App\Traits\Model\Relationships\Trait\UserRelation;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

trait ChatRelationships
{
    use MessageRelation, UserRelation;

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'chat_users')->withPivot('id','type');
    }

    public function image(): HasOne
    {
        return $this->hasOne(ChatImage::class);
    }
}
