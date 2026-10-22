@extends('admin.layout')

@section('title', $sectionLabel)
@section('page-title', $sectionLabel)
@section('eyebrow', 'Website / Content / '.$sectionLabel)

@section('content')
    <div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
        <div><p class="mb-2 text-[10px] font-bold uppercase tracking-[.2em] text-[#a18755]">Page content editor</p><h2 class="text-3xl font-semibold tracking-[-.055em]">{{ $sectionLabel }}</h2><p class="mt-3 max-w-2xl text-sm leading-6 text-[#68727b]">Edit the content for this section and choose whether it appears on the public portfolio.</p></div>
        <a href="{{ route('home') }}#{{ $sectionKey }}" target="_blank" rel="noopener noreferrer" class="inline-flex min-h-10 items-center justify-center rounded-md border border-[#14283f]/15 bg-[#fffdf8] px-4 py-2 text-xs font-bold text-[#14283f] no-underline">Preview section ↗</a>
    </div>
    <form method="POST" action="{{ route('admin.content.update', $sectionKey) }}" class="space-y-6">
        @csrf @method('PUT')
        <section class="rounded-xl border border-[#14283f]/10 bg-[#fffdf8] p-5 sm:p-7">
            <div class="mb-6 flex flex-col justify-between gap-4 border-b border-[#14283f]/10 pb-5 sm:flex-row sm:items-center">
                <div><h3 class="text-base font-semibold">Section visibility</h3><p class="mt-1 text-xs leading-5 text-[#68727b]">Turn this off to hide the section from public visitors without deleting its content.</p></div>
                <label class="inline-flex cursor-pointer items-center gap-3 rounded-full border border-[#14283f]/10 bg-[#f5f1e8] px-4 py-3 text-xs font-semibold"><input type="checkbox" name="enabled" value="1" @checked(old('enabled', $sectionData['enabled'] ?? true)) class="h-4 w-4 accent-[#14283f]"> Show on public website</label>
            </div>
            <div class="space-y-5">
                <div><label for="title" class="mb-2 block text-xs font-semibold">Section title / headline</label><input id="title" name="title" required maxlength="180" value="{{ old('title', $sectionData['title'] ?? '') }}" class="form-field text-sm"></div>
                <div><label for="subtitle" class="mb-2 block text-xs font-semibold">Subtitle / short label</label><input id="subtitle" name="subtitle" maxlength="240" value="{{ old('subtitle', $sectionData['subtitle'] ?? '') }}" class="form-field text-sm" placeholder="Optional subtitle"></div>
                <div><label for="body" class="mb-2 block text-xs font-semibold">Main description</label><textarea id="body" name="body" rows="6" maxlength="5000" class="form-field resize-y text-sm" placeholder="Write the text visitors will read...">{{ old('body', $sectionData['body'] ?? '') }}</textarea></div>
                <div>
                    <label for="items_text" class="mb-2 block text-xs font-semibold">{{ $sectionKey === 'skills' ? 'Software and logo image URLs' : 'List items (optional)' }}</label>
                    @if($sectionKey === 'skills')
                        @php
                            $softwareTools = $sectionData['software_tools'] ?? array_map(fn ($name) => ['name' => $name, 'logo_url' => ''], $sectionData['items'] ?? []);
                            $softwareLines = implode("\n", array_map(fn ($tool) => ($tool['name'] ?? '').' | '.($tool['logo_url'] ?? ''), $softwareTools));
                        @endphp
                        <textarea id="items_text" name="items_text" rows="8" maxlength="6000" class="form-field resize-y text-sm" placeholder="BizBox Financial Management System | https://example.com/bizbox-logo.png&#10;QuickBooks | https://example.com/quickbooks-logo.png&#10;Xero | https://example.com/xero-logo.png&#10;Microsoft Excel | &#10;Power BI | ">{{ old('items_text', $softwareLines) }}</textarea>
                        <p class="mt-1 text-[10px] leading-5 text-[#68727b]">One software per line, formatted as <strong>Software name | HTTPS logo image URL</strong>. Leave the URL blank to use the default placeholder image configured in Website Settings. You can add or remove software by editing these lines.</p>
                    @else
                        <textarea id="items_text" name="items_text" rows="7" maxlength="6000" class="form-field resize-y text-sm" placeholder="Enter one item per line">{{ old('items_text', implode("\n", $sectionData['items'] ?? [])) }}</textarea>
                        <p class="mt-1 text-[10px] leading-5 text-[#68727b]">Enter one service, skill, value, or step per line.</p>
                    @endif
                </div>
            </div>
        </section>
        <div class="flex flex-wrap items-center gap-3"><button type="submit" class="inline-flex min-h-11 items-center gap-2 rounded-md bg-[#14283f] px-5 py-3 text-xs font-bold text-white transition hover:bg-[#263e56]">Save {{ $sectionLabel }} <span class="text-[#b99a61]">↗</span></button><a href="{{ route('admin.dashboard') }}" class="rounded-md border border-[#14283f]/15 px-5 py-3 text-xs font-semibold text-[#14283f] no-underline">Back to dashboard</a></div>
    </form>
@endsection
