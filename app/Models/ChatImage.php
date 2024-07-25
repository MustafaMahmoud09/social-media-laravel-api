<?php

namespace App\Models;

use App\Traits\Model\Relationships\Base\One\ChatImageRelationship;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ChatImage extends Model
{
    use HasFactory, ChatImageRelationship;

    protected $fillable = [
        'path',
        'chat_id'
    ];
}
