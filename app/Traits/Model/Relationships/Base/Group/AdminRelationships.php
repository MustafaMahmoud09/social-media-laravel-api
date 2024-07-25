<?php

namespace App\Traits\Model\Relationships\Base\Group;

use App\Models\Address;
use App\Models\CallIcon;
use App\Models\Gender;
use App\Models\Relationship;
use App\Models\Social;
use App\Models\User;
use App\Models\UserDocumentation;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

trait AdminRelationships
{
    public function socials(): HasMany
    {
        return $this->hasMany(Social::class);
    }

    public function genders(): HasMany
    {
        return $this->hasMany(Gender::class);
    }

    public function address(): HasMany
    {
        return $this->hasMany(Address::class);
    }

    public function callIcons(): HasMany
    {
        return $this->hasMany(CallIcon::class);
    }

    public function manageUsers(): BelongsToMany
    {
        return $this->belognsToMany(User::class, 'admin_users');
    }

    public function relationships(): HasMany
    {
        return $this->hasMany(Relationship::class);
    }

    public function documentaion(): HasMany
    {
        return $this->hasMany(UserDocumentation::class);
    }
}
