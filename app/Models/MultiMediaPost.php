<?php

namespace App\Models;

use App\Traits\Model\Relationships\Base\One\MultiMediaPostRelationship;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MultiMediaPost extends Model
{
    use HasFactory, MultiMediaPostRelationship;

    protected $fillable = [
        'path',
        'post_id'
    ];
}
