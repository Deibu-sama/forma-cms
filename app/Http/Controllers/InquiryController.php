<?php

namespace App\Http\Controllers;

use App\Services\Firestore\ContentRepository;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Mail;
use Throwable;

final class InquiryController
{
    public function store(Request $request, ContentRepository $content): JsonResponse
    {
        $validated = $request->validate([
            'full_name' => ['required', 'string', 'max:120'],
            'email' => ['required', 'email:rfc', 'max:254'],
            'business_name' => ['nullable', 'string', 'max:160'],
            'service_required' => [
                'nullable',
                'string',
                Rule::in([
                    'Monthly bookkeeping',
                    'Reconciliation support',
                    'Financial reporting prep',
                    'Catch-up and cleanup',
                    'Not sure yet',
                ]),
            ],
            'accounting_software' => ['nullable', 'string', 'max:100'],
            'monthly_transaction_volume' => [
                'nullable',
                'string',
                Rule::in([
                    'Under 100',
                    '100–500',
                    '501–1500',
                    '1500+',
                    'Not sure',
                ]),
            ],
            'message' => ['required', 'string', 'max:3000'],
            'website' => ['nullable', 'string', 'max:255'],
        ]);

        // Honeypot: respond as if accepted, but do not persist automated spam.
        if (! empty($validated['website'])) {
            return response()->json([
                'message' => 'Thank you. Your enquiry has been received.',
            ], 202);
        }

        if (! $content->createInquiry($validated)) {
            return response()->json([
                'message' => 'The enquiry form is temporarily unavailable. Please use the email link on this page, if available, or try again later.',
            ], 503);
        }

        $recipient = $content->settings()['contact_email'] ?? '';
        if (is_string($recipient) && filter_var($recipient, FILTER_VALIDATE_EMAIL)) {
            try {
                $subject = 'New portfolio enquiry from ' . trim($validated['full_name']);
                $body = implode("\\n", [
                    'You have received a new enquiry through your portfolio website.',
                    '',
                    'Name: ' . trim($validated['full_name']),
                    'Email: ' . trim($validated['email']),
                    'Business: ' . trim((string) ($validated['business_name'] ?? 'Not provided')),
                    'Service requested: ' . trim((string) ($validated['service_required'] ?? 'Not specified')),
                    'Accounting software: ' . trim((string) ($validated['accounting_software'] ?? 'Not specified')),
                    '',
                    'Message:',
                    trim($validated['message']),
                ]);
                Mail::raw($body, function ($mail) use ($recipient, $subject, $validated): void {
                    $mail->to($recipient)->replyTo($validated['email'], $validated['full_name'])->subject($subject);
                });
            } catch (Throwable $exception) {
                report($exception);
                // The inquiry is already persisted; never lose it just because email delivery failed.
            }
        }

        return response()->json([
            'message' => 'Thank you. Your enquiry has been received.',
        ], 201);
    }
}
