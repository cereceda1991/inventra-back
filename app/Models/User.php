<?php

namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable as LaravelAuthenticatable;
use Jenssegers\Mongodb\Auth\User as MongoUser;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends MongoUser implements JWTSubject, LaravelAuthenticatable
{
    use \Illuminate\Auth\Authenticatable;

    protected $connection = 'mongodb';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'currency',
        'decimals',
        'darkmode',
        'profile'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            $user->role='administrator';
            $user->currency = 'USD'; // Moneda por defecto: dólares
            $user->decimals = true; // Decimales por defecto: true
            $user->darkmode = false; // Modo oscuro por defecto: false
            $user->profile='https://i.ibb.co/Zgq29Gy/profile.png';//Avatar por defecto
        });
    }

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
