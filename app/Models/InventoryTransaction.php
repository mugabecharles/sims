<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryTransaction extends Model
{
    protected $fillable = ['item_id', 'type', 'quantity', 'reference', 'notes', 'user_id'];

    public function item() { return $this->belongsTo(InventoryItem::class, 'item_id'); }
    public function user() { return $this->belongsTo(User::class); }
}
