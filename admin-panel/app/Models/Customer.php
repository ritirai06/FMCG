<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    protected $fillable = [
        'created_by', 'business_name', 'code', 'contact_person', 'mobile', 'email',
        'status', 'verified', 'route', 'group_name', 'geolocation',
        'billing_address', 'shipping_address', 'gstin', 'opening_balance',
        'credit_period', 'credit_limit', 'credit_bill_limit',
        'state_of_supply', 'document_path',
    ];

    protected $casts = [
        'verified'        => 'boolean',
        'opening_balance' => 'decimal:2',
        'credit_limit'    => 'decimal:2',
        'credit_bill_limit' => 'decimal:2',
    ];

    public function createdBy() { return $this->belongsTo(\App\Models\User::class, 'created_by'); }
    public function payments()  { return $this->hasMany(\App\Models\Payment::class); }
    public function returns()   { return $this->hasMany(\App\Models\SaleReturn::class); }
}
