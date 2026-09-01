<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryItem extends Model
{
    protected $fillable = ['school_id', 'sku', 'name', 'unit', 'category', 'quantity', 'reorder_level', 'active'];
    protected $casts    = ['active' => 'boolean'];

    public function school()       { return $this->belongsTo(School::class); }
    public function transactions() { return $this->hasMany(InventoryTransaction::class, 'item_id'); }
}
