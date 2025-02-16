<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Allocation extends Model
{
    use HasFactory,SoftDeletes;

    protected $fillable = [
        'name',
        'allocation_date',
        'allocated_by',
        'staff_id',
        'tutor_id',
        'student_id',
        'section_id',
    ];

    public function staff()
    {
        return $this->belongsTo(Staff::class);
    }

    public function tutor()
    {
        return $this->belongsTo(Tutor::class);
    }

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function section()
    {
        return $this->belongsTo(Section::class);
    }
}
