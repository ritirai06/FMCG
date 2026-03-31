<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Order;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = ['invoice_number','order_id','date','amount','status'];

    protected $casts = [
        'date' => 'date',
        'amount' => 'decimal:2'
    ];

    public function order(){
        return $this->belongsTo(Order::class,'order_id');
    }
}
