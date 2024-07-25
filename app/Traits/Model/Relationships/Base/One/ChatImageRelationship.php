<?php

namespace App\Traits\Model\Relationships\Base\One;

use App\Models\Chat;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait ChatImageRelationship
{

    public function chat(): BelongsTo
    {
        return $this->belognsTo(Chat::class);
    }
}
