<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex,nofollow">
    <title>@yield('title', 'Admin') · Bookkeeper Pro</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    @vite('resources/css/app.css')
</head>
<body class="min-h-screen bg-[#f4f2ec] text-[#14283f] antialiased">
    <div class="min-h-screen lg:grid lg:grid-cols-[248px_minmax(0,1fr)]">
        <aside class="hidden min-h-screen flex-col bg-[#14283f] text-white lg:flex">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 border-b border-white/10 px-6 py-6 no-underline">
                <span class="grid h-10 w-10 place-items-center rounded-xl bg-[#b99a61] text-[#14283f]">
                    <svg viewBox="0 0 24 24" class="h-6 w-6" fill="none" stroke="currentColor" stroke-width="1.7"><rect x="4" y="3" width="16" height="18" rx="2"/><path d="M8 7h8M8 11h8M8 15h4M7 3v18"/></svg>
                </span>
                <span><span class="block text-sm font-bold tracking-tight">Bookkeeper Pro</span><span class="mt-1 block text-[9px] uppercase tracking-[.18em] text-white/45">Administration</span></span>
            </a>
            <div class="px-4 pt-7">
                <p class="px-3 pb-3 text-[9px] font-bold uppercase tracking-[.2em] text-white/35">Workspace</p>
                <nav aria-label="Admin navigation" class="space-y-1">
                    <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.dashboard') ? 'bg-white/10 text-white' : 'text-white/65 hover:bg-white/5 hover:text-white' }} flex items-center gap-3 rounded-lg px-3 py-3 text-xs font-semibold no-underline transition">
                        <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.7"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/></svg>
                        Dashboard
                    </a>
                    <a href="{{ route('admin.settings') }}" class="{{ request()->routeIs('admin.settings*') ? 'bg-white/10 text-white' : 'text-white/65 hover:bg-white/5 hover:text-white' }} flex items-center gap-3 rounded-lg px-3 py-3 text-xs font-semibold no-underline transition">
                        <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.7"><circle cx="12" cy="12" r="3"/><path d="m19.4 15 .1.1 1.4 1.1-1.4 2.4-1.7-.7a8 8 0 0 1-1.6.9l-.3 1.8h-2.8l-.3-1.8a8 8 0 0 1-1.6-.9l-1.7.7-1.4-2.4 1.4-1.1a8 8 0 0 1 0-1.9l-1.4-1.1 1.4-2.4 1.7.7a8 8 0 0 1 1.6-.9l.3-1.8h2.8l.3 1.8a8 8 0 0 1 1.6.9l1.7-.7 1.4 2.4-1.4 1.1a8 8 0 0 1 0 1.9Z"/></svg>
                        Website settings
                    </a>
                </nav>
                <p class="px-3 pb-3 pt-8 text-[9px] font-bold uppercase tracking-[.2em] text-white/35">Website</p>
                <a href="{{ route('home') }}" target="_blank" rel="noopener noreferrer" class="flex items-center gap-3 rounded-lg px-3 py-3 text-xs font-semibold text-white/65 no-underline transition hover:bg-white/5 hover:text-white">
                    <svg viewBox="0 0 24 24" class="h-[18px] w-[18px]" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M14 3h7v7M10 14 21 3"/><path d="M19 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h6"/></svg>
                    View live website <span class="ml-auto text-[#b99a61]">↗</span>
                </a>
            </div>
            <div class="mt-auto border-t border-white/10 p-5">
                <div class="rounded-xl border border-white/10 bg-white/[.035] p-4">
                    <p class="text-[10px] font-semibold text-white/80">Keep your website current</p>
                    <p class="mt-2 text-[10px] leading-5 text-white/45">Changes to public brand settings are saved to Firestore.</p>
                </div>
                <form method="POST" action="{{ route('admin.logout') }}" class="mt-4">
                    @csrf
                    <button type="submit" class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-left text-xs font-semibold text-white/55 transition hover:bg-white/5 hover:text-white">
                        <svg viewBox="0 0 24 24" class="h-[17px] w-[17px]" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M10 17l5-5-5-5M15 12H3"/><path d="M12 3h6a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-6"/></svg>
                        Sign out
                    </button>
                </form>
            </div>
        </aside>
        <div class="min-w-0">
            <header class="flex min-h-[76px] items-center justify-between border-b border-[#14283f]/10 bg-[#fffdf8] px-5 sm:px-8 lg:px-10">
                <div class="flex items-center gap-3 lg:hidden">
                    <span class="grid h-9 w-9 place-items-center rounded-lg bg-[#14283f] text-[#b99a61]">
                        <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7"><rect x="4" y="3" width="16" height="18" rx="2"/><path d="M8 7h8M8 11h8M8 15h4M7 3v18"/></svg>
                    </span>
                    <span class="text-sm font-bold">Bookkeeper Pro</span>
                </div>
                <div class="hidden lg:block">
                    <p class="text-[9px] font-bold uppercase tracking-[.2em] text-[#68727b]">Administration / @yield('eyebrow', 'Workspace')</p>
                    <h1 class="mt-1 text-lg font-semibold tracking-tight">@yield('page-title', 'Dashboard')</h1>
                </div>
                <div class="ml-auto flex items-center gap-3 sm:gap-4">
                    <a href="{{ route('admin.dashboard') }}" class="text-[10px] font-semibold text-[#68727b] no-underline hover:text-[#14283f] lg:hidden">Dashboard</a>
                    <a href="{{ route('admin.settings') }}" class="text-[10px] font-semibold text-[#68727b] no-underline hover:text-[#14283f] lg:hidden">Settings</a>
                    <a href="{{ route('home') }}" target="_blank" rel="noopener noreferrer" class="text-[10px] font-semibold text-[#68727b] no-underline hover:text-[#14283f] lg:hidden">View site ↗</a>
                    <form method="POST" action="{{ route('admin.logout') }}" class="lg:hidden">
                        @csrf
                        <button type="submit" class="text-[10px] font-semibold text-[#68727b] hover:text-[#14283f]">Sign out</button>
                    </form>
                    <span class="hidden text-right sm:block"><span class="block text-xs font-semibold">{{ auth()->user()->name }}</span><span class="mt-1 block text-[10px] text-[#68727b]">Administrator</span></span>
                    <span class="grid h-10 w-10 place-items-center rounded-full bg-[#e9dfca] text-xs font-bold text-[#14283f]">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                </div>
            </header>
            <main class="px-5 py-7 sm:px-8 sm:py-9 lg:px-10 lg:py-10">
                @if(session('success'))
                    <div role="status" class="mb-6 flex items-start gap-3 rounded-xl border border-emerald-900/15 bg-emerald-50 px-4 py-3.5 text-sm text-emerald-900">
                        <span class="font-bold">✓</span><span>{{ session('success') }}</span>
                    </div>
                @endif
                @if(session('error'))
                    <div role="alert" class="mb-6 flex items-start gap-3 rounded-xl border border-rose-900/15 bg-rose-50 px-4 py-3.5 text-sm text-rose-900">
                        <span class="font-bold">!</span><span>{{ session('error') }}</span>
                    </div>
                @endif
                @if($errors->any())
                    <div role="alert" class="mb-6 rounded-xl border border-rose-900/15 bg-rose-50 px-4 py-3.5 text-sm text-rose-900">
                        <p class="font-semibold">Please review the following fields:</p>
                        <ul class="mt-2 list-inside list-disc space-y-1">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                    </div>
                @endif
                @yield('content')
            </main>
            <footer class="flex flex-wrap items-center justify-between gap-2 border-t border-[#14283f]/10 px-5 py-5 text-[10px] text-[#68727b] sm:px-8 lg:px-10">
                <span>Bookkeeper Pro Portfolio · Admin workspace</span>
                <span>Keep credentials and client records private.</span>
            </footer>
        </div>
    </div>
</body>
</html>
