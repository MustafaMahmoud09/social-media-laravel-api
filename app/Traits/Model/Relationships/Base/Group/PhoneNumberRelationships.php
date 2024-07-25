<?php
namespace App\Traits\Model\Relationships\Base\Group;

use App\Models\CallIcon;
use App\Traits\Model\Relationships\Trait\UserRelation;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait PhoneNumberRelationships
{
    use UserRelation;

    public function callIcon(): BelongsTo
    {
        return $this->belongsTo(CallIcon::class);
    }
}
