<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    protected $fillable = [
    'name',
    'manager_name',
    'contact',
    'location',
    'status'
];

}
