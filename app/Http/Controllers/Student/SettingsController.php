<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Support\UserAppPreferences;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $preferences = UserAppPreferences::forUser($user);

        return view('student.settings.index', compact('user', 'preferences'));
    }

    public function update(Request $request)
    {
        $user = Auth::user();

        $request->validate([
            'locale' => 'required|in:ar,en',
            'theme' => 'required|in:light,dark,auto',
        ]);

        $preferences = UserAppPreferences::normalizeFromRequest($request->all());

        $user->update(['app_preferences' => $preferences]);

        $locale = UserAppPreferences::localeForUser($user->fresh());
        session([
            'locale' => $locale,
            'landing_locale' => $locale,
            'app_theme' => $preferences['theme'],
        ]);

        return back()->with('success', 'تم حفظ الإعدادات بنجاح');
    }
}
