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
    @php
        $portfolioSections = [
            'home' => 'Home',
            'about' => 'About',
            'services' => 'Services',
            'tax-compliance' => 'Tax compliance support',
            'experience' => 'Professional experience',
            'skills' => 'Software & technical skills',
            'how-i-work' => 'How I work',
            'packages' => 'Engagement packages',
            'why-work-with-me' => 'Why work with me',
        ];
        $isPortfolioPage = request()->routeIs('admin.content.*');
        $isContentOpen = $isPortfolioPage || request()->routeIs('admin.faqs*') || request()->routeIs('admin.projects*') || request()->routeIs('admin.certificates*');
    @endphp
    <div class="min-h-screen bg-[#f4f2ec] lg:grid lg:grid-cols-[256px_minmax(0,1fr)]">
        <aside class="hidden lg:sticky lg:top-0 lg:flex lg:h-screen lg:flex-col lg:overflow-y-auto bg-[#14283f] text-white">
            <a href="{{ route('admin.dashboard') }}" class="flex items-center gap-3 border-b border-white/10 px-5 py-5 no-underline">
                <span class="grid h-10 w-10 shrink-0 place-items-center rounded-xl bg-[#b99a61] text-[#14283f]">
                    <svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7"><rect x="4" y="3" width="16" height="18" rx="2"/><path d="M8 7h8M8 11h8M8 15h4"/></svg>
                </span>
                <span><span class="block text-sm font-bold tracking-tight">Portfolio Studio</span><span class="mt-1 block text-[9px] uppercase tracking-[.18em] text-white/45">Admin workspace</span></span>
            </a>
            <nav aria-label="Admin navigation" class="flex-1 space-y-1 px-3 py-5">
                <p class="px-3 pb-2 text-[9px] font-bold uppercase tracking-[.18em] text-white/40">Overview</p>
                <a href="{{ route('admin.dashboard') }}" @class(['admin-nav-link', 'admin-nav-link-active' => request()->routeIs('admin.dashboard')])>
                    <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/></svg>Dashboard
                </a>
                <p class="px-3 pb-2 pt-6 text-[9px] font-bold uppercase tracking-[.18em] text-white/40">Manage content</p>
                <a href="{{ route('admin.projects') }}" @class(['admin-nav-link', 'admin-nav-link-active' => request()->routeIs('admin.projects*')])>
                    <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="4" width="18" height="16" rx="2"/><path d="M3 9h18M9 20V9"/></svg>Portfolio projects
                </a>
                <a href="{{ route('admin.faqs') }}" @class(['admin-nav-link', 'admin-nav-link-active' => request()->routeIs('admin.faqs*')])>
                    <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="9"/><path d="M9.6 9a2.5 2.5 0 1 1 4.4 1.6c-1.2 1.1-2 1.3-2 3M12 17h.01"/></svg>Frequently asked questions
                </a>
                <a href="{{ route('admin.certificates') }}" @class(['admin-nav-link', 'admin-nav-link-active' => request()->routeIs('admin.certificates*')])>
                    <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="8" r="5"/><path d="m8 12-1 9 5-3 5 3-1-9"/></svg>Certificates
                </a>
                <details class="admin-nav-group mt-2" {{ $isPortfolioPage ? 'open' : '' }}>
                    <summary class="admin-nav-summary"><span class="flex items-center gap-3"><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 5h16v14H4zM8 9h8M8 13h8M8 17h5"/></svg>Portfolio sections</span><svg class="admin-chevron" viewBox="0 0 24 24" aria-hidden="true"><path d="m7 10 5 5 5-5"/></svg></summary>
                    <div class="mt-1 space-y-1 border-l border-white/10 ml-5 pl-3">
                        @foreach ($portfolioSections as $sectionKey => $sectionName)
                            <a href="{{ route('admin.content.edit', $sectionKey) }}" @class(['admin-nav-sub-link', 'admin-nav-sub-link-active' => request()->route('section') === $sectionKey])>{{ $sectionName }}</a>
                        @endforeach
                    </div>
                </details>
                <p class="px-3 pb-2 pt-6 text-[9px] font-bold uppercase tracking-[.18em] text-white/40">Configuration</p>
                <a href="{{ route('admin.settings') }}" @class(['admin-nav-link', 'admin-nav-link-active' => request()->routeIs('admin.settings*')])>
                    <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="3"/><path d="m19 13 2 1-2 3-2-1-2 1v2h-4v-2l-2-1-2 1-2-3 2-1v-2l-2-1 2-3 2 1 2-1V5h4v2l2 1 2-1 2 3-2 1z"/></svg>Website settings
                </a>
            </nav>
            <div class="border-t border-white/10 p-4">
                <a href="{{ route('home') }}" target="_blank" rel="noopener noreferrer" class="admin-nav-link mb-2">
                    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M14 3h7v7M10 14 21 3"/><path d="M19 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V7a2 2 0 0 1 2-2h6"/></svg>View live website <span class="ml-auto">↗</span>
                </a>
                <div class="flex items-center gap-3 rounded-xl bg-white/[.06] p-3">
                    <span class="grid h-9 w-9 shrink-0 place-items-center rounded-full bg-[#b99a61] text-xs font-bold text-[#14283f]">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                    <span class="min-w-0 flex-1"><span class="block truncate text-xs font-semibold">{{ auth()->user()->name }}</span><span class="mt-1 block text-[10px] text-white/45">Administrator</span></span>
                    <form method="POST" action="{{ route('admin.logout') }}">@csrf<button type="submit" class="rounded-md p-2 text-white/55 transition hover:bg-white/10 hover:text-white" aria-label="Sign out" title="Sign out"><svg viewBox="0 0 24 24" class="h-4 w-4" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M10 17l5-5-5-5M15 12H3"/><path d="M12 3h6a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2h-6"/></svg></button></form>
                </div>
            </div>
        </aside>

        <div class="min-w-0">
            <header class="sticky top-0 z-30 border-b border-[#14283f]/10 bg-[#fffdf8]/95 backdrop-blur">
                <div class="flex min-h-[72px] items-center justify-between gap-4 px-4 sm:px-7 lg:px-9">
                    <div class="flex min-w-0 items-center gap-3">
                        <span class="grid h-9 w-9 shrink-0 place-items-center rounded-lg bg-[#14283f] text-[#b99a61] lg:hidden"><svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7"><rect x="4" y="3" width="16" height="18" rx="2"/><path d="M8 7h8M8 11h8M8 15h4"/></svg></span>
                        <div class="min-w-0">
                            <p class="hidden text-[9px] font-bold uppercase tracking-[.18em] text-[#68727b] sm:block">Portfolio Studio / @yield('eyebrow', 'Workspace')</p>
                            <h1 class="truncate text-base font-semibold tracking-tight sm:mt-1 sm:text-lg">@yield('page-title', 'Dashboard')</h1>
                        </div>
                    </div>
                    <div class="flex shrink-0 items-center gap-2 sm:gap-3">
                        <a href="{{ route('home') }}" target="_blank" rel="noopener noreferrer" class="hidden rounded-lg border border-[#14283f]/10 px-3 py-2 text-[11px] font-semibold text-[#14283f] no-underline transition hover:border-[#b99a61] sm:inline-flex">Preview site ↗</a>
                        <details class="admin-mobile-menu relative lg:hidden">
                            <summary class="grid h-10 w-10 cursor-pointer list-none place-items-center rounded-lg border border-[#14283f]/15 bg-white text-[#14283f] transition hover:bg-[#f4f2ec]" aria-label="Open admin navigation"><svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.8"><path d="M4 7h16M4 12h16M4 17h16"/></svg></summary>
                            <div class="absolute right-0 top-12 z-50 max-h-[min(75vh,620px)] w-[min(88vw,340px)] overflow-y-auto rounded-2xl border border-[#14283f]/10 bg-[#fffdf8] p-3 shadow-2xl">
                                <p class="px-3 pb-2 pt-1 text-[9px] font-bold uppercase tracking-[.18em] text-[#68727b]">Overview</p>
                                <a href="{{ route('admin.dashboard') }}" @class(['admin-mobile-link', 'admin-mobile-link-active' => request()->routeIs('admin.dashboard')])>Dashboard</a>
                                <p class="px-3 pb-2 pt-4 text-[9px] font-bold uppercase tracking-[.18em] text-[#68727b]">Manage content</p>
                                <a href="{{ route('admin.projects') }}" @class(['admin-mobile-link', 'admin-mobile-link-active' => request()->routeIs('admin.projects*')])>Portfolio projects</a>
                                <a href="{{ route('admin.faqs') }}" @class(['admin-mobile-link', 'admin-mobile-link-active' => request()->routeIs('admin.faqs*')])>Frequently asked questions</a>
                                <a href="{{ route('admin.certificates') }}" @class(['admin-mobile-link', 'admin-mobile-link-active' => request()->routeIs('admin.certificates*')])>Certificates</a>
                                <details class="mt-1 rounded-lg" {{ $isPortfolioPage ? 'open' : '' }}>
                                    <summary class="admin-mobile-link cursor-pointer">Portfolio sections <span class="ml-auto text-[#68727b]">⌄</span></summary>
                                    <div class="ml-3 mt-1 border-l border-[#14283f]/10 pl-2">
                                        @foreach ($portfolioSections as $sectionKey => $sectionName)
                                            <a href="{{ route('admin.content.edit', $sectionKey) }}" @class(['admin-mobile-link', 'admin-mobile-link-active' => request()->route('section') === $sectionKey])>{{ $sectionName }}</a>
                                        @endforeach
                                    </div>
                                </details>
                                <p class="px-3 pb-2 pt-4 text-[9px] font-bold uppercase tracking-[.18em] text-[#68727b]">Configuration</p>
                                <a href="{{ route('admin.settings') }}" @class(['admin-mobile-link', 'admin-mobile-link-active' => request()->routeIs('admin.settings*')])>Website settings</a>
                                <div class="mt-3 border-t border-[#14283f]/10 pt-3"><a href="{{ route('home') }}" target="_blank" rel="noopener noreferrer" class="admin-mobile-link">View live website ↗</a>
                                    <form method="POST" action="{{ route('admin.logout') }}">@csrf<button type="submit" class="admin-mobile-link w-full text-left">Sign out</button></form>
                                </div>
                            </div>
                        </details>
                        <span class="hidden text-right md:block"><span class="block text-xs font-semibold">{{ auth()->user()->name }}</span><span class="mt-1 block text-[10px] text-[#68727b]">Administrator</span></span>
                        <span class="hidden h-9 w-9 shrink-0 place-items-center rounded-full bg-[#e9dfca] text-xs font-bold text-[#14283f] md:grid">{{ strtoupper(substr(auth()->user()->name, 0, 1)) }}</span>
                    </div>
                </div>
            </header>
            <main class="mx-auto w-full max-w-[1600px] px-4 py-6 sm:px-7 sm:py-8 lg:px-9 lg:py-9">
                @if(session('success'))
                    <div role="status" class="mb-6 flex items-start gap-3 rounded-xl border border-emerald-900/15 bg-emerald-50 px-4 py-3.5 text-sm text-emerald-900"><span class="font-bold">✓</span><span>{{ session('success') }}</span></div>
                @endif
                @if(session('error'))
                    <div role="alert" class="mb-6 flex items-start gap-3 rounded-xl border border-rose-900/15 bg-rose-50 px-4 py-3.5 text-sm text-rose-900"><span class="font-bold">!</span><span>{{ session('error') }}</span></div>
                @endif
                @if($errors->any())
                    <div role="alert" class="mb-6 rounded-xl border border-rose-900/15 bg-rose-50 px-4 py-3.5 text-sm text-rose-900"><p class="font-semibold">Please review the following fields:</p><ul class="mt-2 list-inside list-disc space-y-1">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
                @endif
                @yield('content')
            </main>
            <footer class="flex flex-wrap items-center justify-between gap-2 border-t border-[#14283f]/10 px-4 py-5 text-[10px] text-[#68727b] sm:px-7 lg:px-9">
                <span>Portfolio Studio · Admin workspace</span><span>Keep credentials and client records private.</span>
            </footer>
        </div>
    </div>
</body>
</html>
