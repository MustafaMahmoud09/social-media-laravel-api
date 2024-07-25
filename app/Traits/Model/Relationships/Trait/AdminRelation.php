<?php

namespace App\Traits\Model\Relationships\Trait;

use App\Models\Admin;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait AdminRelation
{

    public function admin(): BelongsTo
    {
        return $this->belongsTo(Admin::class);
    }
}
