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

    <section class="mt-4 rounded-2xl bg-zinc-900 p-5 text-white sm:p-6 dark:ring-1 dark:ring-zinc-800">
        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-zinc-400">I dag · {{ $today['label'] }}</p>
        <dl class="mt-3 space-y-1">
            @foreach (['Øl i butikk' => $today['beer'], 'Vinmonopolet' => $today['wine']] as $label => $product)
                <div class="flex items-baseline justify-between gap-4">
                    <dt class="text-lg text-zinc-200">{{ $label }}</dt>
                    <dd class="text-right">
                        @if ($product['open'])
                            <span class="text-lg font-semibold tabular-nums text-emerald-400">{{ $product['range'] }}</span>
                        @else
                            <span class="text-lg font-semibold text-zinc-400">Stengt</span>
                            @if ($product['next'])
                                <span class="block text-sm tabular-nums text-zinc-400">Åpner {{ $product['next']['day'] }} {{ $product['next']['opens'] }}</span>
                            @endif
                        @endif
                    </dd>
                </div>
            @endforeach
        </dl>

        @if ($nextAvvik && $nextAvvik['near'])
            <a href="/{{ $nextAvvik['slug'] }}" class="group mt-5 flex items-center justify-between gap-3 border-t border-zinc-700/70 pt-4">
                <span>
                    <span class="block text-xs font-semibold uppercase tracking-[0.18em] text-zinc-400">Neste røde dag</span>
                    <span class="mt-1.5 block font-semibold text-white">{{ $nextAvvik['name'] }} · {{ $nextAvvik['date'] }}</span>
                    <span class="block text-sm text-zinc-300">Kjøp innen {{ $nextAvvik['deadline'] }}</span>
                </span>
                <span class="text-zinc-500 transition group-hover:translate-x-0.5 group-hover:text-zinc-300">→</span>
            </a>
        @elseif ($nextAvvik)
            <div class="mt-5 border-t border-zinc-700/70 pt-4">
                <span class="block text-xs font-semibold uppercase tracking-[0.18em] text-zinc-400">Neste røde dag</span>
                <span class="mt-1.5 block text-sm text-zinc-300">Ingen røde dager de neste 30 dagene</span>
            </div>
        @endif
    </section>

    @if ($nextAvvik && ! $nextAvvik['near'])
        <p class="mt-3 text-sm text-zinc-500 dark:text-zinc-400">
            Neste: <a href="/{{ $nextAvvik['slug'] }}" class="font-medium text-zinc-700 underline decoration-zinc-300 underline-offset-2 transition hover:text-zinc-900 dark:text-zinc-300 dark:decoration-zinc-600 dark:hover:text-white">{{ $nextAvvik['name'] }} · {{ $nextAvvik['date'] }}</a>
        </p>
    @endif

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
