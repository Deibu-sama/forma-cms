@extends('admin.layout')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard')
@section('eyebrow', 'Overview')

@section('content')
    <div class="mb-8 flex flex-col justify-between gap-5 md:flex-row md:items-end">
        <div>
            <p class="mb-2 text-[10px] font-bold uppercase tracking-[.2em] text-[#a18755]">Your workspace</p>
            <h2 class="text-3xl font-semibold tracking-[-.055em] sm:text-4xl">Good day, {{ explode(' ', trim($administrator->name))[0] }}.</h2>
            <p class="mt-3 max-w-2xl text-sm leading-6 text-[#68727b]">Manage the brand, messaging, and public-facing details visitors see on your bookkeeping portfolio.</p>
        </div>
        <a href="{{ route('admin.settings') }}" class="button-lift inline-flex min-h-11 items-center justify-center gap-3 rounded-md bg-[#14283f] px-5 py-3 text-xs font-bold text-white no-underline hover:bg-[#263e56]">Edit website settings <span class="text-[#b99a61]">↗</span></a>
    </div>

    @if($demoMode)
        <div class="mb-7 flex items-start gap-3 rounded-xl border border-[#b99a61]/35 bg-[#f2e8d2] p-4 sm:p-5">
            <span class="mt-0.5 grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-[#b99a61]/25 text-[#14283f]">!</span>
            <div>
                <p class="text-xs font-bold">Firestore is not connected</p>
                <p class="mt-1 max-w-3xl text-xs leading-5 text-[#615846]">The public website is showing demonstration content. You can review the settings form, but saving changes needs a valid Firestore project and server credentials with permission to write to <code class="rounded bg-white/70 px-1 py-0.5">site_settings/main</code>.</p>
            </div>
        </div>
    @else
        <div class="mb-7 flex items-start gap-3 rounded-xl border border-emerald-900/15 bg-emerald-50 p-4 sm:p-5">
            <span class="mt-0.5 grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-emerald-900/10 text-emerald-900">✓</span>
            <div><p class="text-xs font-bold text-emerald-950">Firestore is connected</p><p class="mt-1 text-xs leading-5 text-emerald-900/75">Public settings and published portfolio entries are being read through Laravel.</p></div>
        </div>
    @endif

    <div class="grid gap-4 sm:grid-cols-2 xl:grid-cols-3">
        <article class="rounded-xl border border-[#14283f]/10 bg-[#fffdf8] p-5 sm:p-6">
            <div class="flex items-start justify-between"><span class="grid h-10 w-10 place-items-center rounded-lg bg-[#e9dfca] text-[#14283f]"><svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7"><rect x="3" y="3" width="18" height="18" rx="2"/><path d="M8 8h8M8 12h8M8 16h4"/></svg></span><span class="rounded-full bg-[#f2ead9] px-2.5 py-1 text-[9px] font-bold uppercase tracking-wider text-[#866b3e]">{{ $demoMode ? 'Sample' : 'Published' }}</span></div>
            <p class="mt-5 text-xs font-semibold text-[#68727b]">{{ $demoMode ? 'Example work samples' : 'Published projects' }}</p>
            <p class="mt-1 text-3xl font-semibold tracking-tight">{{ $projectCount }}</p>
            <p class="mt-2 text-[11px] leading-5 text-[#68727b]">{{ $demoMode ? 'Illustrative content used while Firestore is unavailable.' : 'Projects available to the public portfolio.' }}</p>
        </article>
        <article class="rounded-xl border border-[#14283f]/10 bg-[#fffdf8] p-5 sm:p-6">
            <div class="flex items-start justify-between"><span class="grid h-10 w-10 place-items-center rounded-lg bg-[#e4e9df] text-[#49634e]"><svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7"><circle cx="12" cy="12" r="9"/><path d="m8 12 2.5 2.5L16 9"/></svg></span><span class="rounded-full bg-[#e4e9df] px-2.5 py-1 text-[9px] font-bold uppercase tracking-wider text-[#49634e]">Ready</span></div>
            <p class="mt-5 text-xs font-semibold text-[#68727b]">Website identity</p>
            <p class="mt-1 text-xl font-semibold tracking-tight">{{ $settings['site_name'] ?? 'Bookkeeper Pro Portfolio' }}</p>
            <p class="mt-2 text-[11px] leading-5 text-[#68727b]">Site title, favicon, brand name, and SEO description.</p>
            <a href="{{ route('admin.settings') }}" class="mt-4 inline-flex items-center gap-2 text-xs font-bold text-[#14283f] no-underline hover:text-[#9b7a42]">Manage settings <span class="text-[#b99a61]">↗</span></a>
        </article>
        <article class="rounded-xl border border-[#14283f]/10 bg-[#fffdf8] p-5 sm:p-6 sm:col-span-2 xl:col-span-1">
            <div class="flex items-start justify-between"><span class="grid h-10 w-10 place-items-center rounded-lg bg-[#14283f] text-[#b99a61]"><svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M4 5h16v12H7l-3 3V5Z"/><path d="M8 9h8M8 13h5"/></svg></span><span class="rounded-full bg-[#f2ead9] px-2.5 py-1 text-[9px] font-bold uppercase tracking-wider text-[#866b3e]">Backend</span></div>
            <p class="mt-5 text-xs font-semibold text-[#68727b]">Enquiry form</p>
            <p class="mt-1 text-xl font-semibold tracking-tight">Protected endpoint</p>
            <p class="mt-2 text-[11px] leading-5 text-[#68727b]">Validation, CSRF protection, rate limiting, and private Firestore persistence when configured.</p>
            <p class="mt-3 text-[10px] font-semibold text-[#866b3e]">Inbox management is a future milestone.</p>
        </article>
    </div>

    <div class="mt-8 grid gap-5 xl:grid-cols-[1.15fr_.85fr]">
        <section class="rounded-xl border border-[#14283f]/10 bg-[#fffdf8] p-5 sm:p-7">
            <div class="flex items-start justify-between gap-4">
                <div><p class="text-[9px] font-bold uppercase tracking-[.2em] text-[#a18755]">Quick action</p><h3 class="mt-2 text-xl font-semibold tracking-tight">Website settings</h3><p class="mt-2 max-w-xl text-xs leading-5 text-[#68727b]">Control your public site identity and primary copy from one place.</p></div>
                <span class="hidden h-11 w-11 place-items-center rounded-xl bg-[#f4f0e6] text-[#14283f] sm:grid"><svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M12 3v2M12 19v2M4.2 4.2l1.4 1.4m12.8 12.8 1.4 1.4M3 12h2m14 0h2M4.2 19.8l1.4-1.4M18.4 5.6l1.4-1.4"/><circle cx="12" cy="12" r="5"/></svg></span>
            </div>
            <div class="mt-6 grid gap-3 sm:grid-cols-2">
                <a href="{{ route('admin.settings') }}" class="group flex items-start gap-3 rounded-lg border border-[#14283f]/10 p-4 no-underline transition hover:border-[#b99a61]/60 hover:bg-[#f8f4eb]">
                    <span class="mt-0.5 text-[#b99a61]">01</span><span><span class="block text-xs font-bold text-[#14283f]">Brand & browser title</span><span class="mt-1 block text-[10px] leading-5 text-[#68727b]">Site name, tab title, favicon URL and SEO</span></span><span class="ml-auto text-[#b99a61]">↗</span>
                </a>
                <a href="{{ route('admin.settings') }}#homepage-copy" class="group flex items-start gap-3 rounded-lg border border-[#14283f]/10 p-4 no-underline transition hover:border-[#b99a61]/60 hover:bg-[#f8f4eb]">
                    <span class="mt-0.5 text-[#b99a61]">02</span><span><span class="block text-xs font-bold text-[#14283f]">Homepage messaging</span><span class="mt-1 block text-[10px] leading-5 text-[#68727b]">Hero, about, and contact copy</span></span><span class="ml-auto text-[#b99a61]">↗</span>
                </a>
            </div>
        </section>
        <section class="rounded-xl bg-[#14283f] p-5 text-white sm:p-7">
            <p class="text-[9px] font-bold uppercase tracking-[.2em] text-[#b99a61]">Before launch</p>
            <h3 class="mt-2 text-xl font-semibold tracking-tight">A quick readiness checklist</h3>
            <ul class="mt-5 space-y-4">
                @foreach([
                    ['Set brand details', 'Confirm site title, favicon, contact email, and service wording.'],
                    ['Connect Firestore', 'Use production credentials stored only in environment secrets.'],
                    ['Review policies', 'Publish privacy information and define enquiry retention.'],
                ] as [$title, $description])
                    <li class="flex items-start gap-3"><span class="mt-0.5 grid h-5 w-5 shrink-0 place-items-center rounded-full border border-[#b99a61]/40 text-[10px] text-[#b99a61]">✓</span><span><span class="block text-xs font-semibold">{{ $title }}</span><span class="mt-1 block text-[10px] leading-5 text-white/50">{{ $description }}</span></span></li>
                @endforeach
            </ul>
            <a href="{{ route('admin.settings') }}" class="mt-7 inline-flex items-center gap-3 rounded-md bg-[#b99a61] px-4 py-3 text-xs font-bold text-[#14283f] no-underline transition hover:bg-[#d3b77f]">Review website settings <span>↗</span></a>
        </section>
    </div>
@endsection
