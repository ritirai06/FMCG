<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreInventory extends Model
{
    use HasFactory;

    protected $fillable = ['store_id','sku_count','low_stock_items','last_sync'];

    protected $casts = ['last_sync' => 'date'];

    public function store(){
        return $this->belongsTo(Store::class,'store_id');
    }
}
