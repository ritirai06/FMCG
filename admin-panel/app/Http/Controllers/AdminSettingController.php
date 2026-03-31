<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AdminSetting;

class AdminSettingController extends Controller
{

    // SHOW PAGE
    public function index()
    {
        $setting = AdminSetting::first();
        $settings = AdminSetting::all(); // For list display

        return view('admin.settings', compact('setting', 'settings'));
    }

    // UPDATE SETTINGS
    public function update(Request $request)
    {
        $setting = AdminSetting::first();

        if(!$setting){
            $setting = new AdminSetting();
        }

        // IMAGE UPLOAD
        if($request->hasFile('profile_image')){
            $image = time().'.'.$request->profile_image->extension();
            $request->profile_image->move(public_path('uploads/admin'), $image);
            $setting->profile_image = $image;
        }

        $setting->fill($request->except('profile_image'));
        $setting->save();

        return back()->with('success','Settings Updated Successfully');
    }

    // DELETE SETTINGS
    public function destroy($id)
    {
        $setting = AdminSetting::find($id);

        if(!$setting){
            return back()->with('error', 'Setting not found');
        }

        $setting->delete();

        return back()->with('success', 'Setting deleted successfully');
    }
}