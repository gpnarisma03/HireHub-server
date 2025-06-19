<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

use App\Notifications\VerifyEmailNotification;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable implements MustVerifyEmail

{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * Use 'user_id' as the primary key (your custom table column)
     */
    protected $primaryKey = 'user_id';

    /**
     * Mass assignable attributes (match your users table)
     */
    protected $fillable = [
        'first_name',
        'middle_initial',
        'last_name',
        'email',
        'mobile_number',
        'password',
        'role',
    ];

    /**
     * Hidden attributes
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Attribute casting
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * A user (employer) may own one company
     */
    public function companies(): hasMany
    {
        return $this->hasMany(Company::class, 'user_id');
    }

    /**
     * A user (employee) may submit many job applications
     */
    public function applications(): HasMany
    {
        return $this->hasMany(Application::class, 'user_id');
    }

    public function sendEmailVerificationNotification()
{
    $this->notify(new \App\Notifications\VerifyEmailNotification());
}

}
