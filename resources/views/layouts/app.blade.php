<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="{{ $theme ?? 'classic-corporate' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{{ $settings['meta_description'] ?? 'A customizable portfolio website.' }}">
    <title>{{ $settings['meta_title'] ?? ($settings['site_name'] ?? config('app.name')) }}</title>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <a href="#main" class="sr-only focus:not-sr-only focus:fixed focus:left-4 focus:top-4 focus:z-50 focus:bg-white focus:p-3">Skip to content</a>
    <header class="site-primary text-white">
        <div class="mx-auto flex max-w-6xl flex-wrap items-center justify-between gap-4 px-6 py-5">
            <a class="text-xl font-black tracking-tight" href="{{ route('home') }}">{{ $settings['site_name'] ?? config('app.name') }}</a>
            <nav aria-label="Main navigation" class="flex flex-wrap gap-5 text-sm text-slate-100">
                <a href="#features" class="hover:underline">Features</a>
                <a href="#portfolio" class="hover:underline">Portfolio</a>
                <a href="#themes" class="hover:underline">Themes</a>
            </nav>
        </div>
    </header>
    @if($isDemo ?? false)
        <div class="border-b border-amber-300 bg-amber-50 px-6 py-2 text-center text-sm text-amber-950">
            Demo content is active. Configure Firestore to load your own site settings and projects.
        </div>
    @endif
    @yield('content')
    <footer class="border-t site-border py-8">
        <div class="mx-auto flex max-w-6xl flex-wrap justify-between gap-3 px-6 text-sm site-muted">
            <span>© {{ date('Y') }} {{ $settings['site_name'] ?? config('app.name') }}</span>
            <span>Powered by FormaCMS</span>
        </div>
    </footer>
</body>
</html>
