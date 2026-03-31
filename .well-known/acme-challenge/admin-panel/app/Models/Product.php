<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name',
        'product_name',
        'sku',
        'brand_id',
        'category_id',
        'sub_category',
        'purchase_price',
        'sale_price',
        'price',
        'margin',
        'mrp',
        'status',
        'image',
        'stock_quantity'
    ];

    // 🔗 Brand Relationship
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    // 🔗 Category Relationship
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    // 🔗 Inventory Relationship
    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }

    // 🔗 Order Items Relationship
    public function orderItems()
    {
        return $this->hasMany(OrderItem::class, 'product_id');
    }

    // Accessor for getting price
    public function getPriceAttribute()
    {
        return $this->sale_price ?? $this->mrp ?? 0;
    }

    // Scope for available products
    public function scopeAvailable($query)
    {
        return $query->where('status', true)->where('stock_quantity', '>', 0);
    }
}
