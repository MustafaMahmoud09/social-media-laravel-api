<?php
namespace App\Models;

use App\Traits\Model\Relationships\Base\Group\StateRelationships;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class State extends Model
{
    use HasFactory, StateRelationships;

    protected $fillable = [
        'state',
        'theme',
        'validity',
        'user_id'
    ];
}
