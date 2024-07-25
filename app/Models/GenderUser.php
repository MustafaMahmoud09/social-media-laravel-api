<?php

namespace App\Models;

use App\Traits\Model\Relationships\Base\Group\GenderUserRelationships;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GenderUser extends Model
{
    use HasFactory, GenderUserRelationships;

    protected $fillable = [
        'user_id',
        'gender_id'
    ];
}
