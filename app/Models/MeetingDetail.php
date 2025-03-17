<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MeetingDetail extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = "meeting_detail";

    protected $fillable = [
        'arrange_date',
        'meeting_type',
        'location',
        'meeting_link',
        'online_meeting_application_type',
        'arrange_id',
        'topic',
        'status',
    ];

    // Relationships
    public function arranging()
    {
        return $this->belongsTo(Arranging::class, 'arrange_id');
    }
}
