<?php

namespace App\Models;

use App\Traits\Model\Relationships\Base\One\MultiMediaStateRelationship;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MultiMediaState extends Model
{
    use HasFactory, MultiMediaStateRelationship;

    protected $fillable = [
        'path',
        'state_id'
    ];
}
