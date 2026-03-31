<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SalaryPayout extends Model
{
    protected $table = 'salary_payouts';
    protected $fillable = ['employee_name','month','year','base_salary','allowances','sales','incentive','total_payout'];
    protected $casts = [
        'base_salary' => 'decimal:2',
        'allowances' => 'decimal:2',
        'sales' => 'decimal:2',
        'incentive' => 'decimal:2',
        'total_payout' => 'decimal:2',
    ];
}
