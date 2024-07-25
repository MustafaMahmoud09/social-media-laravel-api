<?php
namespace App\Traits\Model\Relationships\Base\Group;

use App\Models\Relationship;
use App\Traits\Model\Relationships\Trait\UserRelation;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait UserRelationshipRelationships
{
    use UserRelation;

    public function relationship(): BelongsTo
    {
        return $this->belongsTo(Relationship::class);
    }

}
