<?php
namespace App\Traits\Model\Relationships\Base\One;

use App\Models\Comment;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait MultiMediaCommentRelationship
{
    public function comment(): BelongsTo
    {
        return $this->belognsTo(Comment::class);
    }
}
