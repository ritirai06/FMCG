<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    protected $fillable = ['invoice_id', 'item_id', 'item_name', 'quantity', 'price', 'total'];

    protected $casts = [
        'quantity' => 'decimal:2',
        'price'    => 'decimal:2',
        'total'    => 'decimal:2',
    ];

    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'item_id');
    }
}
