@extends('layouts.app')

@section('content')
<main id="main">
    <section class="portfolio-hero">
        <div class="container portfolio-hero-grid">
            <div class="portfolio-hero-copy">
                <p class="eyebrow">{{ $settings['eyebrow'] ?? 'Independent creative professional' }}</p>
                <h1>{!! nl2br(e($settings['headline'] ?? "Thoughtful work.\nMade with purpose.")) !!}</h1>
                <p class="hero-description">{{ $settings['description'] ?? 'I turn ideas into meaningful experiences through considered design, clear strategy, and a little curiosity.' }}</p>
                <div class="hero-actions">
                    <a href="#work" class="button button-primary">Explore my work <span aria-hidden="true">↗</span></a>
                    <a href="#contact" class="text-link">Let’s talk <span aria-hidden="true">↗</span></a>
                </div>
                <div class="hero-meta">
                    <span class="availability-dot" aria-hidden="true"></span>
                    <span>{{ $settings['availability'] ?? 'Available for select projects' }}</span>
                </div>
            </div>
            <div class="hero-art" aria-label="Portfolio introduction artwork">
                @if(!empty($settings['hero_image_url']))
                    <img class="hero-image" src="{{ $settings['hero_image_url'] }}" alt="{{ $settings['hero_image_alt'] ?? 'Featured portfolio portrait or work' }}" fetchpriority="high">
                @else
                    <div class="hero-art-backdrop"></div>
                    <div class="hero-art-shape hero-art-shape-one"></div>
                    <div class="hero-art-shape hero-art-shape-two"></div>
                    <div class="hero-art-note"><span>PORTFOLIO / 2026</span><strong>Ideas into<br>impact.</strong><span class="hero-art-mark" aria-hidden="true">✳</span></div>
                    <span class="hero-art-caption">A collection of selected work and ideas</span>
                @endif
            </div>
        </div>
        <div class="container hero-bottom-line">
            <span>Scroll to explore</span>
            <a href="#work" aria-label="Scroll to selected work">↓</a>
            <span class="hero-bottom-right">{{ $settings['site_name'] ?? config('app.name') }}</span>
        </div>
    </section>

    <section id="work" class="section work-section">
        <div class="container">
            <div class="section-heading">
                <div>
                    <p class="eyebrow eyebrow-accent">A few favorites</p>
                    <h2>Selected work<span class="heading-period">.</span></h2>
                </div>
                <p class="section-intro">{{ $settings['work_intro'] ?? 'A selection of projects, experiments, and collaborations shaped by curiosity and care.' }}</p>
            </div>
            <div class="portfolio-grid">
                @forelse($projects as $index => $project)
                    <article class="portfolio-card portfolio-card-{{ ($index % 3) + 1 }}">
                        <a class="portfolio-image-link" href="{{ !empty($project['project_url']) ? $project['project_url'] : '#contact' }}" @if(!empty($project['project_url'])) target="_blank" rel="noopener noreferrer" @endif aria-label="View {{ $project['title'] ?? 'portfolio project' }}">
                            @if(!empty($project['image_url']))
                                <img src="{{ $project['image_url'] }}" alt="{{ $project['image_alt'] ?? $project['title'] ?? 'Portfolio project image' }}" loading="lazy" referrerpolicy="no-referrer">
                            @else
                                <div class="project-art project-art-{{ ($index % 3) + 1 }}" aria-hidden="true">
                                    <span class="project-art-label">{{ str_pad((string) ($index + 1), 2, '0', STR_PAD_LEFT) }} / SELECTED WORK</span>
                                    <span class="project-art-title">{{ $project['art_label'] ?? $project['title'] ?? 'Creative project' }}</span>
                                    <span class="project-art-symbol">{{ ['✳', '◒', '↗'][$index % 3] }}</span>
                                </div>
                            @endif
                            <span class="project-open" aria-hidden="true">↗</span>
                        </a>
                        <div class="portfolio-card-content">
                            <div>
                                <h3>{{ $project['title'] ?? 'Untitled project' }}</h3>
                                <p>{{ $project['summary'] ?? '' }}</p>
                            </div>
                            <span class="project-category">{{ $project['category'] ?? 'Selected work' }}</span>
                        </div>
                    </article>
                @empty
                    <div class="empty-work">
                        <span class="empty-work-mark" aria-hidden="true">✳</span>
                        <h3>The next great project goes here.</h3>
                        <p>Published projects will appear here once they’re added to your portfolio.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <section id="about" class="about-section">
        <div class="container about-grid">
            <p class="eyebrow">A little about me</p>
            <div>
                <h2>{{ $settings['about_headline'] ?? 'Good work starts with listening, then making something that matters.' }}</h2>
                <p class="about-description">{{ $settings['about_text'] ?? 'I partner with people and teams to turn ambitious ideas into clear, considered outcomes. My approach is collaborative, thoughtful, and always focused on the people on the other side of the work.' }}</p>
                <a href="#contact" class="text-link text-link-dark">More about working together <span aria-hidden="true">↗</span></a>
            </div>
        </div>
    </section>

    <section id="contact" class="contact-section">
        <div class="container contact-inner">
            <p class="eyebrow">Have something in mind?</p>
            <h2>{{ $settings['contact_headline'] ?? 'Let’s make something meaningful.' }}</h2>
            <p>{{ $settings['contact_text'] ?? 'Have a project, a question, or an idea you want to explore? I’d love to hear about it.' }}</p>
            @if(!empty($settings['contact_email']))
                <a class="button button-light" href="mailto:{{ $settings['contact_email'] }}">Start a conversation <span aria-hidden="true">↗</span></a>
            @else
                <span class="contact-note">Contact details will be added soon.</span>
            @endif
        </div>
    </section>
</main>
@endsection
