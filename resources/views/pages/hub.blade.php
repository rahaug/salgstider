@extends('layouts.app')

@section('title', 'Ølsalg i '.$theme['name'].' '.$year.' – åpningstider for øl og Vinmonopolet')
@section('description', $description)
@section('canonical', $canonical)

@push('head')
    <script type="application/ld+json">{!! $schema !!}</script>
@endpush

@section('content')
    <h1 class="text-2xl font-bold leading-tight tracking-tight text-zinc-900 sm:text-3xl dark:text-white">Åpningstider for ølsalg og Vinmonopolet i {{ $theme['name'] }} {{ $year }}</h1>
    <p class="mt-2 max-w-prose leading-relaxed text-zinc-600 dark:text-zinc-300">{{ $theme['intro'] }}</p>

    <x-hero :hero="$hero" />

    <section class="mt-6">
        <h2 class="text-xs font-semibold uppercase tracking-[0.18em] text-zinc-500">Åpningstider dag for dag</h2>
        <x-hours-table :rows="$timeline" :caption="'Åpningstider for øl i butikk og Vinmonopolet i '.$theme['name'].' '.$year" />
        @include('partials.caveat')
        @if ($storeClosingNote)
            @include('partials.store-closing-note')
        @endif
    </section>

    <a href="{{ $theme['ef']['url'] }}" class="group mt-6 flex items-center justify-between gap-3 border-b border-zinc-200 py-3 dark:border-zinc-800">
        <span class="text-zinc-600 dark:text-zinc-300">Lurer du på <span class="font-medium text-zinc-900 dark:text-white">{{ $theme['ef']['title'] }}</span></span>
        <span class="text-zinc-400 transition group-hover:translate-x-0.5 group-hover:text-zinc-600 dark:text-zinc-500">→</span>
    </a>

    <x-faq :items="$faq" />
@endsection
