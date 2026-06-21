@props(['hero'])

<section class="mt-6 rounded-2xl bg-zinc-900 p-5 text-white sm:p-6 dark:ring-1 dark:ring-zinc-800">
    @if ($hero['mode'] === 'closed')
        <p class="text-xs font-semibold uppercase tracking-[0.18em] text-zinc-400">{{ $hero['heading'] }}</p>
        <dl class="mt-3 space-y-1">
            <div class="flex items-baseline justify-between">
                <dt class="text-lg text-zinc-200">Øl i butikk</dt>
                <dd class="text-lg font-semibold text-red-400">Stengt</dd>
            </div>
            <div class="flex items-baseline justify-between">
                <dt class="text-lg text-zinc-200">Vinmonopolet</dt>
                <dd class="text-lg font-semibold text-red-400">Stengt</dd>
            </div>
        </dl>

        <div class="mt-5 border-t border-zinc-700/70 pt-4">
            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-zinc-400">Siste salg</p>
            <dl class="mt-2 space-y-2">
                @foreach (['Øl' => $hero['beer'], 'Vin' => $hero['wine']] as $label => $product)
                    <div class="flex items-baseline justify-between gap-4 text-lg">
                        <dt class="text-zinc-300">{{ $label }}</dt>
                        <dd class="text-right tabular-nums"><span class="font-semibold text-white">{{ $product['shortDate'] }}</span><span class="ml-4 font-semibold text-emerald-400">{{ $product['range'] }}</span></dd>
                    </div>
                @endforeach
            </dl>
        </div>
    @elseif ($hero['mode'] === 'deadline')
        <p class="text-base text-zinc-300">Kjøp alkohol til {{ $hero['occasion'] }} innen:</p>

        @if ($hero['shared'])
            <p class="mt-1 text-2xl font-bold tracking-tight text-white">{{ $hero['date'] }}</p>
            <dl class="mt-3 space-y-1">
                @foreach (['Øl i butikk' => $hero['beer'], 'Vinmonopolet' => $hero['wine']] as $label => $product)
                    <div class="flex items-baseline justify-between">
                        <dt class="text-lg text-zinc-200">{{ $label }}</dt>
                        <dd @class(['text-lg font-semibold tabular-nums', 'text-emerald-400' => $product['open'], 'text-red-400' => ! $product['open']])>{{ $product['range'] }}</dd>
                    </div>
                @endforeach
            </dl>
        @else
            <dl class="mt-3 space-y-3">
                @foreach (['Øl i butikk' => $hero['beer'], 'Vinmonopolet' => $hero['wine']] as $label => $product)
                    <div class="flex items-baseline justify-between gap-4">
                        <dt class="text-lg text-zinc-200">{{ $label }}</dt>
                        <dd class="text-right">
                            <span class="block text-lg font-bold tabular-nums text-white">{{ $product['date'] }}</span>
                            <span @class(['block text-base font-semibold tabular-nums', 'text-emerald-400' => $product['open'], 'text-red-400' => ! $product['open']])>{{ $product['range'] }}</span>
                        </dd>
                    </div>
                @endforeach
            </dl>
        @endif
    @else
        <p class="text-base font-semibold text-emerald-400">Åpent</p>
        <dl class="mt-3 space-y-1">
            @foreach (['Øl i butikk' => $hero['beer'], 'Vinmonopolet' => $hero['wine']] as $label => $product)
                <div class="flex items-baseline justify-between">
                    <dt class="text-lg text-zinc-200">{{ $label }}</dt>
                    <dd @class(['text-lg font-semibold tabular-nums', 'text-emerald-400' => $product['open'], 'text-red-400' => ! $product['open']])>{{ $product['range'] }}</dd>
                </div>
            @endforeach
        </dl>
    @endif
</section>
