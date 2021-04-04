<?php

namespace App;

use App\EventRegistration;
use App\CompetitionRegistration;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable, HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'first_name', 'last_name', 'email', 'password', 'role', 'licence_number', 'gender', 'weight_class', 'visits', 'last_visited_at', 'granted_by',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'edited_at' => 'datetime',
    ];

    public function eventRegistrations()
    {
        return $this->hasMany(EventRegistration::class);
    }

    public function competitionRegistrations()
    {
        return $this->hasMany(CompetitionRegistration::class);
    }
}
