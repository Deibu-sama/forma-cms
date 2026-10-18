@extends('layouts.app')

@section('content')
<main id="main">
    <section class="site-primary text-white">
        <div class="mx-auto max-w-6xl px-6 py-20 md:py-28">
            <p class="mb-4 text-sm font-bold uppercase tracking-[.18em] text-[var(--brand-accent)]">{{ $settings['eyebrow'] ?? 'A flexible website foundation' }}</p>
            <h1 class="max-w-4xl text-4xl font-black leading-tight tracking-tight md:text-6xl">{{ $settings['headline'] ?? 'Build a portfolio that feels like you.' }}</h1>
            <p class="mt-6 max-w-2xl text-lg leading-8 text-slate-200">{{ $settings['description'] ?? 'A customizable portfolio starter for independent professionals and small businesses.' }}</p>
            <div class="mt-8 flex flex-wrap gap-3">
                <a href="#portfolio" class="site-accent site-radius inline-flex px-5 py-3 font-bold text-[var(--brand-primary)]">Explore the portfolio</a>
                <a href="#themes" class="site-radius inline-flex border border-slate-500 px-5 py-3 font-bold text-white">Explore themes</a>
            </div>
        </div>
    </section>

    <section id="features" class="mx-auto max-w-6xl px-6 py-16 md:py-20">
        <p class="text-sm font-bold uppercase tracking-[.18em] text-[var(--brand-accent)]">Built to adapt</p>
        <h2 class="mt-2 text-3xl font-black tracking-tight md:text-4xl">Everything you need to make it yours.</h2>
        <div class="mt-8 grid gap-5 md:grid-cols-3">
            @foreach([
                ['01', 'Theme presets', 'Switch between design presets and configure brand colors without rewriting page templates.'],
                ['02', 'Editable sections', 'Organize hero areas, services, project galleries, FAQs, and calls to action into reusable sections.'],
                ['03', 'Portfolio by URL', 'Use externally hosted images with captions and alternative text.']
            ] as $feature)
                <article class="site-surface site-radius site-border border p-6 shadow-sm">
                    <span class="site-accent mb-5 inline-flex h-10 w-10 items-center justify-center rounded-xl font-black text-[var(--brand-primary)]">{{ $feature[0] }}</span>
                    <h3 class="text-xl font-bold">{{ $feature[1] }}</h3>
                    <p class="mt-2 leading-7 site-muted">{{ $feature[2] }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section id="themes" class="border-y site-border site-surface">
        <div class="mx-auto grid max-w-6xl gap-8 px-6 py-16 md:grid-cols-2 md:items-center">
            <div>
                <p class="text-sm font-bold uppercase tracking-[.18em] text-[var(--brand-accent)]">Your visual identity</p>
                <h2 class="mt-2 text-3xl font-black tracking-tight md:text-4xl">One foundation. Multiple looks.</h2>
                <p class="mt-4 max-w-xl leading-7 site-muted">Choose a built-in theme in the future admin dashboard. The public site uses validated CSS design tokens and shared components.</p>
            </div>
            <div class="site-primary site-radius p-7 text-white">
                <label for="theme-picker" class="block text-sm font-semibold">Preview theme</label>
                <select id="theme-picker" class="mt-2 w-full rounded-lg border border-slate-300 bg-white px-3 py-3 text-slate-900">
                    <option value="classic-corporate">Classic Corporate</option>
                    <option value="modern-minimal">Modern Minimal</option>
                    <option value="premium-dark">Premium Dark</option>
                </select>
                <p class="mt-3 text-sm text-slate-200">Prototype preview only. Saving themes to Firestore requires the admin CMS milestone.</p>
            </div>
        </div>
    </section>

    <section id="portfolio" class="mx-auto max-w-6xl px-6 py-16 md:py-20">
        <p class="text-sm font-bold uppercase tracking-[.18em] text-[var(--brand-accent)]">Selected work</p>
        <h2 class="mt-2 text-3xl font-black tracking-tight md:text-4xl">Show the work that matters.</h2>
        <div class="mt-8 grid gap-5 md:grid-cols-3">
            @forelse($projects as $project)
                <article class="site-surface site-radius site-border overflow-hidden border shadow-sm">
                    @if(!empty($project['image_url']))
                        <img src="{{ $project['image_url'] }}" alt="{{ $project['image_alt'] ?? $project['title'] ?? 'Portfolio image' }}" loading="lazy" referrerpolicy="no-referrer" class="h-44 w-full object-cover">
                    @else
                        <div aria-hidden="true" class="site-primary flex h-44 items-center justify-center text-4xl font-black text-[var(--brand-accent)]">F.</div>
                    @endif
                    <div class="p-6">
                        <span class="site-accent inline-flex rounded-full px-3 py-1 text-xs font-bold text-[var(--brand-primary)]">{{ $project['category'] ?? 'Portfolio' }}</span>
                        <h3 class="mt-3 text-xl font-bold">{{ $project['title'] ?? 'Untitled project' }}</h3>
                        <p class="mt-2 leading-7 site-muted">{{ $project['summary'] ?? '' }}</p>
                    </div>
                </article>
            @empty
                <p class="site-muted">No published projects yet. Add a project in your CMS once the admin editor is configured.</p>
            @endforelse
        </div>
    </section>
</main>
<script>
    document.getElementById('theme-picker')?.addEventListener('change', (event) => {
        document.documentElement.dataset.theme = event.target.value;
    });
</script>
@endsection
