<?php

namespace App\Models;

use App\Traits\Model\Relationships\Base\One\ProfileRelationship;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Profile extends Model
{
    use HasFactory, ProfileRelationship;

    protected $fillable = [
        'path',
        'user_id',
        'title'
    ];
}
