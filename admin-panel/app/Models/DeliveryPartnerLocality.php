<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryPartnerLocality extends Model
{
    protected $fillable = ['delivery_partner_id', 'city_id', 'locality_id'];

    public function deliveryPartner()
    {
        return $this->belongsTo(DeliveryPerson::class, 'delivery_partner_id');
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function locality()
    {
        return $this->belongsTo(Locality::class);
    }
}
