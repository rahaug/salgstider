@props(['rows'])

<table {{ $attributes->merge(['class' => 'mt-4 w-full text-base']) }}>
    <thead>
        <tr class="text-xs font-semibold uppercase tracking-wider text-zinc-500">
            <th scope="col" class="border-b border-zinc-200 pb-2.5 pr-3 pt-2.5 text-left dark:border-zinc-800">Rød dag</th>
            <th scope="col" class="rounded-t-lg border-b border-zinc-200 bg-zinc-50 px-3 pb-2.5 pt-2.5 text-left dark:border-zinc-800 dark:bg-zinc-900">Siste frist</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($rows as $row)
            <tr>
                <td class="py-2.5 pr-3 align-top w-1/2">
                    <a href="/{{ $row['slug'] }}" class="font-medium text-zinc-900 transition hover:text-red-700 dark:text-zinc-100 dark:hover:text-red-400">{{ $row['name'] }}</a>
                    <span class="block text-sm text-zinc-500 dark:text-zinc-400">{{ $row['date'] }}</span>
                </td>
                <td class="bg-zinc-50 py-2.5 w-1/2 align-top dark:bg-zinc-900 px-3 @if ($loop->last) rounded-b-lg @endif">
                    @if ($row['deadline']['shared'])
                        <span class="block font-medium text-zinc-900 dark:text-zinc-100">{{ $row['deadline']['date'] }}</span>
                        <span class="mt-0.5 flex flex-wrap gap-x-4 gap-y-0.5 text-sm tabular-nums text-zinc-500 dark:text-zinc-400">
                            <span class="flex gap-2 whitespace-nowrap">
                                <span>Øl</span>
                                <span>{{ $row['deadline']['beer']['range'] }}</span>
                            </span>
                            <span class="flex gap-2 whitespace-nowrap">
                                <span>Vin</span>
                                <span>{{ $row['deadline']['wine']['range'] }}</span>
                            </span>
                        </span>
                    @else
                        <span class="flex items-baseline justify-between gap-3 text-sm">
                            <span class="font-medium text-zinc-600 dark:text-zinc-300">Øl</span>
                            <span class="whitespace-nowrap text-zinc-500 dark:text-zinc-400"><span class="font-medium text-zinc-900 dark:text-zinc-100">{{ $row['deadline']['beer']['date'] }}</span> · <span class="tabular-nums">{{ $row['deadline']['beer']['range'] }}</span></span>
                        </span>
                        <span class="mt-1 flex items-baseline justify-between gap-3 text-sm">
                            <span class="font-medium text-zinc-600 dark:text-zinc-300">Vin</span>
                            <span class="whitespace-nowrap text-zinc-500 dark:text-zinc-400"><span class="font-medium text-zinc-900 dark:text-zinc-100">{{ $row['deadline']['wine']['date'] }}</span> · <span class="tabular-nums">{{ $row['deadline']['wine']['range'] }}</span></span>
                        </span>
                    @endif
                </td>
            </tr>
        @endforeach
    </tbody>
</table>
