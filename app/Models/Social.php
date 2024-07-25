<?php

namespace App\Models;

use App\Traits\Model\Relationships\Base\Group\SocialRelationships;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Social extends Model
{
    use HasFactory, SocialRelationships, SoftDeletes;

    protected $fillable = [
        'path',
        'title',
        'description',
        'admin_id'
    ];
}
