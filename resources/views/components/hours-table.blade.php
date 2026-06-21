@props(['rows', 'caption' => null])

<table {{ $attributes->merge(['class' => 'mt-4 w-full text-base']) }}>
    @isset($caption)
        <caption class="sr-only">{{ $caption }}</caption>
    @endisset
    <thead>
        <tr class="text-xs font-semibold uppercase tracking-wider text-zinc-500">
            <th scope="col" class="border-b border-zinc-200 pb-2.5 text-left dark:border-zinc-800">Dag</th>
            <th scope="col" class="border-b border-zinc-200 pb-2.5 text-right dark:border-zinc-800">Øl i butikk</th>
            <th scope="col" class="border-b border-zinc-200 pb-2.5 text-right dark:border-zinc-800">Vinmonopolet</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($rows as $row)
            <tr @class(['bg-zinc-100 dark:bg-zinc-800/50' => $row['current'] ?? false])>
                <td class="border-b border-zinc-100 py-1.5 dark:border-zinc-900">
                    <a href="/{{ $row['slug'] }}" @class([
                        'font-medium transition hover:text-red-700 dark:hover:text-red-400',
                        'text-zinc-900 dark:text-zinc-100' => ! ($row['current'] ?? false),
                        'font-bold text-zinc-900 dark:text-white' => $row['current'] ?? false,
                    ])>{{ $row['name'] }}</a>
                    <time datetime="{{ $row['iso'] }}" class="block text-sm text-zinc-500 dark:text-zinc-400">{{ $row['date'] }}</time>
                </td>
                <td class="border-b border-zinc-100 py-1.5 text-right tabular-nums dark:border-zinc-900">
                    @if ($row['beer']->open)<span class="font-semibold text-zinc-900 dark:text-zinc-100">{{ $row['beer']->range() }}</span>@else<span class="text-zinc-500">Stengt</span>@endif
                </td>
                <td class="border-b border-zinc-100 py-1.5 text-right tabular-nums dark:border-zinc-900">
                    @if ($row['wine']->open)<span class="font-semibold text-zinc-900 dark:text-zinc-100">{{ $row['wine']->range() }}</span>@else<span class="text-zinc-500">Stengt</span>@endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
