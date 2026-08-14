<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

class ForgotPasswordController extends Controller
{
    /**
     * عرض نموذج طلب رابط إعادة تعيين كلمة المرور
     */
    public function showLinkRequestForm()
    {
        $authBackgroundUrl = \Illuminate\Support\Facades\Storage::disk('public')->exists(\App\Providers\AppServiceProvider::AUTH_BACKGROUND_STORAGE_PATH)
            ? public_storage_url(\App\Providers\AppServiceProvider::AUTH_BACKGROUND_STORAGE_PATH)
            : asset('images/brainstorm-meeting.jpg');
        return view('auth.forgot-password', compact('authBackgroundUrl'));
    }

    /**
     * إرسال رابط إعادة التعيين إلى البريد المسجل (مع التحقق من وجود البريد)
     */
    public function sendResetLinkEmail(Request $request)
    {
        $validator = Validator::make($request->only('email'), [
            'email' => ['required', 'email', 'max:255'],
        ], [
            'email.required' => __('auth.email_required'),
            'email.email' => __('auth.email_invalid'),
        ]);

        if ($validator->fails()) {
            return back()->withErrors($validator)->withInput();
        }

        $email = trim((string) $request->input('email'));
        $user = User::whereRaw('LOWER(email) = ?', [strtolower($email)])->first();

        if (! $user) {
            return back()->withErrors(['email' => __('auth.email_not_registered')])->withInput();
        }

        // المعلمون بحساب مسودة / قيد المراجعة يمكنهم استعادة كلمة المرور
        $openInstructor = \App\Services\TutorApplicationFormService::isOpenInstructorAccount($user);
        if (! $user->is_active && ! $openInstructor) {
            return back()->withErrors(['email' => __('auth.account_inactive')])->withInput();
        }

        if (empty($user->email) || ! filter_var($user->email, FILTER_VALIDATE_EMAIL)) {
            return back()->withErrors(['email' => __('auth.email_invalid')])->withInput();
        }

        try {
            $status = Password::broker()->sendResetLink([
                'email' => $user->email,
            ]);
        } catch (\Throwable $e) {
            Log::error('password reset email failed', [
                'user_id' => $user->id,
                'email' => $user->email,
                'error' => $e->getMessage(),
            ]);

            return back()->withErrors([
                'email' => __('auth.reset_link_mail_error'),
            ])->withInput();
        }

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('status', __('auth.reset_link_sent'));
        }

        if ($status === Password::RESET_THROTTLED) {
            return back()->withErrors([
                'email' => __('auth.reset_link_throttled'),
            ])->withInput();
        }

        Log::warning('password reset link not sent', [
            'user_id' => $user->id,
            'email' => $user->email,
            'status' => $status,
        ]);

        return back()->withErrors(['email' => __('auth.reset_link_failed')])->withInput();
    }
}
