<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = [
        'name','sku','unit','sell_price','item_code','item_description',
        'brand','category','sub_category',
        'purchase_price','sale_price','mrp','margin','status',
        'image','quantity','gst_percent','gst_amount','price_includes_gst','final_price',
        'hsn_code','cess_percent','discount','discount_type','discount_value','discount_amount','offer_text',
        'warehouse_id','available_units',
    ];

    public function brand()      { return $this->belongsTo(Brand::class, 'brand', 'name'); }
    public function category()   { return $this->belongsTo(Category::class, 'category', 'name'); }
    public function inventories(){ return $this->hasMany(Inventory::class); }
    public function images()     { return $this->hasMany(ProductImage::class); }
    public function units()      { return $this->hasMany(ProductUnit::class); }
    public function orderItems() { return $this->hasMany(OrderItem::class); }
}
