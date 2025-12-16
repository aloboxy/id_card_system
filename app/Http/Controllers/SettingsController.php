<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Setting;
use Illuminate\Support\Facades\Storage;

class SettingsController extends Controller
{
    public function edit()
    {
        $systemName = Setting::getValue('system_name', 'ID Card System');
        $systemLogo = Setting::getValue('system_logo');

        return view('settings.edit', compact('systemName', 'systemLogo'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'system_name' => 'required|string|max:255',
            'system_logo' => 'nullable|image|max:2048',
        ]);

        Setting::setValue('system_name', $request->system_name);

        if ($request->hasFile('system_logo')) {
            // Delete old logo if exists
            $oldLogo = Setting::getValue('system_logo');
            if ($oldLogo && Storage::disk('public')->exists($oldLogo)) {
                Storage::disk('public')->delete($oldLogo);
            }

            $path = $request->file('system_logo')->store('settings', 'public');
            Setting::setValue('system_logo', $path);
        }

        return redirect()->route('settings.edit')->with('success', 'Settings updated successfully.');
    }
}
