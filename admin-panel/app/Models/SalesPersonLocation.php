<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesPersonLocation extends Model
{
    protected $fillable = [
        'sales_person_id',
        'latitude',
        'longitude',
        'address',
        'activity_type',
        'notes',
        'recorded_at'
    ];

    protected $casts = [
        'recorded_at' => 'datetime',
        'latitude' => 'decimal:8',
        'longitude' => 'decimal:8'
    ];

    public function salesPerson()
    {
        return $this->belongsTo(SalesPerson::class);
    }

    /**
     * Calculate distance between two coordinates in kilometers
     */
    public static function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $earthRadius = 6371; // km

        $dLat = deg2rad($lat2 - $lat1);
        $dLon = deg2rad($lon2 - $lon1);

        $a = sin($dLat/2) * sin($dLat/2) +
             cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
             sin($dLon/2) * sin($dLon/2);

        $c = 2 * atan2(sqrt($a), sqrt(1-$a));
        $distance = $earthRadius * $c;

        return round($distance, 2);
    }
}
