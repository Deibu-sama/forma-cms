<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="description" content="{{ $settings['meta_description'] ?? $settings['description'] ?? 'Bookkeeping, reconciliations, and reporting support for a clearer view of your business.' }}">
    <meta name="theme-color" content="{{ $settings['custom_colors']['primary'] ?? '#14283F' }}">
    <meta property="og:title" content="{{ $settings['site_title'] ?? $settings['meta_title'] ?? ($settings['site_name'] ?? 'Bookkeeper Pro Portfolio') }}">
    <meta property="og:description" content="{{ $settings['meta_description'] ?? $settings['description'] ?? 'Bookkeeping, reconciliations, and reporting support for a clearer view of your business.' }}">
    <meta property="og:type" content="website">
    <title>{{ $settings['site_title'] ?? $settings['meta_title'] ?? ($settings['site_name'] ?? 'Bookkeeper Pro Portfolio') }}</title>
    @if(!empty($settings['favicon_url']))
        <link rel="icon" href="{{ $settings['favicon_url'] }}">
    @endif
    @viteReactRefresh
    @vite(['resources/css/app.css', 'resources/js/app.jsx'])
    <script>window.__BOOKKEEPER_DATA__ = {{ Illuminate\Support\Js::from($appData) }};</script>
</head>
<body>
    <div id="app">
        <main class="min-h-screen bg-[#f5f1e8] px-6 py-20 text-[#14283f]">
            <p class="mx-auto max-w-2xl text-sm">Loading Bookkeeper Pro Portfolio…</p>
        </main>
    </div>
</body>
</html>
