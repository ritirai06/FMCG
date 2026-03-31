<?php

use App\Models\AdminSetting;

if (!function_exists('admin_setting')) {

    function admin_setting()
    {
        return AdminSetting::first();
    }
}