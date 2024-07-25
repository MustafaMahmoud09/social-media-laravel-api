<?php

namespace App\Models;

use App\Traits\Model\Relationships\Base\One\CoverRelationship;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cover extends Model
{
    use HasFactory, CoverRelationship;

    protected $fillable = [
        'path',
        'user_id',
        'title'
    ];
}
