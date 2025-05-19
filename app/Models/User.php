<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Factories\HasFactory;


class User extends Authenticatable implements JWTSubject
{
    use Notifiable;
    use HasFactory;

    protected $fillable = ['name', 'email', 'password'];

    protected $hidden = ['password'];

    // JWT methods
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function bookmarks()
    {
        return $this->hasMany(\App\Models\Bookmark::class);
    }

}
