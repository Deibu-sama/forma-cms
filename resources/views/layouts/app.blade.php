<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="{{ $theme ?? 'classic-corporate' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{{ $settings['meta_description'] ?? 'A customizable portfolio website.' }}">
    <title>{{ $settings['meta_title'] ?? ($settings['site_name'] ?? config('app.name')) }}</title>
    @if (file_exists(public_path('build/manifest.json')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <link rel="stylesheet" href="{{ asset('css/fallback.css') }}?v=20261022-2">
    @endif
</head>
<body class="forma-body">
    <a href="#main" class="skip-link">Skip to content</a>
    <header class="site-header">
        <div class="container header-inner">
            <a class="brand-name" href="{{ route('home') }}">{{ $settings['site_name'] ?? config('app.name') }}</a>
            <nav aria-label="Main navigation" class="site-nav">
                <a href="#features">Features</a>
                <a href="#portfolio">Portfolio</a>
                <a href="#themes">Themes</a>
            </nav>
        </div>
    </header>
    @if($isDemo ?? false)
        <div class="demo-notice">Demo content is active. Configure Firestore to load your own site settings and projects.</div>
    @endif
    @yield('content')
    <footer class="site-footer">
        <div class="container footer-inner">
            <span>© {{ date('Y') }} {{ $settings['site_name'] ?? config('app.name') }}</span>
            <span>Powered by FormaCMS</span>
        </div>
    </footer>
</body>
</html>
