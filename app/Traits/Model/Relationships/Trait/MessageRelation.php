<?php
namespace App\Traits\Model\Relationships\Trait;

use App\Models\Message;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait MessageRelation
{
    public function messages(): HasMany
    {
        return $this->hasMany(Message::class);
    }
}
