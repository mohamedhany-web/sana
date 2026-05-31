<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;

class SettingsController extends Controller
{
    public function index()
    {
        return view('parent.settings.index');
    }
}
