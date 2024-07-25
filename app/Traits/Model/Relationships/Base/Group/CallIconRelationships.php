<?php
namespace App\Traits\Model\Relationships\Base\Group;

use App\Models\PhoneNumber;
use App\Traits\Model\Relationships\Trait\AdminRelation;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait CallIconRelationships
{
    use AdminRelation;

    public function phoneNumbers(): HasMany
    {
        return $this->hasMany(PhoneNumber::class);
    }
}
