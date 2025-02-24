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

    // Allow mass assignment for these attributes
    protected $fillable = ['name', 'email', 'password', 'phone_number', 'specialization'];

    // Hide sensitive data when converting to JSON
    protected $hidden = ['password', 'remember_token'];

    // Auto-cast attributes
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed', // Laravel will auto-hash password on creation
    ];

    /**
     * Define the relationship with Allocations
     */
    public function allocations()
    {
        return $this->hasMany(\App\Models\Allocation::class);
    }

    /**
     * Define the relationship with Comments
     */
    public function comments()
    {
        return $this->hasMany(\App\Models\Comments::class);
    }
}
