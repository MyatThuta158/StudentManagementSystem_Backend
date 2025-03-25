<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MeetingRecord extends Model
{
    use HasFactory, SoftDeletes;

    protected $table    = 'meeting_record';
    protected $fillable = [
        'arrange_id',
        'meeting_note',
        'uploaded_document',
    ];

    // Relationships
    public function arranging()
    {
        return $this->belongsTo(Arranging::class, 'arrange_id');
    }
}
