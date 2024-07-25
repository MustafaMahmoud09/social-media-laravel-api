<?php

namespace App\Models;

use App\Traits\Model\Relationships\Base\Group\CommentRelationships;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory, CommentRelationships;

    protected $fillable = [
         'comment',
         'user_id',
         'post_id',
         'comment_id'
    ];
}
