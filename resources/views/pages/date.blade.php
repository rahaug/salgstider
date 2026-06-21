@extends('layouts.app')

@section('title', 'Ølsalg '.$titleSubject.' '.$year.' – åpningstider øl og Vinmonopolet')
@section('description', $description)
@section('canonical', $canonical)

@push('head')
    <script type="application/ld+json">{!! $schema !!}</script>
@endpush

@section('content')
    <p class="text-base font-medium text-zinc-700 dark:text-zinc-300">{{ $eyebrow }}</p>
    <h1 class="mt-1.5 text-3xl font-bold leading-tight tracking-tight text-zinc-900 sm:text-4xl dark:text-white">Åpningstider for ølsalg og Vinmonopolet {{ $heading }} {{ $year }}</h1>
    <p class="mt-3 text-lg leading-relaxed text-zinc-700 dark:text-zinc-300">{{ $lede }}</p>

    <x-hero :hero="$hero" />

    @include('partials.caveat')
    @if ($storeClosingNote)
        @include('partials.store-closing-note')
    @endif

    <a href="/{{ $month }}" class="group mt-6 inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.18em] text-zinc-700 transition hover:text-red-700 dark:text-zinc-300 dark:hover:text-red-400">
        <span class="inline-block h-1.5 w-1.5 rounded-full bg-red-500"></span>
        Se alle røde dager i {{ $month }}
        <span aria-hidden="true" class="transition group-hover:translate-x-0.5">→</span>
    </a>

    @if ($cluster)
        <section class="mt-8">
            <a href="/{{ $cluster['slug'] }}" class="group inline-flex items-center gap-1.5 text-xs font-semibold uppercase tracking-[0.18em] text-zinc-500 transition hover:text-red-700 dark:hover:text-red-400">
                Åpningstider i {{ $cluster['name'] }}
                <span aria-hidden="true" class="transition group-hover:translate-x-0.5">→</span>
            </a>
            <x-hours-table :rows="$cluster['rows']" :caption="'Åpningstider for øl i butikk og Vinmonopolet i '.$cluster['name']" />
        </section>
    @elseif (! empty($upcoming))
        <section class="mt-8">
            <h2 class="text-xs font-semibold uppercase tracking-[0.18em] text-zinc-500">Spesielle dager fremover</h2>
            <x-hours-table :rows="$upcoming" caption="Spesielle salgstider for øl og Vinmonopolet fremover" />
        </section>
    @endif

    <x-faq :items="$faq" />

    <nav class="mt-10 flex items-center justify-between border-t border-zinc-200 pt-5 text-sm dark:border-zinc-800">
        <a href="/{{ $nav['prev']['slug'] }}" class="group inline-flex items-center gap-1.5 font-medium text-zinc-600 transition hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white">
            <span aria-hidden="true" class="transition group-hover:-translate-x-0.5">←</span>
            {{ $nav['prev']['name'] }}
        </a>
        <a href="/{{ $nav['next']['slug'] }}" class="group inline-flex items-center gap-1.5 font-medium text-zinc-600 transition hover:text-zinc-900 dark:text-zinc-400 dark:hover:text-white">
            {{ $nav['next']['name'] }}
            <span aria-hidden="true" class="transition group-hover:translate-x-0.5">→</span>
        </a>
    </nav>
@endsection
