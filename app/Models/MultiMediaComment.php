<?php

namespace App\Models;

use App\Traits\Model\Relationships\Base\One\MultiMediaCommentRelationship;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MultiMediaComment extends Model
{
    use HasFactory, MultiMediaCommentRelationship;

    protected $fillable = [
        'path',
        'comment_id'
    ];
}
