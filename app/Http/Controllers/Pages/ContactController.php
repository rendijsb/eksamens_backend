<?php

declare(strict_types=1);

namespace App\Http\Controllers\Pages;

use App\Http\Controllers\Controller;
use App\Http\Resources\Pages\ContactResource;
use App\Mail\ContactFormAutoReply;
use App\Mail\ContactFormSubmission;
use App\Models\Pages\Contact;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{
    public function getContactInfo(): Response|ContactResource
    {
        $contact = Contact::first();

        if (!$contact) {
            return response()->noContent();
        }

        return new ContactResource($contact);
    }

    public function updateContactInfo(Request $request): ContactResource
    {
        $validated = $request->validate([
            Contact::ADDRESS => 'required|string|max:255',
            Contact::EMAIL => 'required|email|max:255',
            Contact::PHONE => 'required|string|max:20',
            Contact::WORKING_HOURS => 'nullable|string|max:255',
            Contact::MAP_EMBED_CODE => 'nullable|string',
            Contact::ADDITIONAL_INFO => 'nullable|string',
        ]);

        $contact = Contact::first();

        if ($contact) {
            $contact->update($validated);
        } else {
            $contact = Contact::create($validated);
        }

        return new ContactResource($contact);
    }

    public function sendContactForm(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'subject' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
        ]);

        Mail::to(config('mail.from.address'))->send(new ContactFormSubmission($validated));

        Mail::to($validated['email'])->send(new ContactFormAutoReply($validated));

        return response()->json([
            'success' => true,
            'message' => 'Jūsu ziņojums ir sekmīgi nosūtīts. Mēs ar jums sazināsimies drīzumā.'
        ]);
    }
}
