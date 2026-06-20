@extends('layouts.app')

@section('title', 'Ølsalg i '.$name.' '.$year.' – åpningstider øl og Vinmonopolet')
@section('description', $description)
@section('canonical', $canonical)

@push('head')
    <script type="application/ld+json">{!! $schema !!}</script>
@endpush

@section('content')
    <h1 class="text-2xl font-bold leading-tight tracking-tight text-zinc-900 sm:text-3xl dark:text-white">Åpningstider for ølsalg og Vinmonopolet i {{ $heading }} {{ $year }}</h1>

    @if ($state === 'empty')
        <p class="mt-2 max-w-prose leading-relaxed text-zinc-600 dark:text-zinc-300">Det er ingen avvik for ølsalg og Vinmonopolet i {{ $name }} {{ $year }}.</p>

        <section class="mt-6">
            <h2 class="text-xs font-semibold uppercase tracking-[0.18em] text-zinc-500">Vanlige åpningstider</h2>
            <x-week-table :rows="$week" :caption="'Vanlige åpningstider for øl i butikk og Vinmonopolet i '.$name" />
            @include('partials.caveat')
        </section>
    @else
        <p class="mt-2 max-w-prose leading-relaxed text-zinc-600 dark:text-zinc-300">{{ $intro }}</p>

        <x-hero :hero="$hero" />

        <section class="mt-6">
            <h2 class="text-xs font-semibold uppercase tracking-[0.18em] text-zinc-500">Røde dager i {{ $name }}</h2>
            <x-deadline-table :rows="$rows" />
            @include('partials.caveat')
            @if ($storeClosingNote)
                @include('partials.store-closing-note')
            @endif
        </section>
    @endif

    <x-faq :items="$faq" />
@endsection
