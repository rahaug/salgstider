<?php

namespace App\Pages;

use Carbon\CarbonImmutable;

class HomeBuilder extends PageBuilder
{
    /** @return array<string, mixed> */
    public function build(): array
    {
        $today = CarbonImmutable::now('Europe/Oslo')->startOfDay();
        $faq = $this->faq();

        return [
            'description' => 'Enkel oversikt over salgstider for øl i butikk og Vinmonopolet før røde dager, og resten av året.',
            'months' => $this->months($today),
            'faq' => $faq,
            'schema' => $this->schema($faq),
        ];
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
