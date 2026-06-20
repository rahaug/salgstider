@props(['rows'])

<table {{ $attributes->merge(['class' => 'mt-4 w-full text-base']) }}>
    <thead>
        <tr class="text-xs font-semibold uppercase tracking-wider text-zinc-500">
            <th scope="col" class="border-b border-zinc-200 pb-2.5 text-left dark:border-zinc-800">Rød dag</th>
            <th scope="col" class="border-b border-zinc-200 pb-2.5 text-right dark:border-zinc-800">Siste frist</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($rows as $row)
            <tr>
                <td class="border-b border-zinc-100 py-2.5 dark:border-zinc-900">
                    <a href="/{{ $row['slug'] }}" class="font-medium text-zinc-900 transition hover:text-amber-700 dark:text-zinc-100 dark:hover:text-amber-400">{{ $row['name'] }}</a>
                    <span class="block text-sm text-zinc-500 dark:text-zinc-400">{{ $row['date'] }}</span>
                </td>
                <td class="border-b border-zinc-100 py-2.5 text-right dark:border-zinc-900">
                    <span class="block font-semibold text-zinc-900 dark:text-zinc-100">{{ $row['deadline']['date'] }}</span>
                    <span class="block text-sm tabular-nums text-zinc-500 dark:text-zinc-400">øl {{ $row['deadline']['beer'] }} · vin {{ $row['deadline']['wine'] }}</span>
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
