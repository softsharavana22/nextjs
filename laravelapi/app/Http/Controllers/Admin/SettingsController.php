<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingsController extends Controller
{
    /**
     * Get site settings
     */
    public function index()
    {
        $settings = Setting::first();

        return response()->json([
            'status' => 200,
            'data' => $settings
        ]);
    }

    /**
     * Update site settings
     */
    public function update(Request $request)
    {
        $request->validate([
            'site_name'     => 'nullable|string|max:255',
            'facebook_link' => 'nullable|url|max:255',
            'twitter_link'  => 'nullable|url|max:255',
        ]);

        // Single-row pattern
        $settings = Setting::firstOrCreate([]);

        $settings->update([
            'site_name'     => $request->site_name,
            'facebook_link' => $request->facebook_link,
            'twitter_link'  => $request->twitter_link,
        ]);

        return response()->json([
            'status'  => 200,
            'message' => 'Settings updated successfully',
            'data'    => $settings
        ]);
    }    
}
