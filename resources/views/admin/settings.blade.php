@extends('admin.layout')

@section('title', 'Website Settings')
@section('page-title', 'Website settings')
@section('eyebrow', 'Website / Settings')

@section('content')
    @php
        $colors = $settings['custom_colors'] ?? [];
        $sections = [
            'site_identity' => 'Site identity',
            'homepage-copy' => 'Homepage content',
            'brand-colors' => 'Brand colors',
            'seo' => 'Search & sharing',
        ];
    @endphp

    <div class="mb-8 flex flex-col justify-between gap-5 xl:flex-row xl:items-end">
        <div>
            <p class="mb-2 text-[10px] font-bold uppercase tracking-[.2em] text-[#a18755]">Configuration</p>
            <h2 class="text-3xl font-semibold tracking-[-.055em] sm:text-4xl">Make it yours.</h2>
            <p class="mt-3 max-w-2xl text-sm leading-6 text-[#68727b]">Update your browser title, favicon, homepage messaging, contact details, and brand palette. The public React site reads these values through Laravel.</p>
        </div>
        <a href="{{ route('home') }}" target="_blank" rel="noopener noreferrer" class="inline-flex min-h-10 items-center justify-center gap-2 self-start rounded-md border border-[#14283f]/15 bg-[#fffdf8] px-4 py-2 text-xs font-bold text-[#14283f] no-underline transition hover:border-[#b99a61] xl:self-auto">Preview public website <span class="text-[#b99a61]">↗</span></a>
    </div>

    @unless($firestoreReady)
        <div class="mb-7 flex gap-3 rounded-xl border border-[#b99a61]/40 bg-[#f2e8d2] p-4 sm:p-5">
            <span class="grid h-8 w-8 shrink-0 place-items-center rounded-lg bg-[#b99a61]/25 text-[#14283f]">!</span>
            <div><p class="text-xs font-bold text-[#14283f]">Firestore is not connected — settings can use the local fallback</p><p class="mt-1 text-xs leading-5 text-[#615846]">The public portfolio is showing illustrative work samples. Website settings can be saved to the configured SQL database for now. To store settings remotely and show published Firestore projects, configure <code class="rounded bg-white/70 px-1 py-0.5">FIRESTORE_PROJECT_ID</code> and <code class="rounded bg-white/70 px-1 py-0.5">FIREBASE_CREDENTIALS_BASE64</code> with suitable write permissions.</p></div>
        </div>
    @endunless

    <form method="POST" action="{{ route('admin.settings.update') }}" class="space-y-6">
        @csrf
        @method('PUT')

        <section id="site_identity" class="scroll-mt-24 overflow-hidden rounded-xl border border-[#14283f]/10 bg-[#fffdf8]">
            <div class="border-b border-[#14283f]/10 px-5 py-5 sm:px-7">
                <div class="flex items-center gap-3"><span class="grid h-9 w-9 place-items-center rounded-lg bg-[#14283f] text-[#b99a61]"><svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M4 5h16M4 10h16M4 15h10M4 20h7"/></svg></span><div><h3 class="text-base font-semibold">Site identity</h3><p class="mt-1 text-[11px] text-[#68727b]">Your name and professional title visitors see in the portfolio header.</p></div></div>
            </div>
            <div class="grid gap-5 p-5 sm:grid-cols-2 sm:p-7">
                <div><label for="site_name" class="mb-2 block text-xs font-semibold">Website name <span class="text-rose-700">*</span></label><input id="site_name" name="site_name" value="{{ old('site_name', $settings['site_name'] ?? 'Bookkeeper Pro Portfolio') }}" required maxlength="100" class="form-field text-sm"><p class="mt-1.5 text-[10px] text-[#68727b]">Displayed in the site header and footer.</p></div>
                <div><label for="brand_name" class="mb-2 block text-xs font-semibold">Your name <span class="text-rose-700">*</span></label><input id="brand_name" name="brand_name" value="{{ old('brand_name', $settings['brand_name'] ?? 'Your Name') }}" required maxlength="100" class="form-field text-sm"><p class="mt-1.5 text-[10px] text-[#68727b]">Displayed as text only. This portfolio does not use a company logo.</p></div>
                <div class="sm:col-span-2"><label for="site_title" class="mb-2 block text-xs font-semibold">Browser page title <span class="text-rose-700">*</span></label><input id="site_title" name="site_title" value="{{ old('site_title', $settings['site_title'] ?? 'Bookkeeper Pro Portfolio | Bookkeeping & Reporting Support') }}" required maxlength="180" class="form-field text-sm"><p class="mt-1.5 text-[10px] text-[#68727b]">Appears in the browser tab and most search result titles.</p></div>
                <div class="sm:col-span-2"><label for="tagline" class="mb-2 block text-xs font-semibold">Professional title / tagline</label><input id="tagline" name="tagline" value="{{ old('tagline', $settings['tagline'] ?? 'Bookkeeping Professional') }}" maxlength="160" class="form-field text-sm" placeholder="Bookkeeping Professional"></div>
                <div class="sm:col-span-2"><label for="profile_image_url" class="mb-2 block text-xs font-semibold">Your professional photo URL</label>
                    @if(!empty($settings['profile_image_url']))<img src="{{ $settings['profile_image_url'] }}" alt="Current profile photo preview" class="mb-3 h-36 w-32 rounded-xl border border-[#14283f]/10 object-cover" onerror="this.style.display='none'">@endif
                    <input id="profile_image_url" name="profile_image_url" type="url" value="{{ old('profile_image_url', $settings['profile_image_url'] ?? '') }}" maxlength="2048" inputmode="url" placeholder="https://example.com/your-professional-photo.jpg" class="form-field text-sm">
                    <p class="mt-1.5 text-[10px] leading-5 text-[#68727b]">Paste a direct HTTPS link to your portrait image. The link must be publicly accessible; use a direct image URL, not a private Google Drive page or an album page.</p>
                </div>
                <div class="sm:col-span-2"><label for="favicon_url" class="mb-2 block text-xs font-semibold">Optional browser icon URL</label><input id="favicon_url" name="favicon_url" type="url" value="{{ old('favicon_url', $settings['favicon_url'] ?? '') }}" maxlength="2048" inputmode="url" placeholder="https://example.com/favicon.png" class="form-field text-sm"><p class="mt-1.5 text-[10px] leading-5 text-[#68727b]">Optional only; the portfolio header itself uses no logo.</p></div>
                <div class="sm:col-span-2"><label for="hero_image_url" class="mb-2 block text-xs font-semibold">Hero image URL <span class="font-normal text-[#68727b]">(optional)</span></label><input id="hero_image_url" name="hero_image_url" type="url" value="{{ old('hero_image_url', $settings['hero_image_url'] ?? '') }}" maxlength="2048" placeholder="https://your-domain.com/hero-image.jpg" class="form-field text-sm"><p class="mt-1.5 text-[10px] text-[#68727b]">If provided, the homepage uses this image instead of the CSS dashboard illustration.</p></div>
                <div class="sm:col-span-2"><label for="hero_image_alt" class="mb-2 block text-xs font-semibold">Hero image description (alt text)</label><input id="hero_image_alt" name="hero_image_alt" value="{{ old('hero_image_alt', $settings['hero_image_alt'] ?? '') }}" maxlength="240" class="form-field text-sm" placeholder="Describe the image for people using screen readers"></div>
            </div>
        </section>

        <section id="homepage-copy" class="scroll-mt-24 overflow-hidden rounded-xl border border-[#14283f]/10 bg-[#fffdf8]">
            <div class="border-b border-[#14283f]/10 px-5 py-5 sm:px-7"><div class="flex items-center gap-3"><span class="grid h-9 w-9 place-items-center rounded-lg bg-[#e9dfca] text-[#14283f]"><svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7"><path d="M4 5h16M4 10h10M4 15h16M4 20h7"/></svg></span><div><h3 class="text-base font-semibold">Homepage content</h3><p class="mt-1 text-[11px] text-[#68727b]">Change the text visitors read. Use a new line in the headline to split the hero over two lines.</p></div></div></div>
            <div class="grid gap-5 p-5 sm:grid-cols-2 sm:p-7">
                <div class="sm:col-span-2"><label for="eyebrow" class="mb-2 block text-xs font-semibold">Hero eyebrow <span class="text-rose-700">*</span></label><input id="eyebrow" name="eyebrow" value="{{ old('eyebrow', $settings['eyebrow'] ?? 'BOOKKEEPING · RECONCILIATION · REPORTING') }}" required maxlength="180" class="form-field text-sm"></div>
                <div class="sm:col-span-2"><label for="headline" class="mb-2 block text-xs font-semibold">Main headline <span class="text-rose-700">*</span></label><textarea id="headline" name="headline" rows="2" required maxlength="180" class="form-field resize-y text-sm">{{ old('headline', $settings['headline'] ?? "Clear books.\nConfident decisions.") }}</textarea><p class="mt-1.5 text-[10px] text-[#68727b]">Use a line break to split this heading; the last line gets the accent color.</p></div>
                <div class="sm:col-span-2"><label for="description" class="mb-2 block text-xs font-semibold">Hero description <span class="text-rose-700">*</span></label><textarea id="description" name="description" rows="3" required maxlength="700" class="form-field resize-y text-sm">{{ old('description', $settings['description'] ?? '') }}</textarea></div>
                <div><label for="about_headline" class="mb-2 block text-xs font-semibold">About section headline <span class="text-rose-700">*</span></label><textarea id="about_headline" name="about_headline" rows="3" required maxlength="220" class="form-field resize-y text-sm">{{ old('about_headline', $settings['about_headline'] ?? '') }}</textarea></div>
                <div><label for="about_text" class="mb-2 block text-xs font-semibold">About section text <span class="text-rose-700">*</span></label><textarea id="about_text" name="about_text" rows="3" required maxlength="1200" class="form-field resize-y text-sm">{{ old('about_text', $settings['about_text'] ?? '') }}</textarea></div>
                <div><label for="contact_headline" class="mb-2 block text-xs font-semibold">Contact headline <span class="text-rose-700">*</span></label><textarea id="contact_headline" name="contact_headline" rows="2" required maxlength="220" class="form-field resize-y text-sm">{{ old('contact_headline', $settings['contact_headline'] ?? '') }}</textarea></div>
                <div><label for="contact_text" class="mb-2 block text-xs font-semibold">Contact section text <span class="text-rose-700">*</span></label><textarea id="contact_text" name="contact_text" rows="3" required maxlength="700" class="form-field resize-y text-sm">{{ old('contact_text', $settings['contact_text'] ?? '') }}</textarea></div>
                <div><label for="availability" class="mb-2 block text-xs font-semibold">Availability note</label><input id="availability" name="availability" value="{{ old('availability', $settings['availability'] ?? '') }}" maxlength="100" class="form-field text-sm" placeholder="Available for selected projects"></div>
                <div><label for="work_intro" class="mb-2 block text-xs font-semibold">Portfolio introduction</label><textarea id="work_intro" name="work_intro" rows="2" maxlength="500" class="form-field resize-y text-sm">{{ old('work_intro', $settings['work_intro'] ?? '') }}</textarea></div>
                <div><label for="contact_email" class="mb-2 block text-xs font-semibold">Contact email</label><input id="contact_email" name="contact_email" type="email" value="{{ old('contact_email', $settings['contact_email'] ?? '') }}" maxlength="254" class="form-field text-sm" placeholder="hello@yourdomain.com"><p class="mt-1.5 text-[10px] text-[#68727b]">Public contact address and destination for new enquiry email notifications. Configure SMTP mail settings in Render for delivery.</p></div>
                <div><label for="footer_note" class="mb-2 block text-xs font-semibold">Footer note</label><input id="footer_note" name="footer_note" value="{{ old('footer_note', $settings['footer_note'] ?? '') }}" maxlength="180" class="form-field text-sm" placeholder="Designed with care."></div>
            </div>
        </section>

        <section id="theme-presets" class="scroll-mt-24 overflow-hidden rounded-xl border border-[#14283f]/10 bg-[#fffdf8]">
            <div class="border-b border-[#14283f]/10 px-5 py-5 sm:px-7"><h3 class="text-base font-semibold">Choose a visual theme</h3><p class="mt-1 text-[11px] text-[#68727b]">Choose a preset, then fine-tune its colors below.</p></div>
            <div class="p-5 sm:p-7">
                <label for="theme" class="mb-2 block text-xs font-semibold">Theme preset</label>
                <select id="theme" name="theme" class="form-field text-sm">
                    @foreach(['classic-corporate' => 'Classic navy & ivory', 'sage-editorial' => 'Sage & cream editorial', 'modern-lavender' => 'Modern lavender', 'ocean-professional' => 'Ocean blue', 'warm-minimal' => 'Warm terracotta minimal'] as $value => $label)
                        <option value="{{ $value }}" @selected(old('theme', $settings['theme'] ?? 'classic-corporate') === $value)>{{ $label }}</option>
                    @endforeach
                </select>
                <p class="mt-2 text-[10px] leading-5 text-[#68727b]">Selecting a preset updates the six color controls below automatically.</p>
            </div>
        </section>

        <section id="brand-colors" class="scroll-mt-24 overflow-hidden rounded-xl border border-[#14283f]/10 bg-[#fffdf8]">
            <div class="border-b border-[#14283f]/10 px-5 py-5 sm:px-7"><div class="flex items-center gap-3"><span class="grid h-9 w-9 place-items-center rounded-lg bg-[#e9dfca] text-[#14283f]">◐</span><div><h3 class="text-base font-semibold">Brand colors</h3><p class="mt-1 text-[11px] text-[#68727b]">Pick your six colors; values are stored as validated hex codes.</p></div></div></div>
            <div class="grid gap-4 p-5 sm:grid-cols-2 lg:grid-cols-3 sm:p-7">
                @foreach(['primary' => ['Navy / primary', '#14283F'], 'background' => ['Ivory / page background', '#F5F1E8'], 'accent' => ['Gold / accent', '#B99A61'], 'surface' => ['Card surface', '#FFFDF8'], 'text' => ['Body text', '#192C41'], 'muted' => ['Muted text', '#68727B']] as $key => [$label, $default])
                    <label class="flex items-center gap-3 rounded-lg border border-[#14283f]/10 p-3.5">
                        <input type="color" aria-label="{{ $label }}" name="custom_colors[{{ $key }}]" value="{{ old('custom_colors.'.$key, $colors[$key] ?? $default) }}" class="h-11 w-12 cursor-pointer rounded-md border-0 bg-transparent p-0.5">
                        <span class="min-w-0"><span class="block text-xs font-semibold">{{ $label }}</span><span class="mt-1 block text-[10px] text-[#68727b]">{{ old('custom_colors.'.$key, $colors[$key] ?? $default) }}</span></span>
                    </label>
                @endforeach
            </div>
        </section>

        <section id="seo" class="scroll-mt-24 overflow-hidden rounded-xl border border-[#14283f]/10 bg-[#fffdf8]">
            <div class="border-b border-[#14283f]/10 px-5 py-5 sm:px-7"><div class="flex items-center gap-3"><span class="grid h-9 w-9 place-items-center rounded-lg bg-[#14283f] text-[#b99a61]"><svg viewBox="0 0 24 24" class="h-5 w-5" fill="none" stroke="currentColor" stroke-width="1.7"><circle cx="10.5" cy="10.5" r="6.5"/><path d="m16 16 5 5M8 10h5M10.5 8v5"/></svg></span><div><h3 class="text-base font-semibold">Search & sharing</h3><p class="mt-1 text-[11px] text-[#68727b]">Provide an accurate description for search and social previews.</p></div></div></div>
            <div class="p-5 sm:p-7"><label for="meta_description" class="mb-2 block text-xs font-semibold">Meta description <span class="text-rose-700">*</span></label><textarea id="meta_description" name="meta_description" rows="3" required maxlength="320" class="form-field resize-y text-sm">{{ old('meta_description', $settings['meta_description'] ?? '') }}</textarea><p class="mt-1.5 text-[10px] text-[#68727b]">Keep it clear and factual; do not add unverified credentials, results, or guarantees.</p></div>
        </section>

        <div class="sticky bottom-4 z-20 flex flex-col items-stretch justify-between gap-3 rounded-xl border border-[#14283f]/10 bg-[#fffdf8]/95 p-3 shadow-xl backdrop-blur sm:flex-row sm:items-center sm:px-5">
            <p class="px-1 text-[10px] leading-5 text-[#68727b]">@if($firestoreReady) Saving updates <code>site_settings/main</code> in Firestore. @else Saving updates the server SQL fallback until Firestore is available. @endif Public changes appear after the next page load.</p>
            <div class="flex gap-2">
                <a href="{{ route('admin.dashboard') }}" class="inline-flex min-h-11 items-center justify-center rounded-md border border-[#14283f]/15 px-4 py-2 text-xs font-bold text-[#14283f] no-underline hover:bg-[#f4f0e6]">Cancel</a>
                <button type="submit" class="button-lift inline-flex min-h-11 items-center justify-center gap-3 rounded-md bg-[#14283f] px-5 py-2 text-xs font-bold text-white hover:bg-[#263e56]">Save website settings <span class="text-[#b99a61]">↗</span></button>
            </div>
        </div>
    </form>
    <script>
        (() => {
            const presets = {
                'classic-corporate': {primary:'#14283F',background:'#F5F1E8',accent:'#B99A61',surface:'#FFFDF8',text:'#192C41',muted:'#68727B'},
                'sage-editorial': {primary:'#34483B',background:'#F1F2E8',accent:'#A4B494',surface:'#FCFCF6',text:'#27372D',muted:'#687567'},
                'modern-lavender': {primary:'#342C4F',background:'#F4F0FA',accent:'#B9A8E5',surface:'#FFFDFF',text:'#29233C',muted:'#746D83'},
                'ocean-professional': {primary:'#123C50',background:'#EDF5F7',accent:'#59B5C7',surface:'#FFFFFF',text:'#183746',muted:'#617983'},
                'warm-minimal': {primary:'#51382F',background:'#FAF1E9',accent:'#D58B6D',surface:'#FFFCF8',text:'#3D2C27',muted:'#82716B'}
            };
            const select = document.getElementById('theme');
            if (!select) return;
            select.addEventListener('change', () => {
                const colors = presets[select.value];
                if (!colors) return;
                Object.entries(colors).forEach(([key, value]) => {
                    const input = document.querySelector('[name="custom_colors[' + key + ']"]');
                    if (input) {
                        input.value = value;
                        const colorText = input.closest('label')?.querySelector('span span');
                        if (colorText) colorText.textContent = value;
                    }
                });
            });
        })();
    </script>
@endsection
