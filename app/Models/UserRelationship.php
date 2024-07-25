<?php

namespace App\Models;

use App\Traits\Model\Relationships\Base\Group\UserRelationshipRelationships;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserRelationship extends Model
{
    use HasFactory, UserRelationshipRelationships;

    protected $fillable = [
        'relationship_id',
        'user_id'
    ];
}
