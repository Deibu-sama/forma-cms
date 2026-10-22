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
        $isDemo = $this->content->isDemoMode();

        return view('app', [
            'settings' => $settings,
            'appData' => [
                'settings' => $settings,
                'projects' => $projects,
                'isDemo' => $isDemo,
            ],
        ]);
    }
}
