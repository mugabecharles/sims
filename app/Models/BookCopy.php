<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class BookCopy extends Model
{
    protected $fillable = ['book_id', 'barcode', 'status', 'shelf_location'];

    public function book()  { return $this->belongsTo(Book::class); }
    public function loans() { return $this->hasMany(Loan::class, 'copy_id'); }

    public function activeLoan()
    {
        return $this->loans()->whereNull('returned_at')->latest()->first();
    }
}
