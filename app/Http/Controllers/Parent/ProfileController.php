<?php

namespace App\Http\Controllers\Parent;

use App\Http\Controllers\Controller;
use App\Services\UserProfileImageStorage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        return view('parent.profile.index', [
            'user' => $user,
            'profileImageUrl' => $user->profile_image_url,
        ]);
    }

    public function update(Request $request)
    {
        $user = auth()->user();

        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'nullable|string|max:20|unique:users,phone,'.$user->id,
            'email' => 'required|email|max:255|unique:users,email,'.$user->id,
            'current_password' => 'nullable|string',
            'password' => 'nullable|string|min:8|confirmed',
            'profile_image' => 'nullable|image|max:'.config('upload_limits.max_upload_kb'),
        ], [
            'name.required' => 'الاسم مطلوب',
            'email.required' => 'البريد الإلكتروني مطلوب',
            'email.unique' => 'البريد الإلكتروني مستخدم من قبل',
            'password.min' => 'كلمة المرور يجب أن تكون 8 أحرف على الأقل',
            'password.confirmed' => 'تأكيد كلمة المرور غير متطابق',
        ]);

        if ($request->filled('password')) {
            if (! $request->filled('current_password') || ! Hash::check($request->current_password, $user->password)) {
                return back()->withErrors(['current_password' => 'كلمة المرور الحالية غير صحيحة']);
            }
        }

        $data = [
            'name' => $request->name,
            'email' => $request->email,
        ];

        if ($request->filled('phone') && ! str_starts_with($request->phone, 'PARENT_')) {
            $data['phone'] = $request->phone;
        }

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
            $data['must_change_password'] = false;
        }

        if ($request->hasFile('profile_image')) {
            UserProfileImageStorage::delete($user->profile_image);
            $data['profile_image'] = UserProfileImageStorage::store($request->file('profile_image'));
        }

        $user->update($data);

        return back()->with('success', 'تم تحديث بياناتك بنجاح');
    }
}
