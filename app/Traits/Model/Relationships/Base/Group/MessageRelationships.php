<?php
namespace App\Traits\Model\Relationships\Base\Group;

use App\Models\Chat;
use App\Models\Message;
use App\Models\MultiMediaMessage;
use App\Models\User;
use App\Traits\Model\Relationships\Trait\MessageRelation;
use App\Traits\Model\Relationships\Trait\UserRelation;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait MessageRelationships{

    use UserRelation, MessageRelation;

    public function chat(): BelongsTo
    {
        return $this->belongsTo(Chat::class);
    }

    public function multiMedias(): HasMany
    {
        return $this->hasMany(MultiMediaMessage::class);
    }

    public function baseMessage(): BelongsTo{
        return $this->belongsTo(Message::class,'message_id');
    }

    public function userReacts(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'message_user_reacts')->withPivot('type','id');
    }

    public function userWatches(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'message_user_watches')->withPivot('created_at');
    }

}
