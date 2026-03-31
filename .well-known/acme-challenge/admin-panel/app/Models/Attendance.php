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
    ];

    protected $casts = [
        'date' => 'date',
    ];
}



