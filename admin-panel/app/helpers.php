<?php

use App\Models\AdminSetting;

if (!function_exists('admin_setting')) {

    function admin_setting()
    {
        $setting = AdminSetting::first();
        
        // Return default object if no settings exist
        if (!$setting) {
            $setting = new AdminSetting();
            $setting->company_name = 'Admin Panel';
            $setting->profile_image = null;
        }
        
        return $setting;
    }
}