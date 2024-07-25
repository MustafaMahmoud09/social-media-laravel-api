<?php

namespace App\Models;

use App\Traits\Model\Relationships\Base\Group\MessageRelationships;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    use HasFactory, MessageRelationships;

    protected $fillable = [
        'message',
        'user_id',
        'chat_id',
        'message_id'
    ];
}
