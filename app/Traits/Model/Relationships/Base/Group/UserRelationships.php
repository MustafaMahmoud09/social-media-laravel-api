<?php

namespace App\Traits\Model\Relationships\Base\Group;

use App\Models\Address;
use App\Models\Admin;
use App\Models\Chat;
use App\Models\Comment;
use App\Models\Cover;
use App\Models\GenderUser;
use App\Models\Message;
use App\Models\PhoneNumber;
use App\Models\Post;
use App\Models\Profile;
use App\Models\Relationship;
use App\Models\Social;
use App\Models\State;
use App\Models\User;
use App\Models\UserDocumentation;
use App\Models\UserRelationship;
use App\Traits\Model\Relationships\Trait\CommentRelation;
use App\Traits\Model\Relationships\Trait\MessageRelation;
use App\Traits\Model\Relationships\Trait\PostManyRelation;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

trait UserRelationships
{

    use  CommentRelation, PostManyRelation, MessageRelation;

    public function profiles(): HasMany
    {
        return $this->hasMany(Profile::class);
    }

    public function covers(): HasMany
    {
        return $this->hasMany(Cover::class);
    }

    public function relationship(): HasOne
    {
        return $this->hasOne(UserRelationship::class);
    }

    public function phoneNumbers(): HasMany
    {
        return $this->hasMany(PhoneNumber::class);
    }

    public function socials(): BelongsToMany
    {
        return $this->belongsToMany(Social::class, 'user_socails');
    }

    public function postReacts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'post_user_reacts');
    }

    public function messageReacts(): BelongsToMany
    {
        return $this->belongsToMany(Message::class, 'message_user_reacts');
    }

    public function messageWatches(): BelongsToMany
    {
        return $this->belongsToMany(Message::class, 'message_user_watches');
    }

    public function stateReacts(): BelongsToMany
    {
        return $this->belongsToMany(State::class, 'state_user_reacts');
    }

    public function states(): HasMany
    {
        return $this->hasMany(State::class);
    }

    public function commentReacts(): BelongsToMany
    {
        return $this->belongsToMany(Comment::class, 'comment_user_reacts');
    }

    public function followings(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'following_users', 'follow_id', 'following_id');
    }

    public function followers(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'following_users', 'following_id', 'follow_id');
    }

    public function chats(): BelongsToMany
    {
        return $this->belongsToMany(Chat::class, 'chat_users')->withPivot('id', 'type');
    }

    public function adminManagments(): BelongsToMany
    {
        return $this->belognsToMany(Admin::class, 'admin_users');
    }

    public function address(): BelongsToMany
    {
        return $this->belongsToMany(Address::class, 'user_address')->withPivot('id', 'type', 'created_at', 'updated_at');
    }

    public function userGender(): HasOne
    {
        return $this->hasOne(GenderUser::class);
    }

    public function documentation(): HasOne
    {
        return $this->hasOne(UserDocumentation::class);
    }
}
