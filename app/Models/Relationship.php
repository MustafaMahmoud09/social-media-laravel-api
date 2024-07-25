<?php

namespace App\Models;

use App\Traits\Model\Relationships\Base\Group\RelationshipRelationships;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Relationship extends Model
{
    use HasFactory, RelationshipRelationships, SoftDeletes;

    protected $fillable = [
        'title',
        'admin_id'
    ];
}
