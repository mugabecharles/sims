<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    protected $fillable = [
        'school_id', 'asset_code', 'name', 'category', 'location',
        'condition', 'purchase_date', 'purchase_value', 'custodian', 'notes', 'active',
    ];

    protected $casts = ['purchase_date' => 'date', 'active' => 'boolean'];

    public function school() { return $this->belongsTo(School::class); }
}
