@extends('admin.layout')

@section('title', 'Portfolio Projects')
@section('page-title', 'Portfolio projects')
@section('eyebrow', 'Website / Content / Work samples')

@section('content')
    <div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
        <div><p class="mb-2 text-[10px] font-bold uppercase tracking-[.2em] text-[#a18755]">Your professional work</p><h2 class="text-3xl font-semibold tracking-[-.055em]">Portfolio & work samples</h2><p class="mt-3 max-w-2xl text-sm leading-6 text-[#68727b]">Add each project using a title, summary, and optional public image URL. You can edit the details or remove a project at any time.</p></div>
        <a href="{{ route('home') }}#portfolio" target="_blank" rel="noopener noreferrer" class="inline-flex min-h-10 items-center justify-center rounded-md border border-[#14283f]/15 bg-[#fffdf8] px-4 py-2 text-xs font-bold text-[#14283f] no-underline">Preview portfolio ↗</a>
    </div>
    <section class="mb-8 rounded-xl border border-[#14283f]/10 bg-[#fffdf8] p-5 sm:p-7">
        <h3 class="text-base font-semibold">Add a project</h3><p class="mt-1 mb-5 text-xs leading-5 text-[#68727b]">For the image, paste a direct HTTPS image URL. Do not use a private or sign-in-only link.</p>
        <form method="POST" action="{{ route('admin.projects.store') }}" class="grid gap-4 sm:grid-cols-2">@csrf
            <div><label for="new-title" class="mb-2 block text-xs font-semibold">Project title / name *</label><input id="new-title" name="title" required maxlength="140" value="{{ old('title') }}" class="form-field text-sm" placeholder="e.g. Monthly Bookkeeping Workflow"></div>
            <div><label for="new-category" class="mb-2 block text-xs font-semibold">Category</label><input id="new-category" name="category" maxlength="100" value="{{ old('category') }}" class="form-field text-sm" placeholder="e.g. Reconciliation"></div>
            <div class="sm:col-span-2"><label for="new-summary" class="mb-2 block text-xs font-semibold">Short summary *</label><textarea id="new-summary" name="summary" required maxlength="800" rows="3" class="form-field resize-y text-sm" placeholder="Briefly describe the project and what it demonstrates...">{{ old('summary') }}</textarea></div>
            <div class="sm:col-span-2"><label for="new-description" class="mb-2 block text-xs font-semibold">Additional description</label><textarea id="new-description" name="description" maxlength="1800" rows="3" class="form-field resize-y text-sm" placeholder="Optional extra detail...">{{ old('description') }}</textarea></div>
            <div class="sm:col-span-2"><label for="new-image" class="mb-2 block text-xs font-semibold">Image URL (HTTPS)</label><input id="new-image" name="image_url" type="url" value="{{ old('image_url') }}" maxlength="2048" class="form-field text-sm" placeholder="https://example.com/my-project.jpg"><p class="mt-1 text-[10px] leading-5 text-[#68727b]">Paste a link that points directly to a publicly viewable image.</p></div>
            <div><label for="new-alt" class="mb-2 block text-xs font-semibold">Image description</label><input id="new-alt" name="image_alt" maxlength="240" value="{{ old('image_alt') }}" class="form-field text-sm" placeholder="Describe the image for accessibility"></div>
            <div><label for="new-art" class="mb-2 block text-xs font-semibold">Image fallback label</label><input id="new-art" name="art_label" maxlength="140" value="{{ old('art_label') }}" class="form-field text-sm" placeholder="e.g. Monthly reporting dashboard"></div>
            <div class="sm:col-span-2"><label for="new-link" class="mb-2 block text-xs font-semibold">Project link (optional HTTPS URL)</label><input id="new-link" name="project_url" type="url" value="{{ old('project_url') }}" maxlength="2048" class="form-field text-sm" placeholder="https://example.com/project"></div>
            <div class="sm:col-span-2"><button class="inline-flex min-h-10 items-center gap-2 rounded-md bg-[#14283f] px-5 py-3 text-xs font-bold text-white transition hover:bg-[#263e56]" type="submit">Add project <span class="text-[#b99a61]">+</span></button></div>
        </form>
    </section>
    <div class="mb-4 flex items-center justify-between"><h3 class="text-sm font-semibold">Saved projects</h3><span class="rounded-full bg-[#e9e4d9] px-3 py-1 text-[10px] font-bold">{{ count($projects) }} total</span></div>
    <div class="space-y-4">
        @forelse($projects as $index => $project)
            <section class="rounded-xl border border-[#14283f]/10 bg-[#fffdf8] p-5 sm:p-6">
                <div class="mb-5 flex flex-col gap-4 sm:flex-row sm:items-start">
                    @if(!empty($project['image_url']))<img src="{{ $project['image_url'] }}" alt="{{ $project['image_alt'] ?? '' }}" class="h-24 w-32 rounded-lg border border-[#14283f]/10 object-cover">@else<div class="grid h-24 w-32 place-items-center rounded-lg bg-[#f0ebe0] text-center text-[10px] text-[#68727b]">No image linked</div>@endif
                    <div class="flex-1"><p class="text-[10px] font-bold uppercase tracking-[.18em] text-[#a18755]">Project {{ $index + 1 }}</p><h4 class="mt-1 text-base font-semibold">{{ $project['title'] ?? 'Untitled project' }}</h4><p class="mt-1 text-xs leading-5 text-[#68727b]">{{ $project['summary'] ?? '' }}</p></div>
                    <form method="POST" action="{{ route('admin.projects.destroy', $index) }}" onsubmit="return confirm('Delete this project?')">@csrf @method('DELETE')<button type="submit" class="rounded-md px-3 py-2 text-[11px] font-semibold text-rose-700 transition hover:bg-rose-50">Delete</button></form>
                </div>
                <form method="POST" action="{{ route('admin.projects.update', $index) }}" class="grid gap-4 sm:grid-cols-2">@csrf @method('PUT')
                    <div><label for="title-{{ $index }}" class="mb-2 block text-xs font-semibold">Project title / name *</label><input id="title-{{ $index }}" name="title" required maxlength="140" value="{{ old('title', $project['title'] ?? '') }}" class="form-field text-sm"></div>
                    <div><label for="category-{{ $index }}" class="mb-2 block text-xs font-semibold">Category</label><input id="category-{{ $index }}" name="category" maxlength="100" value="{{ old('category', $project['category'] ?? '') }}" class="form-field text-sm"></div>
                    <div class="sm:col-span-2"><label for="summary-{{ $index }}" class="mb-2 block text-xs font-semibold">Short summary *</label><textarea id="summary-{{ $index }}" name="summary" required maxlength="800" rows="3" class="form-field resize-y text-sm">{{ old('summary', $project['summary'] ?? '') }}</textarea></div>
                    <div class="sm:col-span-2"><label for="description-{{ $index }}" class="mb-2 block text-xs font-semibold">Additional description</label><textarea id="description-{{ $index }}" name="description" maxlength="1800" rows="3" class="form-field resize-y text-sm">{{ old('description', $project['description'] ?? '') }}</textarea></div>
                    <div class="sm:col-span-2"><label for="image-{{ $index }}" class="mb-2 block text-xs font-semibold">Image URL (HTTPS)</label><input id="image-{{ $index }}" name="image_url" type="url" maxlength="2048" value="{{ old('image_url', $project['image_url'] ?? '') }}" class="form-field text-sm" placeholder="https://example.com/my-project.jpg"></div>
                    <div><label for="alt-{{ $index }}" class="mb-2 block text-xs font-semibold">Image description</label><input id="alt-{{ $index }}" name="image_alt" maxlength="240" value="{{ old('image_alt', $project['image_alt'] ?? '') }}" class="form-field text-sm"></div>
                    <div><label for="art-{{ $index }}" class="mb-2 block text-xs font-semibold">Image fallback label</label><input id="art-{{ $index }}" name="art_label" maxlength="140" value="{{ old('art_label', $project['art_label'] ?? '') }}" class="form-field text-sm"></div>
                    <div class="sm:col-span-2"><label for="link-{{ $index }}" class="mb-2 block text-xs font-semibold">Project link (optional HTTPS URL)</label><input id="link-{{ $index }}" name="project_url" type="url" maxlength="2048" value="{{ old('project_url', $project['project_url'] ?? '') }}" class="form-field text-sm"></div>
                    <div class="sm:col-span-2"><button type="submit" class="rounded-md border border-[#14283f]/15 px-4 py-2.5 text-xs font-bold text-[#14283f] transition hover:border-[#b99a61] hover:bg-[#f8f4eb]">Save project changes</button></div>
                </form>
            </section>
        @empty
            <div class="rounded-xl border border-dashed border-[#14283f]/20 bg-white/50 p-8 text-center"><p class="text-sm font-semibold">No custom projects yet</p><p class="mt-2 text-xs text-[#68727b]">Add your first work sample above. Until then, the website uses illustrative sample projects.</p></div>
        @endforelse
    </div>
@endsection
