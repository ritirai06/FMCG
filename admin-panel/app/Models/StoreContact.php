<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreContact extends Model
{
    use HasFactory;

    protected $fillable = ['store_id','contact_person','phone','email'];
    public function store(){
        return $this->belongsTo(Store::class,'store_id');
    }
}
