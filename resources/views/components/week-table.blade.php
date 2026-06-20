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
            <tr>
                <td class="border-b border-zinc-100 py-2 font-medium text-zinc-900 dark:border-zinc-900 dark:text-zinc-100">{{ $row['name'] }}</td>
                <td class="border-b border-zinc-100 py-2 text-right tabular-nums dark:border-zinc-900">
                    @if ($row['beer']->open)<span class="font-semibold text-zinc-900 dark:text-zinc-100">{{ $row['beer']->range() }}</span>@else<span class="text-zinc-500">Stengt</span>@endif
                </td>
                <td class="border-b border-zinc-100 py-2 text-right tabular-nums dark:border-zinc-900">
                    @if ($row['wine']->open)<span class="font-semibold text-zinc-900 dark:text-zinc-100">{{ $row['wine']->range() }}</span>@else<span class="text-zinc-500">Stengt</span>@endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
