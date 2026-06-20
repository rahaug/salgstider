@props(['items'])

<section class="mt-8">
    <h2 class="text-xs font-semibold uppercase tracking-[0.18em] text-zinc-500">Spørsmål og svar</h2>
    <div class="mt-4 space-y-4">
        @foreach ($items as $item)
            <div>
                <h3 class="text-lg font-semibold text-zinc-900 dark:text-white">{{ $item['q'] }}</h3>
                <p class="mt-1 leading-relaxed text-zinc-600 dark:text-zinc-300">{{ $item['a'] }}</p>
            </div>
        @endforeach
    </div>
</section>
