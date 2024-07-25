<?php

namespace App\Models;

use App\Traits\Model\Relationships\Base\Group\GenderRelationships;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Gender extends Model
{
    use HasFactory, GenderRelationships, SoftDeletes;

    protected $fillable = [
        'gender',
        'admin_id'
    ];
}
