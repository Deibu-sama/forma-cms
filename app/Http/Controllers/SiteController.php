<?php

namespace App\Http\Controllers;

use App\Services\Firestore\ContentRepository;
use Illuminate\Contracts\View\View;

final class SiteController
{
    public function __construct(
        private readonly ContentRepository $content,
    ) {}

    public function home(): View
    {
        $settings = $this->content->settings();
        $projects = $this->content->publishedProjects();

        return view('home', [
            'settings' => $settings,
            'projects' => $projects,
            'theme' => $settings['theme'] ?? 'classic-corporate',
            'isDemo' => $this->content->isDemoMode(),
        ]);
    }
}
