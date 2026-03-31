<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminSetting extends Model
{
    protected $fillable = [
        'name','email','phone','profile_image',
        'company_name','gst_number',
        'company_email','company_phone','company_address',
        'currency','language','timezone',
        // preferences toggles
        'email_notifications','dark_mode','maintenance_mode'
    ];
}