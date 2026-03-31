<?php

namespace App\Models;
use App\Models\User;
use App\Models\Store;
use App\Models\City;
use App\Models\SalesPerson;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_number',
        'store_id',
        'store_locality_id',
        'sales_person_id',
        'customer',
        'customer_name',
        'customer_phone',
        'order_date',
        'amount',
        'total_amount',
        'status',
        'notes',
        'created_by',
        'assigned_delivery',
        'assigned_delivery_person_id',
        'delivery_lat',
        'delivery_lng',
        'delivery_photo',
    ];

    protected $casts = [
        'order_date' => 'date',
        'amount' => 'decimal:2',
        'total_amount' => 'decimal:2'
    ];

    // Relationships
    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id');
    }

    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'order_id');
    }

    public function payments()
    {
        return $this->hasMany(Payment::class, 'order_id');
    }

    public function store()
    {
        return $this->belongsTo(Store::class, 'store_id', 'id');
    }

    /**
     * Accessor that returns related Store model when relation is loaded,
     * otherwise falls back to the raw `store` string column value.
     */
    public function getStoreAttribute($value)
    {
        if ($this->relationLoaded('store') && $this->getRelation('store') !== null) {
            return $this->getRelation('store');
        }
        return $value;
    }


    public function salesPerson()
    {
        return $this->belongsTo(SalesPerson::class, 'sales_person_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function assignedDelivery()
    {
        return $this->belongsTo(User::class, 'assigned_delivery');
    }

    public function assignedDeliveryPerson()
    {
        return $this->belongsTo(\App\Models\DeliveryPerson::class, 'assigned_delivery_person_id');
    }

    public function locality()
    {
        return $this->belongsTo(\App\Models\Locality::class, 'store_locality_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    // Scopes
    public function scopeByStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeCreatedBy($query, $userId)
    {
        return $query->where('created_by', $userId);
    }

    public function scopeAssignedTo($query, $userId)
    {
        return $query->where('assigned_delivery', $userId);
    }

    public function scopeByStore($query, $storeId)
    {
        return $query->where('store_id', $storeId);
    }

    public function scopeByDateRange($query, $fromDate, $toDate = null)
    {
        $query->whereDate('created_at', '>=', $fromDate);
        
        if ($toDate) {
            $query->whereDate('created_at', '<=', $toDate);
        }

        return $query;
    }

    public function scopeToday($query)
    {
        return $query->whereDate('created_at', date('Y-m-d'));
    }

    public function scopeThisMonth($query)
    {
        return $query->whereMonth('created_at', now()->month)
                     ->whereYear('created_at', now()->year);
    }

    public function scopeDelivered($query)
    {
        return $query->where('status', 'Delivered');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'Pending');
    }

    public function scopeWithDeliveryAssigned($query)
    {
        return $query->whereNotNull('assigned_delivery');
    }


}

