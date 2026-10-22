<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="index,follow">
    <title>Privacy Notice · {{ config('app.name', 'Bookkeeper Pro Portfolio') }}</title>
    <meta name="description" content="Learn how this portfolio website handles contact-form enquiries and personal information.">
    @vite('resources/css/app.css')
</head>
<body class="min-h-screen bg-[#f5f1e8] text-[#14283f] antialiased">
    <main class="mx-auto max-w-3xl px-5 py-12 sm:px-8 sm:py-20">
        <a href="{{ route('home') }}" class="text-sm font-semibold text-[#80683f] underline underline-offset-4">← Back to website</a>
        <p class="mt-10 text-[10px] font-bold uppercase tracking-[.2em] text-[#a18755]">Privacy</p>
        <h1 class="mt-3 text-4xl font-semibold tracking-tight">Privacy notice</h1>
        <p class="mt-4 text-sm leading-6 text-[#68727b]">Last updated: {{ now()->format('F Y') }}</p>
        <div class="mt-8 space-y-7 text-sm leading-7">
            <section><h2 class="text-lg font-semibold">Information you submit</h2><p class="mt-2">If you use the contact form, the site may collect your name, email address, business name, country, requested service, accounting software, approximate monthly transaction volume, and message. Please do not submit passwords, bank credentials, tax identification numbers, account statements, or other sensitive financial records through this form.</p></section>
            <section><h2 class="text-lg font-semibold">How information is used</h2><p class="mt-2">Information is used to respond to your enquiry, understand the support you are requesting, and follow up about the conversation. Enquiries are stored on the server-side data service configured for this website. If email notifications are configured, the site may also send a notification to the portfolio contact address.</p></section>
            <section><h2 class="text-lg font-semibold">Access and security</h2><p class="mt-2">Enquiry records are intended for authorized administrators only. The website uses server-side validation and access controls, but no internet transmission or storage system can be guaranteed completely secure. Share only information needed to start a conversation.</p></section>
            <section><h2 class="text-lg font-semibold">Retention and requests</h2><p class="mt-2">Enquiries should be retained only as long as needed for the conversation and legitimate business or legal obligations. To ask about access, correction, or deletion of information you submitted, contact the portfolio owner using the contact details on the website.</p></section>
            <section><h2 class="text-lg font-semibold">Third-party services</h2><p class="mt-2">The site may use hosting, database, or email providers to operate the service. Publicly linked images may be loaded directly from their host. Those providers may process technical data under their own terms and privacy notices.</p></section>
            <p class="border-t border-[#14283f]/10 pt-5 text-xs leading-5 text-[#68727b]">This notice is a starter template and should be reviewed and customized by the site owner to accurately describe actual providers, retention periods, contact details, and applicable legal requirements before production use.</p>
        </div>
    </main>
</body>
</html>
