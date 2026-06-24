<?php

namespace App\Services\Front;

use App\Mail\ContactMail;
use Illuminate\Support\Facades\Mail;

/**
 * Contact-form orchestration: builds the mail payload, resolves the recipient
 * (admin-configured contact email, falling back to config), and sends the
 * message. The mail send is the primary user action of the contact form (not a
 * side effect of a DB write), so it lives in the service rather than an
 * observer. No data access is involved beyond the get_contact_email() helper.
 */
class ContactService
{
    /**
     * Send a contact-form submission to the configured recipient.
     */
    public function send($request): void
    {
        $data = [
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'subject' => $request->subject,
            'email' => $request->email,
            'message' => $request->message,
        ];

        $contact_mail = get_contact_email();

        if (! $contact_mail) {
            $contact_mail = config('mail.contact_address');
        }

        Mail::to($contact_mail)->send(new ContactMail($data));
    }
}
