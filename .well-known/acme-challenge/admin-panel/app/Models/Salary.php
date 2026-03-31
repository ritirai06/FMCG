<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Salary extends Model
{
    protected $table = 'salaries';

    protected $fillable = [
        'employee_name', 'role', 'base_salary', 'allowances'
    ];

    protected $casts = [
        'base_salary' => 'decimal:2',
        'allowances' => 'decimal:2',
    ];

    public function total()
    {
        return floatval($this->base_salary) + floatval($this->allowances);
    }
}
