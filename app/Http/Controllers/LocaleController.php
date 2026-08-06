<?php

namespace App\Http\Controllers;

use App\Support\UserAppPreferences;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;

class LocaleController extends Controller
{
    /**
     * تبديل لغة الواجهة العامة بين العربية والإنجليزية.
     */
    public function switch(Request $request, string $locale): RedirectResponse
    {
        $normalized = $locale === 'en' ? 'en' : 'ar_SA';

        session([
            'locale' => $normalized,
            'landing_locale' => $normalized,
        ]);
        App::setLocale($normalized);

        $user = $request->user();
        if ($user) {
            $prefs = UserAppPreferences::forUser($user);
            $prefs['locale'] = $normalized === 'en' ? 'en' : 'ar';
            $user->forceFill(['app_preferences' => $prefs])->save();
        }

        $redirect = $request->query('redirect');
        if (is_string($redirect) && $redirect !== '' && str_starts_with($redirect, url('/'))) {
            return redirect()->to($redirect);
        }

        return redirect()->back(fallback: route('home'));
    }
}
