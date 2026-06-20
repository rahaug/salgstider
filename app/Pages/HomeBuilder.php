<?php

namespace App\Pages;

use App\Enums\ProductType;
use Carbon\CarbonImmutable;

class HomeBuilder extends PageBuilder
{
    private const AVVIK_WINDOW_DAYS = 30;

    /** @return array<string, mixed> */
    public function build(): array
    {
        $today = CarbonImmutable::now('Europe/Oslo')->startOfDay();
        $faq = $this->faq();

        return [
            'description' => 'Enkel oversikt over salgstider for øl i butikk og Vinmonopolet før røde dager, og resten av året.',
            'today' => $this->today($today),
            'nextAvvik' => $this->nextAvvik($today),
            'months' => $this->months($today),
            'faq' => $faq,
            'schema' => $this->schema($faq),
        ];
    }

    /** @return array{label: string, beer: array<string, mixed>, wine: array<string, mixed>} */
    private function today(CarbonImmutable $today): array
    {
        return [
            'label' => ucfirst($today->locale('nb')->isoFormat('dddd D. MMMM')),
            'beer' => $this->status($today, ProductType::Beer),
            'wine' => $this->status($today, ProductType::Wine),
        ];
    }

    /** @return array{open: bool, range: ?string, next: ?array{day: string, opens: string}} */
    private function status(CarbonImmutable $today, ProductType $type): array
    {
        $window = $this->hours->on($today, $type);

        return [
            'open' => $window->open,
            'range' => $window->range(),
            'next' => $window->open ? null : $this->nextOpen($today, $type),
        ];
    }

    /** @return ?array{day: string, opens: string} */
    private function nextOpen(CarbonImmutable $today, ProductType $type): ?array
    {
        $cursor = $today->addDay();

        for ($i = 0; $i < 14; $i++) {
            $window = $this->hours->on($cursor, $type);

            if ($window->open) {
                return [
                    'day' => $cursor->isSameDay($today->addDay()) ? 'i morgen' : $cursor->locale('nb')->dayName,
                    'opens' => str_ends_with($window->opens, ':00') ? substr($window->opens, 0, 2) : $window->opens,
                ];
            }

            $cursor = $cursor->addDay();
        }

        return null;
    }

    /**
     * The next real red day (a named holiday that closes beer). Always resolves to
     * the next one within the year; `near` marks whether it falls inside the action
     * window, in which case the binding buy-by deadline is included.
     *
     * @return ?array{slug: string, name: string, date: string, near: bool, deadline: ?string}
     */
    private function nextAvvik(CarbonImmutable $today): ?array
    {
        $date = $this->nextRedDay($today);

        if ($date === null) {
            return null;
        }

        $near = $date->lessThanOrEqualTo($today->addDays(self::AVVIK_WINDOW_DAYS));
        $deadline = null;

        if ($near) {
            $beerLast = $this->hours->previousOpen($date, ProductType::Beer);
            $wineLast = $this->hours->previousOpen($date, ProductType::Wine);
            $binding = $beerLast->date->lessThan($wineLast->date) ? $beerLast->date : $wineLast->date;
            $deadline = lcfirst($this->fullDate($binding));
        }

        return [
            'slug' => (new DateSlug($date->day, $date->month))->toString(),
            'name' => ucfirst($this->classifier->name($date) ?? $date->locale('nb')->dayName),
            'date' => $date->locale('nb')->isoFormat('D. MMMM'),
            'near' => $near,
            'deadline' => $deadline,
        ];
    }

    private function nextRedDay(CarbonImmutable $today): ?CarbonImmutable
    {
        $cursor = $today->addDay();

        for ($i = 0; $i < 400; $i++, $cursor = $cursor->addDay()) {
            if ($this->classifier->isNamedHoliday($cursor) && ! $this->hours->on($cursor, ProductType::Beer)->open) {
                return $cursor;
            }
        }

        return null;
    }

    /** @return list<array{slug: string, name: string, avvik: int, current: bool}> */
    private function months(CarbonImmutable $today): array
    {
        return array_map(function (int $month) use ($today) {
            $year = $today->month > $month ? $today->year + 1 : $today->year;
            $start = CarbonImmutable::create($year, $month, 1, 0, 0, 0, 'Europe/Oslo');

            $avvik = 0;

            for ($day = $start; $day->month === $month; $day = $day->addDay()) {
                if ($this->classifier->isNamedHoliday($day)) {
                    $avvik++;
                }
            }

            return [
                'slug' => MonthBuilder::MONTHS[$month],
                'name' => ucfirst(MonthBuilder::MONTHS[$month]),
                'avvik' => $avvik,
                'current' => $month === $today->month,
            ];
        }, range(1, 12));
    }

    /** @return list<array{q: string, a: string}> */
    private function faq(): array
    {
        return [
            [
                'q' => 'Når er det ølsalg i butikk?',
                'a' => 'Øl og annen alkoholholdig drikke under 4,7 % selges i butikk til 20:00 på hverdager og 18:00 på lørdager. På søndager, helligdager, 1. mai og 17. mai er det ikke ølsalg.',
            ],
            [
                'q' => 'Når er Vinmonopolet åpent?',
                'a' => 'Vinmonopolet har normalt åpent 10:00–18:00 på hverdager og 10:00–16:00 på lørdager. På søndager, helligdager og julaften er det stengt.',
            ],
            [
                'q' => 'Hvorfor er reglene for alkoholsalg så strenge i Norge?',
                'a' => 'Alkoholsalg er strengt regulert ved lov. Øl kjøper du i vanlige butikker innenfor faste tider, mens vin og sterkere varer kun selges på Vinmonopolet.',
            ],
            [
                'q' => 'Hvor gammel må man være for å kjøpe alkohol?',
                'a' => 'Aldersgrensen er 18 år for øl og vin, og 20 år for sterkere drikke med 22 % alkohol eller mer. Husk legitimasjon – både butikker og Vinmonopolet sjekker alder.',
            ],
        ];
    }
}
