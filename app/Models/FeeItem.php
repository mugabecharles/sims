<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FeeItem extends Model
{
    protected $fillable = [
        'fee_structure_id', 'code', 'name', 'category', 'amount', 'mandatory', 'sort_order',
    ];

    protected $casts = ['mandatory' => 'boolean'];

    public function feeStructure() { return $this->belongsTo(FeeStructure::class); }
}
