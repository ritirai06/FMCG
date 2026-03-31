<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Store extends Model
{
    use HasFactory;

    protected $fillable = ['store_name','code','manager','phone','address','status'];

    protected $casts = [
        'status' => 'boolean',
    ];
    public function inventories(){ return $this->hasMany(StoreInventory::class,'store_id'); }
    public function contacts(){ return $this->hasMany(StoreContact::class,'store_id'); }
    public function settings(){ return $this->hasMany(StoreSetting::class,'store_id'); }
    public function orders()
{
    return $this->hasMany(Order::class,'store_id');
}
}
