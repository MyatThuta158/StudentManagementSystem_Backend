<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class Student extends Authenticatable
{
    use HasFactory, Notifiable, HasApiTokens, HasRoles;

    protected $guard_name = 'api';
    protected $table = 'students'; // ✅ Explicit table name

    protected $fillable = ['name', 'email', 'password', 'phone_number'];

    protected $hidden = ['password', 'remember_token'];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed', // ✅ Laravel auto-hashes passwords
    ];

    public function allocations()
    {
        return $this->hasMany(Allocation::class, 'student_id');
    }

    public function comments()
    {
        return $this->hasMany(Comments::class, 'student_id'); // ✅ Fix reference
    }
}

