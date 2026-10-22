@extends('admin.layout')

@section('title', 'Certificates')
@section('page-title', 'Certificates')
@section('eyebrow', 'Website / Content / Certificates')

@section('content')
    <div class="mb-8"><p class="mb-2 text-[10px] font-bold uppercase tracking-[.2em] text-[#a18755]">Credentials & professional development</p><h2 class="text-3xl font-semibold tracking-[-.055em]">Certificate carousel</h2><p class="mt-3 max-w-2xl text-sm leading-6 text-[#68727b]">Add certificate image links with titles and descriptions. Enabled certificates appear in an automatically rotating public carousel.</p></div>
<p class="mt-3 rounded-lg border border-[#14283f]/10 bg-[#f4f2ec] p-3 text-[11px] leading-5 text-[#68727b]">Uploaded images use public server storage. On Render, attach a persistent disk mounted at <code>storage/app/public</code> to keep uploaded files across deploys and restarts. Until that is configured, use a stable public HTTPS image URL.</p>
    <section class="mb-8 rounded-xl border border-[#14283f]/10 bg-[#fffdf8] p-5 sm:p-7">
        <h3 class="mb-5 text-base font-semibold">Add a certificate</h3>
        <form method="POST" action="{{ route('admin.certificates.store') }}" enctype="multipart/form-data" class="grid gap-4 sm:grid-cols-2">@csrf
            <div><label class="mb-2 block text-xs font-semibold" for="new-title">Certificate title *</label><input id="new-title" name="title" required maxlength="180" class="form-field text-sm" placeholder="e.g. QuickBooks Online Certification"></div>
            <div><label class="mb-2 block text-xs font-semibold" for="new-issuer">Issuing organization</label><input id="new-issuer" name="issuer" maxlength="180" class="form-field text-sm" placeholder="Organization or provider"></div>
            <div class="sm:col-span-2"><label class="mb-2 block text-xs font-semibold" for="new-description">Description *</label><textarea id="new-description" name="description" required maxlength="1000" rows="3" class="form-field resize-y text-sm" placeholder="What this certificate covers..."></textarea></div>
            <div><label class="mb-2 block text-xs font-semibold" for="new-image-file">Upload certificate image</label><input id="new-image-file" name="image_file" type="file" accept="image/jpeg,image/png,image/webp,image/gif" class="form-field text-sm"><p class="mt-1 text-[10px] text-[#68727b]">JPG, PNG, WEBP or GIF; up to 5 MB.</p></div><div><label class="mb-2 block text-xs font-semibold" for="new-image">Or certificate image URL (HTTPS)</label><input id="new-image" name="image_url" type="url" maxlength="2048" class="form-field text-sm" placeholder="https://example.com/certificate.jpg"></div>
            <div><label class="mb-2 block text-xs font-semibold" for="new-alt">Image description</label><input id="new-alt" name="image_alt" maxlength="240" class="form-field text-sm" placeholder="Accessible description"></div>
            <div><label class="mb-2 block text-xs font-semibold" for="new-date">Date / year</label><input id="new-date" name="issued_date" maxlength="80" class="form-field text-sm" placeholder="e.g. 2025"></div>
            <div class="sm:col-span-2"><label class="mb-2 block text-xs font-semibold" for="new-link">Certificate verification link (optional)</label><input id="new-link" name="certificate_url" type="url" maxlength="2048" class="form-field text-sm" placeholder="https://..."></div>
            <div class="sm:col-span-2"><label class="inline-flex items-center gap-2 text-xs font-semibold"><input type="checkbox" name="enabled" value="1" checked class="h-4 w-4 accent-[#14283f]"> Show in public carousel</label></div>
            <div class="sm:col-span-2"><button type="submit" class="rounded-md bg-[#14283f] px-5 py-3 text-xs font-bold text-white transition hover:bg-[#263e56]">Add certificate +</button></div>
        </form>
    </section>
    <div class="mb-4 flex items-center justify-between"><h3 class="text-sm font-semibold">Saved certificates</h3><span class="rounded-full bg-[#e9e4d9] px-3 py-1 text-[10px] font-bold">{{ count($certificates) }} total</span></div>
    <div class="space-y-4">
    @forelse($certificates as $index => $certificate)
        <section class="rounded-xl border border-[#14283f]/10 bg-[#fffdf8] p-5 sm:p-6">
            <div class="mb-5 flex flex-col gap-4 sm:flex-row sm:items-start">
                <img src="{{ $certificate['image_url'] }}" alt="{{ $certificate['image_alt'] ?? '' }}" class="h-24 w-32 rounded-lg border border-[#14283f]/10 object-contain bg-white p-1">
                <div class="flex-1"><p class="text-[10px] font-bold uppercase tracking-[.18em] text-[#a18755]">Certificate {{ $index + 1 }} · {{ !empty($certificate['enabled']) ? 'Visible' : 'Hidden' }}</p><h4 class="mt-1 font-semibold">{{ $certificate['title'] }}</h4><p class="mt-1 text-xs leading-5 text-[#68727b]">{{ $certificate['description'] }}</p></div>
                <form method="POST" action="{{ route('admin.certificates.destroy', $index) }}" onsubmit="return confirm('Delete this certificate?')">@csrf @method('DELETE')<button type="submit" class="rounded-md px-3 py-2 text-[11px] font-semibold text-rose-700 hover:bg-rose-50">Delete</button></form>
            </div>
            <form method="POST" action="{{ route('admin.certificates.update', $index) }}" enctype="multipart/form-data" class="grid gap-4 sm:grid-cols-2">@csrf @method('PUT')
                <div><label class="mb-2 block text-xs font-semibold" for="title-{{ $index }}">Title *</label><input id="title-{{ $index }}" name="title" required maxlength="180" value="{{ $certificate['title'] }}" class="form-field text-sm"></div>
                <div><label class="mb-2 block text-xs font-semibold" for="issuer-{{ $index }}">Issuing organization</label><input id="issuer-{{ $index }}" name="issuer" maxlength="180" value="{{ $certificate['issuer'] ?? '' }}" class="form-field text-sm"></div>
                <div class="sm:col-span-2"><label class="mb-2 block text-xs font-semibold" for="desc-{{ $index }}">Description *</label><textarea id="desc-{{ $index }}" name="description" required maxlength="1000" rows="3" class="form-field resize-y text-sm">{{ $certificate['description'] }}</textarea></div>
                <div><label class="mb-2 block text-xs font-semibold" for="image-file-{{ $index }}">Upload replacement image</label><input id="image-file-{{ $index }}" name="image_file" type="file" accept="image/jpeg,image/png,image/webp,image/gif" class="form-field text-sm"><p class="mt-1 text-[10px] text-[#68727b]">Optional; max 5 MB.</p></div><div><label class="mb-2 block text-xs font-semibold" for="image-{{ $index }}">Or image URL (HTTPS)</label><input id="image-{{ $index }}" name="image_url" type="url" maxlength="2048" value="{{ $certificate['image_url'] }}" class="form-field text-sm"></div>
                <div><label class="mb-2 block text-xs font-semibold" for="alt-{{ $index }}">Image description</label><input id="alt-{{ $index }}" name="image_alt" maxlength="240" value="{{ $certificate['image_alt'] ?? '' }}" class="form-field text-sm"></div>
                <div><label class="mb-2 block text-xs font-semibold" for="date-{{ $index }}">Date / year</label><input id="date-{{ $index }}" name="issued_date" maxlength="80" value="{{ $certificate['issued_date'] ?? '' }}" class="form-field text-sm"></div>
                <div class="sm:col-span-2"><label class="mb-2 block text-xs font-semibold" for="link-{{ $index }}">Verification link</label><input id="link-{{ $index }}" name="certificate_url" type="url" maxlength="2048" value="{{ $certificate['certificate_url'] ?? '' }}" class="form-field text-sm"></div>
                <div class="sm:col-span-2"><label class="inline-flex items-center gap-2 text-xs font-semibold"><input type="checkbox" name="enabled" value="1" @checked(!empty($certificate['enabled'])) class="h-4 w-4 accent-[#14283f]"> Show in public carousel</label></div>
                <div class="sm:col-span-2"><button type="submit" class="rounded-md border border-[#14283f]/15 px-4 py-2.5 text-xs font-bold">Save certificate changes</button></div>
            </form>
        </section>
    @empty
        <div class="rounded-xl border border-dashed border-[#14283f]/20 p-8 text-center"><p class="text-sm font-semibold">No certificates yet</p><p class="mt-2 text-xs text-[#68727b]">Add a certificate above to start your carousel.</p></div>
    @endforelse
    </div>
@endsection
