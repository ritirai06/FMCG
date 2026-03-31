<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesPerson extends Model
{
    protected $table = 'sales_persons';

    protected $fillable = [
        'name',
        'phone',
        'email',
        'city_id',
        'status',
        'avatar_path',
        'base_salary',
        'allowance',
        'bonus_percent',
        'target_sales',
        'incentive_percent'
    ];

    public function cities()
    {
        return $this->belongsToMany(City::class, 'sales_person_city', 'sales_person_id', 'city_id');
    }

    public function localities()
    {
        return $this->belongsToMany(Locality::class, 'sales_person_locality', 'sales_person_id', 'locality_id');
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }
}
