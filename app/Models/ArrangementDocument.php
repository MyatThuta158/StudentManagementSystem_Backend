<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ArrangementDocument extends Model
{
    protected $table = 'arrangement_document';

    protected $primaryKey = 'id';


    protected $fillable = [
        'arrange_id',
        'feedback',
        'document_id',
        'created_by',
        'created_type',
        'status',
    ];

    // Define relationships
    public function arranging()
    {
        return $this->belongsTo(Arranging::class, 'arrange_id');
    }

    public function document()
    {
        return $this->belongsTo(Document::class, 'document_id');
    }
}
