<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    protected $fillable = ['created_by', 'category', 'amount', 'notes', 'expense_date'];

    public function createdBy() { return $this->belongsTo(User::class, 'created_by'); }
}
