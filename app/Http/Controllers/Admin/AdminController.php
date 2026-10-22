<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\Firestore\ContentRepository;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
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

    public function updateSettings(Request $request, ContentRepository $content): RedirectResponse
    {
        $validated = $request->validate([
            'site_name' => ['required', 'string', 'max:100'],
            'site_title' => ['required', 'string', 'max:180'],
            'brand_name' => ['required', 'string', 'max:100'],
            'tagline' => ['nullable', 'string', 'max:160'],
            'theme' => ['required', 'in:classic-corporate,sage-editorial,modern-lavender,ocean-professional,warm-minimal'],
            'profile_image' => ['nullable', 'image', 'mimes:jpg,jpeg,png,webp', 'max:4096'],
            'favicon_url' => ['nullable', 'url:https', 'max:2048'],
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

        if ($request->hasFile('profile_image')) {
            $path = $request->file('profile_image')->store('profile-photos', 'public');
            $validated['profile_image_url'] = asset('storage/'.$path);
        } else {
            $validated['profile_image_url'] = $content->settings()['profile_image_url'] ?? '';
        }

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
