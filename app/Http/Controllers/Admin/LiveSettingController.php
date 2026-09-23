<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LiveSetting;
use Illuminate\Http\Request;

class LiveSettingController extends Controller
{
    public function index()
    {
        $settings = LiveSetting::orderBy('group')->orderBy('id')->get()->groupBy('group');
        return view('admin.live-settings.index', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'settings'        => 'required|array',
            'settings.*.key'  => 'required|string',
            'settings.*.value' => 'nullable|string',
        ]);

        foreach ($data['settings'] as $item) {
            LiveSetting::set($item['key'], $item['value'] ?? '');
        }

        return back()->with('success', __('تم حفظ إعدادات البث المباشر بنجاح'));
    }
}
