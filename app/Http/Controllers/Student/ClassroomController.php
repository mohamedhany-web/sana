<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/**
 * إدارة Classroom انتقلت إلى لوحة المدرب. هذه المسارات للتوجيه فقط.
 */
class ClassroomController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function redirectAway(Request $request)
    {
        $user = Auth::user();

        if ($user && ($user->isInstructor() || $user->isTeacher())) {
            return redirect()
                ->route('instructor.classroom.index')
                ->with('info', 'تم نقل إدارة اللايف ميتينج (Classroom) إلى لوحة المدرب.');
        }

        return redirect()
            ->route('student.dashboard')
            ->with('info', 'إنشاء وإدارة اللايف ميتينج متاحة للمدرب فقط. يمكنك الانضمام عبر رابط الدعوة من معلمك.');
    }
}
