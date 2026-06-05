<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AcademicSubject;
use App\Models\AcademicYear;
use App\Models\InstructorProfile;
use App\Services\InstructorApplicationService;
use Illuminate\Http\Request;

class InstructorApplicationsController extends Controller
{
    public function index(Request $request)
    {
        $query = InstructorProfile::query()
            ->with('user')
            ->whereNotNull('submitted_at')
            ->orderByDesc('submitted_at');

        if ($status = $request->string('status')->toString()) {
            $query->where('status', $status);
        }

        if ($search = $request->string('search')->trim()->toString()) {
            $query->whereHas('user', function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $applications = $query->paginate(20)->withQueryString();

        $stats = [
            'pending' => InstructorProfile::where('status', InstructorProfile::STATUS_PENDING_REVIEW)
                ->whereNotNull('submitted_at')->count(),
            'approved' => InstructorProfile::where('status', InstructorProfile::STATUS_APPROVED)
                ->whereNotNull('submitted_at')->count(),
            'rejected' => InstructorProfile::where('status', InstructorProfile::STATUS_REJECTED)
                ->whereNotNull('submitted_at')->count(),
            'total' => InstructorProfile::whereNotNull('submitted_at')->count(),
        ];

        $publicApplyUrl = route('tutor.apply');

        return view('admin.instructor-applications.index', compact('applications', 'stats', 'publicApplyUrl'));
    }

    public function show(InstructorProfile $application)
    {
        if (! $application->submitted_at) {
            abort(404);
        }

        $application->load(['user', 'reviewedByUser']);
        $subjects = AcademicSubject::whereIn('id', $application->tutor_subject_ids ?? [])->get();
        $years = AcademicYear::whereIn('id', $application->tutor_academic_year_ids ?? [])->get();

        return view('admin.instructor-applications.show', compact('application', 'subjects', 'years'));
    }

    public function approve(Request $request, InstructorProfile $application)
    {
        $data = $request->validate([
            'admin_note' => ['nullable', 'string', 'max:2000'],
        ]);

        if ($application->status === InstructorProfile::STATUS_APPROVED) {
            return back()->with('info', 'هذا الطلب مقبول مسبقاً.');
        }

        InstructorApplicationService::approve($application, $request->user(), $data['admin_note'] ?? null);

        return redirect()
            ->route('admin.instructor-applications.index', ['status' => InstructorProfile::STATUS_PENDING_REVIEW])
            ->with('success', 'تم قبول المعلم وتفعيل حسابه — يمكنه تسجيل الدخول الآن.');
    }

    public function reject(Request $request, InstructorProfile $application)
    {
        $data = $request->validate([
            'rejection_reason' => ['required', 'string', 'max:5000'],
        ]);

        InstructorApplicationService::reject($application, $request->user(), $data['rejection_reason']);

        return redirect()
            ->route('admin.instructor-applications.index', ['status' => InstructorProfile::STATUS_PENDING_REVIEW])
            ->with('success', 'تم رفض الطلب وإبلاغ المعلم.');
    }
}
