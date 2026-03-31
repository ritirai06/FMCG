<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OrderSequence extends Model
{
    protected $fillable = ['date', 'sequence'];

    protected $casts = [
        'date' => 'date',
    ];

    public static function getNextSequence()
    {
        $today = now()->toDateString();
        $sequence = self::firstOrCreate(
            ['date' => $today],
            ['sequence' => 0]
        );
        $sequence->increment('sequence');
        return $sequence->sequence;
    }
}
