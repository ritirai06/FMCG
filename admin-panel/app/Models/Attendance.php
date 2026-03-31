<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $table = 'attendances';

    protected $fillable = [
        'employee_name',
        'date',
        'time_in',
        'time_out',
        'status',
        'notes',
        'image_in',
        'image_out',
        'latitude',
        'longitude',
    ];

    protected $casts = [
        'date' => 'date',
    ];
}



