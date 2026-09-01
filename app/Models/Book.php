<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Book extends Model
{
    protected $fillable = ['school_id', 'isbn', 'title', 'author', 'publisher', 'category', 'publish_year', 'description'];

    public function school()  { return $this->belongsTo(School::class); }
    public function copies()  { return $this->hasMany(BookCopy::class); }

    public function availableCopies()
    {
        return $this->copies()->where('status', 'available');
    }
}
