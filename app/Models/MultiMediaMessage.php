<?php

namespace App\Models;

use App\Traits\Model\Relationships\Base\One\MultiMediaMessageRelationship;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MultiMediaMessage extends Model
{
    use HasFactory, MultiMediaMessageRelationship;

    protected $fillable = [
        'path',
        'message_id'
    ];
}
