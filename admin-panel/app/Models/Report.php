<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Report extends Model
{
    protected $fillable = [
    'report_date',
    'sales_person',
    'store',
    'city',
    'orders',
    'sales_amount',
    'delivery_status'
];
}
