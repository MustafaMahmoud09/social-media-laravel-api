<?php
namespace App\Traits\Model\Relationships\Trait;

use App\Models\Post;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait PostBelognsToRelation
{
    public function postShare(): BelongsTo
    {
        return $this->belongsTo(Post::class, 'post_id');
    }
}
