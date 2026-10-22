@extends('admin.layout')

@section('title', 'Enquiries')
@section('page-title', 'Enquiries')
@section('eyebrow', 'Website / Inbox')

@section('content')
    <div class="mb-7">
        <p class="mb-2 text-[10px] font-bold uppercase tracking-[.2em] text-[#a18755]">Private inbox</p>
        <h2 class="text-3xl font-semibold tracking-[-.055em]">Website enquiries</h2>
        <p class="mt-3 max-w-2xl text-sm leading-6 text-[#68727b]">Review recent contact-form messages and track each follow-up. Only administrators can access this page.</p>
    </div>

    @unless($firestoreReady)
        <div role="status" class="mb-6 rounded-xl border border-[#b99a61]/40 bg-[#f2e8d2] p-4 text-sm">
            <p class="font-semibold">Firestore is not connected</p>
            <p class="mt-1 text-xs leading-5">Enquiries are stored in Firestore and cannot be displayed until server credentials and access are configured. This page will not fabricate or substitute sample enquiries.</p>
        </div>
    @endunless

    @if(count($inquiries) === 0)
        <section class="rounded-xl border border-[#14283f]/10 bg-[#fffdf8] px-6 py-14 text-center">
            <span class="mx-auto grid h-12 w-12 place-items-center rounded-xl bg-[#f4f0e6] text-[#14283f]" aria-hidden="true">✉</span>
            <h3 class="mt-4 text-base font-semibold">No enquiries to show</h3>
            <p class="mx-auto mt-2 max-w-md text-sm leading-6 text-[#68727b]">{{ $firestoreReady ? 'New contact-form messages will appear here when someone reaches out.' : 'Connect Firestore to review saved contact-form messages.' }}</p>
        </section>
    @else
        <div class="mb-4 flex items-center justify-between gap-3 text-xs text-[#68727b]">
            <p>Showing up to {{ count($inquiries) }} recent enquiries.</p>
            <span>Newest first</span>
        </div>
        <div class="space-y-4">
            @foreach($inquiries as $inquiry)
                <article class="rounded-xl border border-[#14283f]/10 bg-[#fffdf8] p-5 sm:p-6">
                    <div class="flex flex-col justify-between gap-4 sm:flex-row sm:items-start">
                        <div class="min-w-0">
                            <div class="flex flex-wrap items-center gap-2">
                                <h3 class="text-base font-semibold">{{ $inquiry['full_name'] ?? 'Unnamed contact' }}</h3>
                                <span class="rounded-full bg-[#f4f0e6] px-2.5 py-1 text-[10px] font-semibold">{{ str_replace('_', ' ', ucfirst($inquiry['status'] ?? 'new')) }}</span>
                            </div>
                            <a class="mt-1 inline-block break-all text-sm text-[#80683f] underline underline-offset-2" href="mailto:{{ $inquiry['email'] ?? '' }}">{{ $inquiry['email'] ?? 'No email provided' }}</a>
                            <p class="mt-2 text-xs leading-5 text-[#68727b]">
                                @if(!empty($inquiry['business_name'])){{ $inquiry['business_name'] }} · @endif
                                {{ $inquiry['service_required'] ?? 'Service not specified' }}
                                @if(!empty($inquiry['created_at']) && $inquiry['created_at'] instanceof \DateTimeInterface)
                                    · {{ $inquiry['created_at']->format('M j, Y · g:i A') }}
                                @endif
                            </p>
                        </div>
                        <form method="POST" action="{{ route('admin.inquiries.status', $inquiry['id']) }}" class="flex w-full flex-col gap-2 sm:w-auto sm:min-w-52">
                            @csrf @method('PATCH')
                            <label for="status-{{ $inquiry['id'] }}" class="text-[11px] font-semibold">Follow-up status</label>
                            <select id="status-{{ $inquiry['id'] }}" name="status" class="form-field text-sm">
                                @foreach(['new' => 'New', 'contacted' => 'Contacted', 'in_discussion' => 'In discussion', 'closed' => 'Closed'] as $value => $label)
                                    <option value="{{ $value }}" @selected(($inquiry['status'] ?? 'new') === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            <button type="submit" class="inline-flex min-h-10 items-center justify-center rounded-lg bg-[#14283f] px-4 py-2 text-xs font-bold text-white hover:bg-[#263e56]">Save status</button>
                        </form>
                    </div>
                    <div class="mt-5 rounded-lg bg-[#f7f5ef] p-4">
                        <p class="mb-2 text-[10px] font-bold uppercase tracking-[.14em] text-[#68727b]">Message</p>
                        <p class="whitespace-pre-line break-words text-sm leading-6 text-[#26394d]">{{ $inquiry['message'] ?? '' }}</p>
                        @if(!empty($inquiry['accounting_software']) || !empty($inquiry['monthly_transaction_volume']))
                            <p class="mt-3 text-xs text-[#68727b]">
                                @if(!empty($inquiry['accounting_software']))Software: {{ $inquiry['accounting_software'] }}@endif
                                @if(!empty($inquiry['accounting_software']) && !empty($inquiry['monthly_transaction_volume'])) · @endif
                                @if(!empty($inquiry['monthly_transaction_volume']))Monthly volume: {{ $inquiry['monthly_transaction_volume'] }}@endif
                            </p>
                        @endif
                    </div>
                </article>
            @endforeach
        </div>
    @endif
@endsection
