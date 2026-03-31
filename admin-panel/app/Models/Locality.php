<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Locality extends Model
{
    protected $fillable = [
        'city_id',
        'name',
        'pincode',
        'status'
    ];

    public function city()
    {
        return $this->belongsTo(City::class);
    }
}
