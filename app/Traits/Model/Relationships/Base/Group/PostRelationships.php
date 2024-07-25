<?php
namespace App\Traits\Model\Relationships\Base\Group;

use App\Models\MultiMediaPost;
use App\Models\User;
use App\Traits\Model\Relationships\Trait\CommentRelation;
use App\Traits\Model\Relationships\Trait\PostBelognsToRelation;
use App\Traits\Model\Relationships\Trait\PostManyRelation;
use App\Traits\Model\Relationships\Trait\UserRelation;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait PostRelationships
{

    use UserRelation, CommentRelation, PostBelognsToRelation, PostManyRelation;

    public function userReacts(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'post_user_reacts')->withPivot('type','id');
    }

    public function multiMedias(): HasMany
    {
        return $this->hasMany(MultiMediaPost::class);
    }
}
