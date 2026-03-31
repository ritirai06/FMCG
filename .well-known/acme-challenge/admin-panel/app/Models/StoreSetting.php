<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StoreSetting extends Model
{
    use HasFactory;

    protected $fillable = ['store_id','notifications_enabled','sync_enabled','notes'];

    protected $casts = [
        'notifications_enabled' => 'boolean',
        'sync_enabled' => 'boolean',
    ];
    public function store(){
        return $this->belongsTo(Store::class,'store_id');
    }
}
