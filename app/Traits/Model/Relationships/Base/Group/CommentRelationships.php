<?php
namespace App\Traits\Model\Relationships\Base\Group;

use App\Models\Comment;
use App\Models\MultiMediaComment;
use App\Models\Post;
use App\Models\User;
use App\Traits\Model\Relationships\Trait\CommentRelation;
use App\Traits\Model\Relationships\Trait\UserRelation;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait CommentRelationships
{

    use UserRelation, CommentRelation;

    public function userReacts(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'comment_user_reacts')->withPivot('type','id');
    }

    public function commentReplay(): BelongsTo
    {
        return $this->belongsTo(Comment::class, 'comment_id');
    }

    public function post(): BelongsTo
    {
        return $this->belongsTo(Post::class);
    }

    public function multiMedias(): HasMany
    {
        return $this->hasMany(MultiMediaComment::class);
    }

}
