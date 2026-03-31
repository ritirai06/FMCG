<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class City extends Model
{
    protected $fillable = ['name','state','status'];

    public function localities()
    {
        return $this->hasMany(Locality::class);
    }
}
