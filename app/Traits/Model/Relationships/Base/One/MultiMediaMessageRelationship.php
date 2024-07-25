<?php
namespace App\Traits\Model\Relationships\Base\One;

use App\Models\Message;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

trait MultiMediaMessageRelationship
{
    public function message(): BelongsTo
    {
        return $this->belognsTo(Message::class);
    }
}
