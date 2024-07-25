<?php
namespace App\Traits\Model\Relationships\Trait;

use App\Models\Comment;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait CommentRelation
{
    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class);
    }
}
