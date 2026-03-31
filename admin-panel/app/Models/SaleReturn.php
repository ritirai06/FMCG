<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SaleReturn extends Model
{
    protected $fillable = ['order_id', 'customer_id', 'created_by', 'total_amount', 'reason', 'items'];

    protected $casts = ['items' => 'array'];

    public function order()    { return $this->belongsTo(Order::class); }
    public function customer() { return $this->belongsTo(Customer::class); }
    public function createdBy(){ return $this->belongsTo(User::class, 'created_by'); }
}
