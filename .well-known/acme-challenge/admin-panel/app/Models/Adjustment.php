<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Warehouse;
use App\Models\Product;

class Adjustment extends Model
{
    protected $fillable = [
        'warehouse_id',
        'product_id',
        'type',
        'quantity',
        'reason'
    ];

    // 🔗 Warehouse Relation
    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    // 🔗 Product Relation
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}
