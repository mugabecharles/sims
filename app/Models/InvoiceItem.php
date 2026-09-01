<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    protected $fillable = ['invoice_id', 'fee_item_id', 'description', 'amount'];

    public function invoice()  { return $this->belongsTo(Invoice::class); }
    public function feeItem()  { return $this->belongsTo(FeeItem::class); }
}
