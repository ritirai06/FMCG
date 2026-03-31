<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'customer_id', 'order_id', 'created_by', 'amount',
        'payment_type', 'reference_number', 'notes', 'payment_date',
    ];

    public function customer() { return $this->belongsTo(Customer::class); }
    public function order()    { return $this->belongsTo(Order::class); }
    public function createdBy(){ return $this->belongsTo(User::class, 'created_by'); }
}
