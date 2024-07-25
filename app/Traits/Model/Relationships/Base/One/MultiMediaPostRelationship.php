<?php
namespace App\Traits\Model\Relationships\Base\One;

use App\Models\Post;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait MultiMediaPostRelationship
{
    public function post(): BelongsTo
    {
        return $this->belognsTo(Post::class);
    }
}
