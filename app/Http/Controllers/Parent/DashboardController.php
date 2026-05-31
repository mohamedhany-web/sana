<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Services\Parent\ParentDashboardService;
use App\Services\Parent\ParentGuardianService;

class DashboardController extends Controller
{
    public function __construct(
        private ParentDashboardService $dashboard,
        private ParentGuardianService $guardianService,
    ) {}

    public function index()
    {
        $parent = auth()->user();
        $stats = $this->dashboard->overviewStats($parent);
        $children = $this->dashboard->linkedStudents($parent);
        $showPasswordNotice = $this->guardianService->usesDefaultPassword($parent) || $parent->must_change_password;

        return view('parent.dashboard.index', compact('stats', 'children', 'showPasswordNotice'));
    }
}
