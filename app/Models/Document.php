<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{

    protected $table = 'document';

    protected $fillable = [
        'file',
        'file_name',
        'created_by',
        'created_type',
    ];

    public function arrangementDocuments()
    {
        return $this->hasMany(ArrangementDocument::class, 'document_id', 'id');
    }
}
