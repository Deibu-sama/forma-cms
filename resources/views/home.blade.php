@extends('layouts.app')

@section('content')
<main id="main">
    <section class="hero-section">
        <div class="container hero-inner">
            <p class="eyebrow">{{ $settings['eyebrow'] ?? 'A flexible website foundation' }}</p>
            <h1>{{ $settings['headline'] ?? 'Build a portfolio that feels like you.' }}</h1>
            <p class="hero-description">{{ $settings['description'] ?? 'A customizable portfolio starter for independent professionals and small businesses.' }}</p>
            <div class="hero-actions">
                <a href="#portfolio" class="button button-primary">Explore the portfolio</a>
                <a href="#themes" class="button button-outline">Explore themes</a>
            </div>
        </div>
    </section>

    <section id="features" class="section container">
        <p class="eyebrow eyebrow-accent">Built to adapt</p>
        <h2>Everything you need to make it yours.</h2>
        <div class="feature-grid">
            @foreach([
                ['01', 'Theme presets', 'Switch between design presets and configure brand colors without rewriting page templates.'],
                ['02', 'Editable sections', 'Organize hero areas, services, project galleries, FAQs, and calls to action into reusable sections.'],
                ['03', 'Portfolio by URL', 'Use externally hosted images with captions and alternative text.']
            ] as $feature)
                <article class="feature-card">
                    <span class="feature-number">{{ $feature[0] }}</span>
                    <h3>{{ $feature[1] }}</h3>
                    <p>{{ $feature[2] }}</p>
                </article>
            @endforeach
        </div>
    </section>

    <section id="themes" class="theme-section">
        <div class="container theme-layout">
            <div class="theme-copy">
                <p class="eyebrow eyebrow-accent">Your visual identity</p>
                <h2>One foundation. Multiple looks.</h2>
                <p>Choose a built-in theme in the future admin dashboard. The public site uses validated CSS design tokens and shared components.</p>
            </div>
            <div class="theme-preview">
                <label for="theme-picker">Preview theme</label>
                <select id="theme-picker">
                    <option value="classic-corporate">Classic Corporate</option>
                    <option value="modern-minimal">Modern Minimal</option>
                    <option value="premium-dark">Premium Dark</option>
                </select>
                <p class="theme-note">Prototype preview only. Saving themes to Firestore requires the admin CMS milestone.</p>
            </div>
        </div>
    </section>

    <section id="portfolio" class="section container">
        <p class="eyebrow eyebrow-accent">Selected work</p>
        <h2>Show the work that matters.</h2>
        <div class="portfolio-grid">
            @forelse($projects as $project)
                <article class="portfolio-card">
                    @if(!empty($project['image_url']))
                        <img src="{{ $project['image_url'] }}" alt="{{ $project['image_alt'] ?? $project['title'] ?? 'Portfolio image' }}" loading="lazy" referrerpolicy="no-referrer">
                    @else
                        <div aria-hidden="true" class="portfolio-placeholder">F.</div>
                    @endif
                    <div class="portfolio-card-content">
                        <span class="project-category">{{ $project['category'] ?? 'Portfolio' }}</span>
                        <h3>{{ $project['title'] ?? 'Untitled project' }}</h3>
                        <p>{{ $project['summary'] ?? '' }}</p>
                    </div>
                </article>
            @empty
                <p class="muted-text">No published projects yet. Add a project in your CMS once the admin editor is configured.</p>
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
