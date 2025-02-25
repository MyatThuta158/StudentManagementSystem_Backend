<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Blog extends Model
{
    protected $guarded = [];

    /**
     * Get the tutor associated with the blog.
     */
    public function tutor()
    {
        return $this->belongsTo(Tutor::class, 'tutor_id');
    }

    /**
     * Get the student associated with the blog.
     */
    public function student()
    {
        return $this->belongsTo(Student::class, 'student_id');
    }


    public function comments()
    {
        return $this->hasMany(Comments::class);
    }
}
