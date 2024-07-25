<?php

namespace App\Models;

use App\Traits\Model\Relationships\Base\Group\ChatRelationships;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Chat extends Model
{
    use HasFactory, ChatRelationships, SoftDeletes;

    protected $fillable = [
        'title',
        'user_id',
        'type'
    ];
}
