<?php

namespace App\Models;

use App\Traits\Model\Relationships\Base\Group\PhoneNumberRelationships;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PhoneNumber extends Model
{
    use HasFactory, PhoneNumberRelationships;

    protected $fillable = [
        'phone_number',
        'user_id',
        'call_icon_id'
    ];
}
