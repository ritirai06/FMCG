<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IncentiveSlab extends Model
{
    protected $table = 'incentive_slabs';

    protected $fillable = ['min_amount','max_amount','percent','effective_from'];

    protected $casts = [
        'min_amount' => 'decimal:2',
        'max_amount' => 'decimal:2',
        'percent' => 'decimal:2',
        'effective_from' => 'date',
    ];
}
