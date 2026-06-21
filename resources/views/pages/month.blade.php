@extends('layouts.app')

@section('title', 'Ølsalg i '.$name.' '.$year.' – åpningstider øl og Vinmonopolet')
@section('description', $description)
@section('canonical', $canonical)

@section('content')
    <h1 class="text-3xl font-bold leading-tight tracking-tight text-zinc-900 sm:text-4xl dark:text-white">Åpningstider for ølsalg og Vinmonopolet i {{ $heading }} {{ $year }}</h1>

    @if ($state === 'empty')
        <p class="mt-2 max-w-prose leading-relaxed text-zinc-600 dark:text-zinc-300">Det er ingen avvik for ølsalg og Vinmonopolet i {{ $name }} {{ $year }}.</p>

        <x-today-card class="mt-6" :avvik="$avvik" />

        <section class="mt-6">
            <h2 class="text-xs font-semibold uppercase tracking-[0.18em] text-zinc-500">Vanlige åpningstider</h2>
            <x-week-table :rows="$week" :caption="'Vanlige åpningstider for øl i butikk og Vinmonopolet i '.$name" />
            @include('partials.caveat')
        </section>
    @else
        <p class="mt-2 max-w-prose leading-relaxed text-zinc-600 dark:text-zinc-300">{{ $intro }}</p>

        <x-today-card class="mt-6" :avvik="$avvik" />

        <section class="mt-6">
            <h2 class="flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.18em] text-zinc-700 dark:text-zinc-300">
                <span class="inline-block h-1.5 w-1.5 rounded-full bg-red-500"></span>
                Røde dager i {{ $name }}
            </h2>
            <div class="mt-3 rounded-2xl border border-zinc-200 bg-zinc-50/70 px-5 pb-2 dark:border-zinc-800 dark:bg-zinc-900/40">
                <x-deadline-table :rows="$rows" />
            </div>
            @if ($storeClosingNote)
                @include('partials.store-closing-note')
            @endif
        </section>

        <section class="mt-8">
            <h2 class="text-xs font-semibold uppercase tracking-[0.18em] text-zinc-500">Vanlige åpningstider</h2>
            <x-week-table :rows="$week" :caption="'Vanlige åpningstider for øl i butikk og Vinmonopolet i '.$name" />
            @include('partials.caveat')
        </section>
    @endif

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
