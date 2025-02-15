<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tutor extends Model
{
    protected $guarded = [];

    protected $hidden = [
        'password',
    ];
}
