<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryPerson extends Model
{
    protected $table = 'delivery_persons';

    protected $fillable = [
        'name',
        'phone',
        'email',
        'vehicle',
        'status',
        'avatar_path',
        'zones_json',
        'orders_json',
    ];

    protected $casts = [
        'zones_json' => 'array',
        'orders_json' => 'array',
    ];
}
