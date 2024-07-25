<?php

namespace App\Models;

use App\Traits\Model\Relationships\Base\Group\AddressRelationships;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Address extends Model
{
    use HasFactory, AddressRelationships, SoftDeletes;

    protected $fillable = [
       'position',
       'zip_code',
       'admin_id'
    ];
}
