<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = [
        'school_id', 'entity_type', 'entity_id', 'document_type',
        'file_name', 'file_url', 'mime_type', 'file_size', 'uploaded_by', 'notes',
    ];

    public function school()     { return $this->belongsTo(School::class); }
    public function uploadedBy() { return $this->belongsTo(User::class, 'uploaded_by'); }

    public function entity()
    {
        return $this->morphTo('entity', 'entity_type', 'entity_id');
    }
}
