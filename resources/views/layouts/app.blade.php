<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="{{ $theme ?? 'classic-corporate' }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{{ $settings['meta_description'] ?? $settings['description'] ?? 'A considered portfolio of selected work, ideas, and collaborations.' }}">
    <meta name="theme-color" content="{{ $settings['custom_colors']['primary'] ?? '#14283F' }}">
    <title>{{ $settings['site_title'] ?? $settings['meta_title'] ?? ($settings['site_name'] ?? config('app.name')) }}</title>
    @if(!empty($settings['favicon_url']))
        <link rel="icon" href="{{ $settings['favicon_url'] }}">
    @endif
    @if (file_exists(public_path('build/manifest.json')))
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    @else
        <link rel="stylesheet" href="{{ asset('css/fallback.css') }}?v=20261022-3">
    @endif
</head>
<body class="forma-body">
    <a href="#main" class="skip-link">Skip to content</a>
    <header class="site-header">
        <div class="container header-inner">
            <a class="brand-name" href="{{ route('home') }}">{{ $settings['site_name'] ?? config('app.name') }}</a>
            <nav aria-label="Main navigation" class="site-nav">
                <a href="#work">Selected work</a>
                <a href="#about">About</a>
                <a href="#contact">Contact</a>
            </nav>
        </div>
    </header>
    @if($isDemo ?? false)
        <div class="demo-notice">Preview mode — sample content is shown. Your published portfolio content will appear here when configured.</div>
    @endif
    @yield('content')
    <footer class="site-footer">
        <div class="container footer-inner">
            <span>© {{ date('Y') }} {{ $settings['site_name'] ?? config('app.name') }}</span>
            <span>{{ $settings['footer_note'] ?? 'Designed with intention.' }} · Powered by FormaCMS</span>
        </div>
    </footer>
</body>
</html>
