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
        // Honeypot: bots fill hidden fields; humans never see them.
        if ($request->filled('website') || $request->filled('company_url')) {
            \Log::warning('Contact form honeypot tripped', [
                'ip' => $request->ip(),
                'ua' => substr((string) $request->userAgent(), 0, 200),
            ]);

            return redirect()->route('public.contact')
                ->with('success', 'تم إرسال رسالتك بنجاح. سنتواصل معك قريباً!');
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:5000',
        ]);

        $message = ContactMessage::create(array_merge($validated, [
            'source' => 'contact_page',
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 500),
            'referrer' => substr((string) $request->headers->get('referer'), 0, 500) ?: null,
        ]));

        try {
            app(ContactMessageAlertService::class)->notifyAdmins($message);
        } catch (\Throwable $e) {
            report($e);
        }

        return redirect()->route('public.contact')
            ->with('success', 'تم إرسال رسالتك بنجاح. سنتواصل معك قريباً!');
    }
}
