<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use App\Notifications\ResetPasswordNotification;


class User extends Authenticatable implements MustVerifyEmail
{
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'date_of_birth',
        'address',
        'gender',
        'profil_path',
        'status',
        'username'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime'
    ];

    public function polls()
    {
        return $this->hasMany(Poll::class); // User can create many polls
    }

    public function voters()
    {
        return $this->hasMany(Voter::class); // user can vote on many polls
    }

    public function sendPasswordResetNotification($token)
    {
        $url = 'http://fingervote.herokuapp.com/api/v1/reset-password?token=' . $token;

        $this->notify(new ResetPasswordNotification($url));
    }
}
