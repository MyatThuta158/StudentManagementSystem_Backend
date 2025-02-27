<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class Tutor extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens, HasRoles;

    protected $guard_name = 'api';
    protected $table = 'tutors';

    protected $fillable = ['name', 'email', 'password', 'phone_number', 'specialization'];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public function allocations()
    {
        return $this->hasMany(Allocation::class, 'tutor_id');
    }

    public function comments()
    {
        return $this->hasMany(Comments::class, 'tutor_id');
    }
}

