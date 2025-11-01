<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    /**
     * Display settings page.
     */
    public function index()
    {
        $settings = Setting::all()->pluck('value', 'key')->toArray();
        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Update settings.
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'site_name' => 'required|string|max:255',
            'contact_email' => 'required|email|max:255',
            'contact_phone' => 'required|string|max:20',
            'delivery_fee_lagos' => 'required|numeric|min:0',
            'delivery_fee_nationwide' => 'required|numeric|min:0',
            'delivery_fee_pickup' => 'required|numeric|min:0',
            'low_stock_threshold' => 'required|integer|min:1',
            'logo' => 'nullable|image|mimes:jpeg,jpg,png|max:2048',
        ]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $oldLogo = Setting::get('logo_path');
            if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }

            $path = $request->file('logo')->store('logos', 'public');
            Setting::set('logo_path', $path);
        }

        // Update all other settings
        foreach ($validated as $key => $value) {
            if ($key !== 'logo') {
                Setting::set($key, $value);
            }
        }

        Setting::clearCache();

        return back()->with('success', 'Settings updated successfully.');
    }
}