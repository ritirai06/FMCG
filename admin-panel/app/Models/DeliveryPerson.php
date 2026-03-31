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
        'vehicle_number',
        'status',
        'avatar_path',
        'zones_json',
        'orders_json',
    ];

    protected $casts = [
        'zones_json' => 'array',
        'orders_json' => 'array',
    ];

    public function cities()
    {
        return $this->belongsToMany(City::class, 'delivery_partner_localities', 'delivery_partner_id', 'city_id')
                    ->whereNull('locality_id')
                    ->distinct();
    }

    public function localities()
    {
        return $this->belongsToMany(Locality::class, 'delivery_partner_localities', 'delivery_partner_id', 'locality_id');
    }

    public function assignedLocalities()
    {
        return $this->hasMany(DeliveryPartnerLocality::class, 'delivery_partner_id');
    }
}
