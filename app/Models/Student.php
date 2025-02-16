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
    protected $fillable   = ['name', 'email', 'password', 'phone_number'];

    public function allocations()
    {
        return $this->hasMany(Allocation::class);
    }
}
