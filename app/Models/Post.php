<?php

namespace App\Models;

use App\Traits\Model\Relationships\Base\Group\PostRelationships;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory, PostRelationships;

    protected $fillable = [
        'post',
        'user_id',
        'post_id'
    ];

    public function getPaginationResult($pageNumber,$perPage = 10)
    {
        return $this::paginate($perPage, ['*'], 'page', $pageNumber);
    }
}
