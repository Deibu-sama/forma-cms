@extends('admin.layout')

@section('title', 'FAQ Manager')
@section('page-title', 'FAQs')
@section('eyebrow', 'Website / Content / FAQs')

@section('content')
    <div class="mb-8 flex flex-col justify-between gap-4 sm:flex-row sm:items-end">
        <div><p class="mb-2 text-[10px] font-bold uppercase tracking-[.2em] text-[#a18755]">Public website content</p><h2 class="text-3xl font-semibold tracking-[-.055em]">Frequently asked questions</h2><p class="mt-3 max-w-2xl text-sm leading-6 text-[#68727b]">Add the questions guests ask and the answers you want them to see. Each item can be edited or removed separately.</p></div>
        <a href="{{ route('home') }}#faqs" target="_blank" rel="noopener noreferrer" class="inline-flex min-h-10 items-center justify-center rounded-md border border-[#14283f]/15 bg-[#fffdf8] px-4 py-2 text-xs font-bold text-[#14283f] no-underline">Preview FAQ section ↗</a>
    </div>
    <section class="mb-8 rounded-xl border border-[#14283f]/10 bg-[#fffdf8] p-5 sm:p-7">
        <h3 class="text-base font-semibold">Add a question</h3><p class="mt-1 mb-5 text-xs leading-5 text-[#68727b]">Keep answers clear, friendly, and specific to your services.</p>
        <form method="POST" action="{{ route('admin.faqs.store') }}" class="space-y-4">@csrf
            <div><label for="new-q" class="mb-2 block text-xs font-semibold">Question</label><input id="new-q" name="q" required maxlength="240" value="{{ old('q') }}" class="form-field text-sm" placeholder="e.g. What services do you offer?"></div>
            <div><label for="new-a" class="mb-2 block text-xs font-semibold">Answer</label><textarea id="new-a" name="a" required maxlength="1600" rows="4" class="form-field resize-y text-sm" placeholder="Write the answer guests will see...">{{ old('a') }}</textarea></div>
            <button class="inline-flex min-h-10 items-center gap-2 rounded-md bg-[#14283f] px-5 py-3 text-xs font-bold text-white transition hover:bg-[#263e56]" type="submit">Add FAQ <span class="text-[#b99a61]">+</span></button>
        </form>
    </section>
    <div class="mb-4 flex items-center justify-between"><h3 class="text-sm font-semibold">Saved questions</h3><span class="rounded-full bg-[#e9e4d9] px-3 py-1 text-[10px] font-bold">{{ count($faqs) }} total</span></div>
    <div class="space-y-4">
        @forelse($faqs as $index => $faq)
            <section class="rounded-xl border border-[#14283f]/10 bg-[#fffdf8] p-5 sm:p-6">
                <div class="mb-4 flex items-center justify-between gap-3"><p class="text-[10px] font-bold uppercase tracking-[.18em] text-[#a18755]">Question {{ $index + 1 }}</p><form method="POST" action="{{ route('admin.faqs.destroy', $index) }}" onsubmit="return confirm('Delete this FAQ?')">@csrf @method('DELETE')<button type="submit" class="rounded-md px-3 py-2 text-[11px] font-semibold text-rose-700 transition hover:bg-rose-50">Delete</button></form></div>
                <form method="POST" action="{{ route('admin.faqs.update', $index) }}" class="space-y-4">@csrf @method('PUT')
                    <div><label for="q-{{ $index }}" class="mb-2 block text-xs font-semibold">Question</label><input id="q-{{ $index }}" name="q" required maxlength="240" value="{{ old('q', $faq['q'] ?? '') }}" class="form-field text-sm"></div>
                    <div><label for="a-{{ $index }}" class="mb-2 block text-xs font-semibold">Answer</label><textarea id="a-{{ $index }}" name="a" required maxlength="1600" rows="3" class="form-field resize-y text-sm">{{ old('a', $faq['a'] ?? '') }}</textarea></div>
                    <button type="submit" class="rounded-md border border-[#14283f]/15 px-4 py-2.5 text-xs font-bold text-[#14283f] transition hover:border-[#b99a61] hover:bg-[#f8f4eb]">Save changes</button>
                </form>
            </section>
        @empty
            <div class="rounded-xl border border-dashed border-[#14283f]/20 bg-white/50 p-8 text-center"><p class="text-sm font-semibold">No custom FAQs yet</p><p class="mt-2 text-xs text-[#68727b]">Add your first question above. Until then, the website uses its built-in default questions.</p></div>
        @endforelse
    </div>
@endsection
