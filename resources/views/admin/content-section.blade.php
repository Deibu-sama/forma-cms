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
                    @if($sectionKey === 'skills')
                        @php
                            $softwareTools = old('software_tools', $sectionData['software_tools'] ?? array_map(fn ($name) => ['name' => $name, 'logo_url' => ''], $sectionData['items'] ?? []));
                            if (count($softwareTools) === 0) $softwareTools = [['name' => '', 'logo_url' => '']];
                        @endphp
                        <div class="mb-3 flex flex-col justify-between gap-2 sm:flex-row sm:items-end">
                            <div><label class="block text-xs font-semibold">Software and logo</label><p class="mt-1 text-[11px] leading-5 text-[#68727b]">Set a name and its own HTTPS logo URL for each tool. Logos are optional; missing logos use a simple initials tile.</p></div>
                            <button type="button" id="add-software-tool" class="inline-flex min-h-9 items-center justify-center rounded-lg border border-[#14283f]/15 bg-white px-3 py-2 text-xs font-semibold text-[#14283f] hover:border-[#b99a61]">+ Add software</button>
                        </div>
                        <div id="software-tools-list" class="space-y-3">
                            @foreach($softwareTools as $index => $tool)
                                <div class="software-tool-row grid gap-3 rounded-xl border border-[#14283f]/10 bg-[#f8f6f0] p-4 sm:grid-cols-[minmax(0,.8fr)_minmax(0,1.2fr)_auto] sm:items-end">
                                    <div><label for="software-name-{{ $index }}" class="mb-2 block text-[11px] font-semibold">Software name</label><input id="software-name-{{ $index }}" name="software_tools[{{ $index }}][name]" value="{{ $tool['name'] ?? '' }}" maxlength="180" class="form-field text-sm" placeholder="e.g. QuickBooks"></div>
                                    <div><label for="software-logo-{{ $index }}" class="mb-2 block text-[11px] font-semibold">Logo image URL (HTTPS)</label><input id="software-logo-{{ $index }}" name="software_tools[{{ $index }}][logo_url]" value="{{ $tool['logo_url'] ?? '' }}" type="url" maxlength="2048" class="form-field text-sm" placeholder="https://example.com/quickbooks-logo.png"></div>
                                    <button type="button" class="remove-software-tool inline-flex min-h-10 items-center justify-center rounded-lg px-3 py-2 text-xs font-semibold text-rose-700 hover:bg-rose-50">Remove</button>
                                </div>
                            @endforeach
                        </div>
                        <template id="software-tool-template">
                            <div class="software-tool-row grid gap-3 rounded-xl border border-[#14283f]/10 bg-[#f8f6f0] p-4 sm:grid-cols-[minmax(0,.8fr)_minmax(0,1.2fr)_auto] sm:items-end">
                                <div><label class="mb-2 block text-[11px] font-semibold">Software name</label><input data-field="name" maxlength="180" class="form-field text-sm" placeholder="e.g. QuickBooks"></div>
                                <div><label class="mb-2 block text-[11px] font-semibold">Logo image URL (HTTPS)</label><input data-field="logo_url" type="url" maxlength="2048" class="form-field text-sm" placeholder="https://example.com/software-logo.png"></div>
                                <button type="button" class="remove-software-tool inline-flex min-h-10 items-center justify-center rounded-lg px-3 py-2 text-xs font-semibold text-rose-700 hover:bg-rose-50">Remove</button>
                            </div>
                        </template>
                        <script>
                            (() => {
                                const list = document.getElementById('software-tools-list');
                                const add = document.getElementById('add-software-tool');
                                const template = document.getElementById('software-tool-template');
                                if (!list || !add || !template) return;
                                let nextIndex = list.querySelectorAll('.software-tool-row').length;
                                const reindex = () => list.querySelectorAll('.software-tool-row').forEach((row, index) => {
                                    row.querySelectorAll('input').forEach((input) => {
                                        const field = input.name.includes('[logo_url]') || input.dataset.field === 'logo_url' ? 'logo_url' : 'name';
                                        input.name = 'software_tools[' + index + '][' + field + ']';
                                        input.id = 'software-' + field.replace('_', '-') + '-' + index;
                                        const label = input.parentElement.querySelector('label');
                                        if (label) label.htmlFor = input.id;
                                    });
                                });
                                add.addEventListener('click', () => {
                                    const fragment = template.content.cloneNode(true);
                                    const row = fragment.querySelector('.software-tool-row');
                                    row.querySelectorAll('input').forEach((input) => {
                                        const field = input.dataset.field;
                                        input.name = 'software_tools[' + nextIndex + '][' + field + ']';
                                        input.id = 'software-' + field.replace('_', '-') + '-' + nextIndex;
                                        input.parentElement.querySelector('label').htmlFor = input.id;
                                    });
                                    nextIndex++;
                                    list.appendChild(fragment);
                                });
                                list.addEventListener('click', (event) => {
                                    const remove = event.target.closest('.remove-software-tool');
                                    if (!remove) return;
                                    const rows = list.querySelectorAll('.software-tool-row');
                                    if (rows.length === 1) {
                                        rows[0].querySelectorAll('input').forEach((input) => input.value = '');
                                        return;
                                    }
                                    remove.closest('.software-tool-row').remove();
                                    reindex();
                                });
                            })();
                        </script>
                    @else
                        <label for="items_text" class="mb-2 block text-xs font-semibold">List items (optional)</label>
                        <textarea id="items_text" name="items_text" rows="7" maxlength="6000" class="form-field resize-y text-sm" placeholder="Enter one item per line">{{ old('items_text', implode("\n", $sectionData['items'] ?? [])) }}</textarea>
                        <p class="mt-1.5 text-[10px] leading-5 text-[#68727b]">Enter one service, skill, value, or step per line.</p>
                    @endif
                </div>
                </div>
            </div>
        </section>
        <div class="flex flex-wrap items-center gap-3"><button type="submit" class="inline-flex min-h-11 items-center gap-2 rounded-md bg-[#14283f] px-5 py-3 text-xs font-bold text-white transition hover:bg-[#263e56]">Save {{ $sectionLabel }} <span class="text-[#b99a61]">↗</span></button><a href="{{ route('admin.dashboard') }}" class="rounded-md border border-[#14283f]/15 px-5 py-3 text-xs font-semibold text-[#14283f] no-underline">Back to dashboard</a></div>
    </form>
@endsection
