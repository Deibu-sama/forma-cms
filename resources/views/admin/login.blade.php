<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex,nofollow">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Admin sign in · Bookkeeper Pro</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    @vite('resources/css/app.css')
</head>
<body class="min-h-screen bg-[#f5f1e8] text-[#14283f] antialiased">
    <main class="grid min-h-screen lg:grid-cols-[1.08fr_.92fr]">
        <section class="hero-pattern relative hidden overflow-hidden px-12 py-14 text-white lg:flex lg:flex-col lg:justify-between xl:px-20 xl:py-16">
            <div class="absolute -right-20 top-1/4 h-96 w-96 rounded-full border border-brand-accent/20"></div>
            <div class="absolute -right-8 top-[31%] h-72 w-72 rounded-full border border-white/10"></div>
            <a href="{{ route('home') }}" class="relative z-10 inline-flex w-fit items-center gap-3 text-white no-underline">
                <span class="grid h-11 w-11 place-items-center rounded-xl bg-[#b99a61] text-[#14283f]">
                    <svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.7"><rect x="4" y="3" width="16" height="18" rx="2"/><path d="M8 7h8M8 11h8M8 15h4M7 3v18"/></svg>
                </span>
                <span><span class="block text-sm font-bold tracking-tight">Bookkeeper Pro</span><span class="mt-1 block text-[9px] uppercase tracking-[.18em] text-white/45">Portfolio administration</span></span>
            </a>
            <div class="relative z-10 max-w-xl pb-10">
                <p class="mb-5 flex items-center gap-3 text-[10px] font-bold uppercase tracking-[.22em] text-[#b99a61]"><span class="h-px w-8 bg-[#b99a61]"></span> A clearer way to manage your site</p>
                <h1 class="text-5xl font-semibold leading-[.98] tracking-[-.065em] xl:text-6xl">Your work.<br><span class="text-[#b99a61]">Your settings.</span><br>All in one place.</h1>
                <p class="mt-6 max-w-md text-sm leading-7 text-white/60">Manage your public website identity and keep your bookkeeping portfolio current, without editing templates or code.</p>
                <div class="mt-9 flex flex-wrap gap-3 text-[10px] text-white/65">
                    <span class="rounded-full border border-white/15 px-3 py-2">Private admin access</span><span class="rounded-full border border-white/15 px-3 py-2">Firestore-backed settings</span>
                </div>
            </div>
            <p class="relative z-10 text-[10px] text-white/35">Authorized administrators only · Never share your login.</p>
        </section>
        <section class="flex items-center justify-center px-5 py-12 sm:px-10">
            <div class="w-full max-w-[420px]">
                <a href="{{ route('home') }}" class="mb-12 inline-flex items-center gap-2 text-xs font-semibold text-[#68727b] no-underline hover:text-[#14283f] lg:hidden">← Back to website</a>
                <div class="mb-8">
                    <div class="mb-7 grid h-12 w-12 place-items-center rounded-xl bg-[#14283f] text-[#b99a61] lg:hidden">
                        <svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.7"><rect x="4" y="3" width="16" height="18" rx="2"/><path d="M8 7h8M8 11h8M8 15h4M7 3v18"/></svg>
                    </div>
                    <p class="mb-3 text-[10px] font-bold uppercase tracking-[.2em] text-[#a18755]">Secure access</p>
                    <h2 class="text-3xl font-semibold tracking-[-.055em] sm:text-4xl">Welcome back.</h2>
                    <p class="mt-3 text-sm leading-6 text-[#68727b]">Sign in to manage your website settings and content.</p>
                </div>
                @if(session('status'))
                    <div role="status" class="mb-5 rounded-lg border border-emerald-900/15 bg-emerald-50 px-4 py-3 text-sm text-emerald-900">{{ session('status') }}</div>
                @endif
                @if($errors->any())
                    <div role="alert" class="mb-5 rounded-lg border border-rose-900/15 bg-rose-50 px-4 py-3 text-sm text-rose-900">We couldn’t sign you in with those credentials. Check your email and password and try again.</div>
                @endif
                <form method="POST" action="{{ route('admin.login.store') }}" class="space-y-5">
                    @csrf
                    <div>
                        <label for="email" class="mb-2 block text-xs font-semibold">Administrator email</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="username" autofocus maxlength="254" class="form-field bg-[#fffdf8] text-sm" placeholder="you@yourbusiness.com">
                    </div>
                    <div>
                        <div class="mb-2 flex items-center justify-between gap-3"><label for="password" class="block text-xs font-semibold">Password</label><span class="text-[10px] text-[#68727b]">Keep it private</span></div>
                        <input id="password" name="password" type="password" required autocomplete="current-password" class="form-field bg-[#fffdf8] text-sm" placeholder="Your password">
                    </div>
                    <label class="flex items-center gap-2.5 text-xs text-[#68727b]"><input type="checkbox" name="remember" value="1" class="h-4 w-4 accent-[#14283f]"> Keep me signed in on this device</label>
                    <button type="submit" class="button-lift flex min-h-12 w-full items-center justify-center gap-3 rounded-md bg-[#14283f] px-5 py-3 text-sm font-bold text-white hover:bg-[#253f5a]">Sign in to admin <span aria-hidden="true">↗</span></button>
                </form>
                <div class="mt-7 border-t border-[#14283f]/10 pt-5 text-[10px] leading-5 text-[#68727b]">
                    No public sign-up is available. The first administrator must be created through the server’s Artisan command.
                </div>
                <a href="{{ route('home') }}" class="mt-8 inline-flex text-xs font-semibold text-[#14283f] no-underline hover:text-[#a18755]">← Return to public portfolio</a>
            </div>
        </section>
    </main>
</body>
</html>
