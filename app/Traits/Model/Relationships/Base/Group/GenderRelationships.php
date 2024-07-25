<?php
namespace App\Traits\Model\Relationships\Base\Group;

use App\Models\GenderUser;
use App\Traits\Model\Relationships\Trait\AdminRelation;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait GenderRelationships
{
    use AdminRelation;
    public function userGender(): HasMany
    {
        return $this->hasMany(GenderUser::class);
    }
}
