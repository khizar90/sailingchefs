<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'created_at',
        'updated_at',
        'email_verified_at'
        
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];
   
    public function followers()
    {
        return $this->hasMany(Follow::class, 'to_user_id');
    }
    public function followings()
    {
        return $this->hasMany(Follow::class, 'from_user_id');
    }
    public function blockedBy()
    {
        return $this->hasMany(Blocklistt::class, 'to_user_id');
    }

    public function blockedUsers()
    {
        return $this->hasMany(Blocklistt::class, 'from_user_id');
    }
    public function notificationsReceived()
    {
        return $this->hasMany(Notification::class, 'user_id');
    }

    public function notificationsSent()
    {
        return $this->hasMany(Notification::class, 'person_id');
    }
    public function recipe()
    {
        return $this->hasMany(Recipe::class, 'user_id');
    }
    public function sender()
    {
        return $this->hasMany(Message::class, 'from_id');
    }
    public function reciver()
    {
        return $this->hasMany(Message::class, 'to_id');
    }
    public function reviews()
    {
        return $this->hasMany(Review::class, 'user_id');
    }

}
