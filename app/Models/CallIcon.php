<?php

namespace App\Models;

use App\Traits\Model\Relationships\Base\Group\CallIconRelationships;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CallIcon extends Model
{
    use HasFactory, CallIconRelationships, SoftDeletes;

    protected $fillable = [
        'country',
        'call',
        'admin_id'
    ];
}
