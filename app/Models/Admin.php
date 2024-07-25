<?php

namespace App\Models;

use App\Traits\Model\Relationships\Base\Group\AdminRelationships;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable implements JWTSubject
{
    use HasFactory, AdminRelationships, HasRoles;

    protected $guard_name = 'web';
    protected $fillable = [
        'name',
        'description',
        'email',
        'password',
        'birth_date',
        'phone',
        'ssn',
        'gender',
    ];



    public function getJWTIdentifier()
    {
      return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
      return [];
    }
}
