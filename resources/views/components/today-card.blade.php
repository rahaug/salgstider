<div {{ $attributes->only('class') }}>
    <section class="rounded-2xl bg-zinc-900 p-5 text-white sm:p-6 dark:ring-1 dark:ring-zinc-800">
        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-zinc-400">I dag · {{ $today['label'] }}</p>
        <dl class="mt-3 space-y-1">
            @foreach (['Øl i butikk' => $today['beer'], 'Vinmonopolet' => $today['wine']] as $label => $product)
                <div class="flex items-baseline justify-between gap-4">
                    <dt class="text-lg text-zinc-200">{{ $label }}</dt>
                    <dd class="text-right">
                        @if ($product['open'])
                            <span class="text-lg font-semibold tabular-nums text-emerald-400">{{ $product['range'] }}</span>
                        @else
                            <span class="text-lg font-semibold text-red-400">Stengt</span>
                            @if ($product['next'])
                                <span class="block text-sm tabular-nums text-zinc-400">Åpner {{ $product['next']['day'] }} {{ $product['next']['opens'] }}</span>
                            @endif
                        @endif
                    </dd>
                </div>
            @endforeach
        </dl>

        @if ($card && $card['zone'] === 'action')
            <a href="/{{ $card['slug'] }}" class="group mt-5 flex items-center justify-between gap-3 border-t border-zinc-700/70 pt-4">
                <span>
                    <span class="block text-xs font-semibold uppercase tracking-[0.18em] text-zinc-400">{{ $card['label'] }}</span>
                    <span class="mt-1.5 block font-semibold text-white">{{ $card['name'] }} · {{ $card['date'] }}</span>
                    <span class="block text-sm text-zinc-300">Kjøp innen {{ $card['deadline'] }}</span>
                </span>
                <span class="text-zinc-500 transition group-hover:translate-x-0.5 group-hover:text-zinc-300">→</span>
            </a>
        @elseif ($card)
            <div class="mt-5 border-t border-zinc-700/70 pt-4">
                <span class="block text-xs font-semibold uppercase tracking-[0.18em] text-zinc-400">{{ $card['label'] }}</span>
                <span class="mt-1.5 block text-sm text-zinc-300">{{ $card['note'] }}</span>
            </div>
        @endif
    </section>

    @if ($below)
        <p class="mt-3 text-sm text-zinc-500 dark:text-zinc-400">
            Neste: <a href="/{{ $below['slug'] }}" class="font-medium text-zinc-700 underline decoration-zinc-300 underline-offset-2 transition hover:text-zinc-900 dark:text-zinc-300 dark:decoration-zinc-600 dark:hover:text-white">{{ $below['name'] }} · {{ $below['date'] }}</a>
        </p>
    @endif
</div>
