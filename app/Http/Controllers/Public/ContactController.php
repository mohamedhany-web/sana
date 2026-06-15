<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use App\Services\ContactMessageAlertService;
use App\Support\PublicContactInfo;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index()
    {
        $contact = PublicContactInfo::payload();

        return view('public.contact', [
            'contact' => $contact,
            'supportEmail' => $contact['email'],
            'supportPhone' => $contact['phone'],
            'whatsappUrl' => $contact['whatsapp_url'],
            'socials' => $contact['socials'],
            'responseCards' => PublicContactInfo::responseExpectations(),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        $message = ContactMessage::create($validated);

        try {
            app(ContactMessageAlertService::class)->notifyAdmins($message);
        } catch (\Throwable $e) {
            report($e);
        }

        return redirect()->route('public.contact')
            ->with('success', 'تم إرسال رسالتك بنجاح. سنتواصل معك قريباً!');
    }
}
