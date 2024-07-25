<?php
namespace App\Traits\Model\Relationships\Trait;

use App\Models\Post;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait PostManyRelation{

    public function posts(): HasMany
    {
        return $this->hasMany(Post::class);
    }

}
