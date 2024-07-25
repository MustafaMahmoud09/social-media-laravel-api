<?php

namespace App\Traits\Model\Relationships\Base\One;

use App\Models\State;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait MultiMediaStateRelationship
{

    public function state(): BelongsTo
    {
        return $this->belognsTo(State::class);
    }
}
