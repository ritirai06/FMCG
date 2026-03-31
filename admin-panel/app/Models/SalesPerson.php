<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalesPerson extends Model
{
    protected $table = 'sales_persons';

    protected $fillable = [
        'name', 'phone', 'email', 'city_id', 'status', 'avatar_path',
        'base_salary', 'allowance', 'bonus', 'bonus_percent',
        'target_sales', 'incentive_percent',
        'current_latitude', 'current_longitude', 'address',
        'last_location_update', 'location_tracking_enabled',
    ];

    protected $casts = [
        'last_location_update'       => 'datetime',
        'location_tracking_enabled'  => 'boolean',
        'current_latitude'           => 'decimal:8',
        'current_longitude'          => 'decimal:8',
    ];

    /**
     * Calculate incentive based on actual sales vs target sales
     * Formula: If actual_sales > target_sales, incentive = (extra_sales * incentive_percent) / 100
     */
    public function calculateIncentive($actualSales)
    {
        if (!$this->target_sales || !$this->incentive_percent) {
            return 0;
        }

        $extraSales = max(0, $actualSales - $this->target_sales);
        $incentive = ($extraSales * $this->incentive_percent) / 100;

        return round($incentive, 2);
    }

    /**
     * Calculate bonus amount from bonus percentage
     */
    public function calculateBonus()
    {
        if (!$this->base_salary || !$this->bonus_percent) {
            return 0;
        }

        return round(($this->base_salary * $this->bonus_percent) / 100, 2);
    }

    /**
     * Calculate total salary including base, allowance, bonus, and incentive
     */
    public function calculateTotalSalary($actualSales = 0)
    {
        $baseSalary = floatval($this->base_salary ?? 0);
        $allowance = floatval($this->allowance ?? 0);
        $bonus = $this->calculateBonus();
        $incentive = $this->calculateIncentive($actualSales);

        return round($baseSalary + $allowance + $bonus + $incentive, 2);
    }

    /**
     * Get actual sales amount from orders
     */
    public function getActualSales()
    {
        return $this->hasMany(\App\Models\Order::class, 'sales_person_id')
            ->where('status', 'Completed')
            ->sum('total_amount') ?? 0;
    }

    public function cities()
    {
        return $this->belongsToMany(City::class, 'sales_person_city', 'sales_person_id', 'city_id');
    }

    public function assignedCities()
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

    public function locations()
    {
        return $this->hasMany(SalesPersonLocation::class);
    }

    public function latestLocation()
    {
        return $this->hasOne(SalesPersonLocation::class)->latestOfMany('recorded_at');
    }
}
