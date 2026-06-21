@extends('layouts.app')

@section('title', 'Salgstider for ølsalg og Vinmonopolet før røde dager')
@section('description', $description)
@section('canonical', url('/'))

@push('head')
    <script type="application/ld+json">{!! $schema !!}</script>
@endpush

@section('content')
    <h1 class="relative text-3xl font-bold leading-tight tracking-tight text-zinc-900 sm:text-4xl dark:text-white">
        <span data-typed-sizer aria-hidden="true">Når stenger ølsalget og Vinmonopolet før røde dager?</span>
        <span data-typed aria-hidden="true" class="absolute inset-0"></span>
        <span class="sr-only">Når stenger ølsalget og Vinmonopolet før røde dager, og er det åpent i dag?</span>
    </h1>
    <p class="mt-4 max-w-prose text-lg leading-relaxed text-zinc-600 dark:text-zinc-300">
        Enkel oversikt over salgstider for øl i butikk og Vinmonopolet før røde dager, og resten av året.
    </p>

    <x-today-card class="mt-4" />

    <section class="mt-8">
        <h2 class="text-xs font-semibold uppercase tracking-[0.18em] text-zinc-500">Røde dager måned for måned</h2>
        <ul class="mt-4 grid grid-flow-col grid-rows-6 gap-x-4 sm:grid-rows-4">
            @foreach ($months as $month)
                <li>
                    <a href="/{{ $month['slug'] }}" @class([
                        'flex items-baseline justify-between gap-2 border-b border-zinc-100 py-2.5 transition hover:text-red-700 dark:border-zinc-900 dark:hover:text-red-400',
                        'text-zinc-700 dark:text-zinc-300' => ! $month['current'],
                        'font-semibold text-zinc-900 dark:text-white' => $month['current'],
                    ])>
                        <span>{{ $month['name'] }}</span>
                        @if ($month['avvik'] > 0)
                            <span class="text-sm font-medium tabular-nums text-red-700 dark:text-red-400">{{ $month['avvik'] }}</span>
                        @else
                            <span class="text-xs font-normal text-zinc-400 dark:text-zinc-600">ingen</span>
                        @endif
                    </a>
                </li>
            @endforeach
        </ul>
    </section>

    <x-faq :items="$faq" />
@endsection
