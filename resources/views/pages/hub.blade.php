@extends('layouts.app')

@section('title', 'Ølsalg i '.$theme['name'].' '.$year.' – åpningstider for øl og Vinmonopolet')
@section('description', $description)
@section('canonical', $canonical)

@push('head')
    <script type="application/ld+json">{!! $schema !!}</script>
@endpush

@section('content')
    <h1 class="text-3xl font-bold leading-tight tracking-tight text-zinc-900 sm:text-4xl dark:text-white">Åpningstider for ølsalg og Vinmonopolet i {{ $theme['name'] }} {{ $year }}</h1>
    <p class="mt-2 max-w-prose leading-relaxed text-zinc-600 dark:text-zinc-300">{{ $theme['intro'] }}</p>

    <x-hero :hero="$hero" />

    <section class="mt-6">
        <h2 class="text-xs font-semibold uppercase tracking-[0.18em] text-zinc-700 dark:text-zinc-300">Åpningstider dag for dag</h2>
        <x-hours-table :rows="$timeline" :caption="'Åpningstider for øl i butikk og Vinmonopolet i '.$theme['name'].' '.$year" />
        @include('partials.caveat')
        @if ($storeClosingNote)
            @include('partials.store-closing-note')
        @endif
    </section>

    <a href="/{{ $month }}" class="group mt-6 inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.18em] text-zinc-700 transition hover:text-red-700 dark:text-zinc-300 dark:hover:text-red-400">
        <span class="inline-block h-1.5 w-1.5 rounded-full bg-red-500"></span>
        Se alle røde dager i {{ $month }}
        <span aria-hidden="true" class="transition group-hover:translate-x-0.5">→</span>
    </a>

    <x-faq :items="$faq" />
@endsection
