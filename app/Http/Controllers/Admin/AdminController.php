<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Firestore\ContentRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

final class AdminController
{
    public function loginForm(): View|RedirectResponse
    {
        if (Auth::check() && Auth::user()?->is_admin) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email:rfc', 'max:254'],
            'password' => ['required', 'string', 'max:1024'],
        ]);

        if (Auth::attempt($credentials + ['is_admin' => true], $request->boolean('remember'))) {
            $request->session()->regenerate();

            return redirect()->intended(route('admin.dashboard'));
        }

        return back()
            ->withErrors(['email' => 'Those credentials do not match an administrator account.'])
            ->onlyInput('email');
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login')->with('status', 'You have been signed out.');
    }

    public function dashboard(ContentRepository $content): View
    {
        $settings = $content->settings();
        $projects = $content->publishedProjects();

        return view('admin.dashboard', [
            'settings' => $settings,
            'projectCount' => count($projects),
            'demoMode' => $content->isDemoMode(),
            'administrator' => Auth::user(),
        ]);
    }

    public function settings(ContentRepository $content): View
    {
        return view('admin.settings', [
            'settings' => $content->settings(),
            'firestoreReady' => ! $content->isDemoMode(),
        ]);
    }

    private function editableSections(): array
    {
        return [
            'home' => 'Home',
            'about' => 'About',
            'services' => 'Services',
            'tax-compliance' => 'Tax Compliance Support',
            'experience' => 'Professional Experience',
            'skills' => 'Software & Technical Skills',
            'how-i-work' => 'How I Work',
            'packages' => 'Engagement Packages',
            'why-work-with-me' => 'Why Work With Me',
        ];
    }

    public function editContentSection(string $section, ContentRepository $content): View|RedirectResponse
    {
        $labels = $this->editableSections();
        abort_unless(isset($labels[$section]), 404);
        $settings = $content->settings();
        $sections = $settings['content_sections'] ?? [];
        $data = $sections[$section] ?? ['title' => $labels[$section], 'subtitle' => '', 'body' => '', 'items' => [], 'enabled' => true];
        return view('admin.content-section', ['sectionKey' => $section, 'sectionLabel' => $labels[$section], 'sectionData' => $data]);
    }

    public function updateContentSection(Request $request, string $section, ContentRepository $content): RedirectResponse
    {
        $labels = $this->editableSections();
        abort_unless(isset($labels[$section]), 404);
        $data = $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'subtitle' => ['nullable', 'string', 'max:240'],
            'body' => ['nullable', 'string', 'max:5000'],
            'items_text' => ['nullable', 'string', 'max:6000'],
            'enabled' => ['nullable', 'boolean'],
        ]);
        $settings = $content->settings();
        $sections = $settings['content_sections'] ?? [];
        $sections[$section] = [
            'title' => trim($data['title']),
            'subtitle' => trim($data['subtitle'] ?? ''),
            'body' => trim($data['body'] ?? ''),
            'items' => array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', $data['items_text'] ?? '') ?: []), fn ($item) => $item !== '')),
            'enabled' => $request->boolean('enabled'),
        ];
        $settings['content_sections'] = $sections;
        $savedTo = $content->saveSettings($this->settingsPayload($settings));
        if ($savedTo === false) return back()->withInput()->with('error', 'Section could not be saved. Check the database connection and try again.');
        return redirect()->route('admin.content.edit', $section)->with('success', 'Section saved.');
    }

    public function certificates(ContentRepository $content): View
    {
        return view('admin.certificates', ['certificates' => $content->settings()['certificates'] ?? []]);
    }

    public function storeCertificate(Request $request, ContentRepository $content): RedirectResponse
    {
        $certificate = $this->validateCertificate($request);
        $settings = $content->settings();
        $certificates = $settings['certificates'] ?? [];
        $certificates[] = $certificate;
        return $this->saveCertificates($content, $settings, $certificates, 'Certificate added.');
    }

    public function updateCertificate(Request $request, ContentRepository $content, int $index): RedirectResponse
    {
        $certificate = $this->validateCertificate($request);
        $settings = $content->settings();
        $certificates = $settings['certificates'] ?? [];
        if (! array_key_exists($index, $certificates)) return back()->with('error', 'Certificate not found.');
        $certificates[$index] = $certificate;
        return $this->saveCertificates($content, $settings, array_values($certificates), 'Certificate updated.');
    }

    public function destroyCertificate(ContentRepository $content, int $index): RedirectResponse
    {
        $settings = $content->settings();
        $certificates = $settings['certificates'] ?? [];
        if (! array_key_exists($index, $certificates)) return back()->with('error', 'Certificate not found.');
        array_splice($certificates, $index, 1);
        return $this->saveCertificates($content, $settings, $certificates, 'Certificate deleted.');
    }

    private function validateCertificate(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'description' => ['required', 'string', 'max:1000'],
            'image_url' => ['required', 'url:https', 'max:2048'],
            'image_alt' => ['nullable', 'string', 'max:240'],
            'issuer' => ['nullable', 'string', 'max:180'],
            'issued_date' => ['nullable', 'string', 'max:80'],
            'certificate_url' => ['nullable', 'url:https', 'max:2048'],
            'enabled' => ['nullable', 'boolean'],
        ]);
        return [
            'title' => trim($data['title']),
            'description' => trim($data['description']),
            'image_url' => trim($data['image_url']),
            'image_alt' => trim($data['image_alt'] ?? ''),
            'issuer' => trim($data['issuer'] ?? ''),
            'issued_date' => trim($data['issued_date'] ?? ''),
            'certificate_url' => trim($data['certificate_url'] ?? ''),
            'enabled' => $request->boolean('enabled'),
        ];
    }

    private function saveCertificates(ContentRepository $content, array $settings, array $certificates, string $message): RedirectResponse
    {
        $settings['certificates'] = $certificates;
        $savedTo = $content->saveSettings($this->settingsPayload($settings));
        if ($savedTo === false) return back()->withInput()->with('error', 'Certificates could not be saved. Check the database connection and try again.');
        return redirect()->route('admin.certificates')->with('success', $message);
    }

    private function settingsPayload(array $settings): array
    {
        $settings['faqs_json'] = json_encode($settings['faqs'] ?? [], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $settings['projects_json'] = json_encode($settings['projects'] ?? [], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $settings['faqs_customized'] = (bool) ($settings['faqs_customized'] ?? false);
        $settings['projects_customized'] = (bool) ($settings['projects_customized'] ?? false);
        return $settings;
    }

    public function faqs(ContentRepository $content): View
    {
        return view('admin.faqs', ['faqs' => $content->settings()['faqs'] ?? []]);
    }

    public function projects(ContentRepository $content): View
    {
        return view('admin.projects', ['projects' => $content->settings()['projects'] ?? []]);
    }

    public function storeFaq(Request $request, ContentRepository $content): RedirectResponse
    {
        $item = $request->validate(['q' => ['required', 'string', 'max:240'], 'a' => ['required', 'string', 'max:1600']]);
        $settings = $content->settings();
        $items = $settings['faqs'] ?? [];
        $items[] = ['q' => trim($item['q']), 'a' => trim($item['a'])];
        return $this->saveContentCollection($content, $settings, 'faqs', $items, 'FAQ added.');
    }

    public function updateFaq(Request $request, ContentRepository $content, int $index): RedirectResponse
    {
        $item = $request->validate(['q' => ['required', 'string', 'max:240'], 'a' => ['required', 'string', 'max:1600']]);
        $settings = $content->settings();
        $items = $settings['faqs'] ?? [];
        if (! array_key_exists($index, $items)) return back()->with('error', 'That FAQ could not be found.');
        $items[$index] = ['q' => trim($item['q']), 'a' => trim($item['a'])];
        return $this->saveContentCollection($content, $settings, 'faqs', array_values($items), 'FAQ updated.');
    }

    public function destroyFaq(ContentRepository $content, int $index): RedirectResponse
    {
        $settings = $content->settings();
        $items = $settings['faqs'] ?? [];
        if (! array_key_exists($index, $items)) return back()->with('error', 'That FAQ could not be found.');
        array_splice($items, $index, 1);
        return $this->saveContentCollection($content, $settings, 'faqs', $items, 'FAQ deleted.');
    }

    public function storeProject(Request $request, ContentRepository $content): RedirectResponse
    {
        $item = $this->validateProject($request);
        $settings = $content->settings();
        $items = $settings['projects'] ?? [];
        $items[] = $item;
        return $this->saveContentCollection($content, $settings, 'projects', $items, 'Project added.');
    }

    public function updateProject(Request $request, ContentRepository $content, int $index): RedirectResponse
    {
        $item = $this->validateProject($request);
        $settings = $content->settings();
        $items = $settings['projects'] ?? [];
        if (! array_key_exists($index, $items)) return back()->with('error', 'That project could not be found.');
        $items[$index] = $item;
        return $this->saveContentCollection($content, $settings, 'projects', array_values($items), 'Project updated.');
    }

    public function destroyProject(ContentRepository $content, int $index): RedirectResponse
    {
        $settings = $content->settings();
        $items = $settings['projects'] ?? [];
        if (! array_key_exists($index, $items)) return back()->with('error', 'That project could not be found.');
        array_splice($items, $index, 1);
        return $this->saveContentCollection($content, $settings, 'projects', $items, 'Project deleted.');
    }

    private function validateProject(Request $request): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:140'],
            'category' => ['nullable', 'string', 'max:100'],
            'summary' => ['required', 'string', 'max:800'],
            'description' => ['nullable', 'string', 'max:1800'],
            'image_url' => ['nullable', 'url:https', 'max:2048'],
            'image_alt' => ['nullable', 'string', 'max:240'],
            'art_label' => ['nullable', 'string', 'max:140'],
            'project_url' => ['nullable', 'url:https', 'max:2048'],
        ]);
        $title = trim($data['title']);
        return [
            'title' => $title,
            'slug' => strtolower(trim(preg_replace('/[^a-z0-9]+/i', '-', $title), '-')),
            'category' => trim($data['category'] ?? ''),
            'summary' => trim($data['summary']),
            'description' => trim($data['description'] ?? ''),
            'image_url' => trim($data['image_url'] ?? ''),
            'image_alt' => trim($data['image_alt'] ?? ''),
            'art_label' => trim($data['art_label'] ?? ''),
            'project_url' => trim($data['project_url'] ?? ''),
            'is_demonstration' => false,
        ];
    }

    private function saveContentCollection(ContentRepository $content, array $settings, string $collection, array $items, string $success): RedirectResponse
    {
        $settings['faqs_json'] = json_encode($collection === 'faqs' ? $items : ($settings['faqs'] ?? []), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $settings['projects_json'] = json_encode($collection === 'projects' ? $items : ($settings['projects'] ?? []), JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $settings['faqs_customized'] = $collection === 'faqs' ? true : (bool) ($settings['faqs_customized'] ?? false);
        $settings['projects_customized'] = $collection === 'projects' ? true : (bool) ($settings['projects_customized'] ?? false);
        $savedTo = $content->saveSettings($settings);
        if ($savedTo === false) return back()->withInput()->with('error', 'Could not save this content. Check database access and try again.');
        return redirect()->route('admin.' . $collection)->with('success', $success);
    }

    public function updateSettings(Request $request, ContentRepository $content): RedirectResponse
    {
        $validated = $request->validate([
            'site_name' => ['required', 'string', 'max:100'],
            'site_title' => ['required', 'string', 'max:180'],
            'brand_name' => ['required', 'string', 'max:100'],
            'tagline' => ['nullable', 'string', 'max:160'],
            'theme' => ['nullable', 'in:classic-corporate,sage-editorial,modern-lavender,ocean-professional,warm-minimal'],
            'favicon_url' => ['nullable', 'url:https', 'max:2048'],
            'profile_image_url' => ['nullable', 'url:https', 'max:2048'],
            'hero_image_url' => ['nullable', 'url:https', 'max:2048'],
            'hero_image_alt' => ['nullable', 'string', 'max:240'],
            'eyebrow' => ['required', 'string', 'max:180'],
            'headline' => ['required', 'string', 'max:180'],
            'description' => ['required', 'string', 'max:700'],
            'about_headline' => ['required', 'string', 'max:220'],
            'about_text' => ['required', 'string', 'max:1200'],
            'contact_headline' => ['required', 'string', 'max:220'],
            'contact_text' => ['required', 'string', 'max:700'],
            'contact_email' => ['nullable', 'email:rfc', 'max:254'],
            'availability' => ['nullable', 'string', 'max:100'],
            'work_intro' => ['nullable', 'string', 'max:500'],
            'footer_note' => ['nullable', 'string', 'max:180'],
            'meta_description' => ['required', 'string', 'max:320'],
            'custom_colors.primary' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'custom_colors.background' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'custom_colors.accent' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'custom_colors.surface' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'custom_colors.text' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
            'custom_colors.muted' => ['required', 'regex:/^#[0-9a-fA-F]{6}$/'],
        ]);

        $validated['theme'] = $validated['theme'] ?? 'classic-corporate';
        $currentSettings = $content->settings();
        $validated['faqs_json'] = json_encode($currentSettings['faqs'] ?? [], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $validated['projects_json'] = json_encode($currentSettings['projects'] ?? [], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
        $validated['faqs_customized'] = (bool) ($currentSettings['faqs_customized'] ?? false);
        $validated['projects_customized'] = (bool) ($currentSettings['projects_customized'] ?? false);
        $validated['content_sections'] = $currentSettings['content_sections'] ?? [];
        $validated['certificates'] = $currentSettings['certificates'] ?? [];

        $savedTo = $content->saveSettings($validated);

        if ($savedTo === false) {
            return back()
                ->withInput()
                ->with('error', 'Settings could not be saved. Check that the SQL database is migrated and writable, or configure Firestore with write access.');
        }

        if ($savedTo === 'local') {
            return redirect()->route('admin.settings')->with('success', 'Settings saved to the server database fallback. Connect Firestore to use the remote CMS content store.');
        }

        return redirect()->route('admin.settings')->with('success', 'Website settings saved to Firestore successfully.');
    }
}
