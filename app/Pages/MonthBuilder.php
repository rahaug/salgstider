<?php

namespace App\Pages;

use App\Calendar\SalesWindow;
use App\Enums\ProductType;
use Carbon\CarbonImmutable;

class MonthBuilder extends PageBuilder
{
    public const MONTHS = [
        1 => 'januar', 2 => 'februar', 3 => 'mars', 4 => 'april',
        5 => 'mai', 6 => 'juni', 7 => 'juli', 8 => 'august',
        9 => 'september', 10 => 'oktober', 11 => 'november', 12 => 'desember',
    ];

    public function resolve(string $slug): ?int
    {
        $month = array_search($slug, self::MONTHS, true);

        return $month === false ? null : $month;
    }

    /** @return array<string, mixed> */
    public function build(int $month): array
    {
        $today = CarbonImmutable::now('Europe/Oslo')->startOfDay();
        $year = $today->month > $month ? $today->year + 1 : $today->year;
        $name = self::MONTHS[$month];

        $start = CarbonImmutable::create($year, $month, 1, 0, 0, 0, 'Europe/Oslo');
        $end = $start->endOfMonth();

        $redDays = [];
        $storeClosingNote = false;

        for ($day = $start; $day <= $end; $day = $day->addDay()) {
            if ($this->classifier->isStoreClosingEve($day)) {
                $storeClosingNote = true;
            }

            if ($this->classifier->isNamedHoliday($day)) {
                $redDays[] = $day;
            }
        }

        $heading = ucfirst($name);

        if ($redDays === []) {
            $faq = [[
                'q' => "Er det ølsalg i {$name} {$year}?",
                'a' => "Ja, det er vanlige åpningstider hele {$name} – ingen røde dager. Øl i butikk selges til 20.00 på hverdager og 18.00 på lørdager. Søndag er det stengt.",
            ]];

            return [
                'name' => $name,
                'heading' => $heading,
                'year' => $year,
                'state' => 'empty',
                'canonical' => url('/'.$name),
                'description' => "Det er ingen røde dager i {$name} {$year} – vanlige åpningstider for ølsalg i butikk og Vinmonopolet hele måneden.",
                'week' => $this->week($start),
                'faq' => $faq,
                'schema' => $this->schema($faq),
            ];
        }

        $first = $redDays[0];
        $beerLast = $this->hours->previousOpen($first, ProductType::Beer);
        $wineLast = $this->hours->previousOpen($first, ProductType::Wine);

        $faq = [
            [
                'q' => "Når må jeg kjøpe alkohol før de røde dagene i {$name} {$year}?",
                'a' => 'Hver røde dag har sin egen frist – se oversikten over. Siste salg er dagen før, eller før helgen når den røde dagen faller på en mandag.',
            ],
            [
                'q' => "Er Vinmonopolet åpent på røde dager i {$name} {$year}?",
                'a' => 'Nei, Vinmonopolet er stengt på røde dager. Handle dagen før.',
            ],
        ];

        return [
            'name' => $name,
            'heading' => $heading,
            'year' => $year,
            'state' => 'avvik',
            'canonical' => url('/'.$name),
            'description' => "Røde dager og salgstider for øl og Vinmonopolet i {$name} {$year}.",
            'intro' => "Her ser du de røde dagene i {$name} {$year} og når du må handle før hver av dem.",
            'storeClosingNote' => $storeClosingNote,
            'hero' => [
                'mode' => 'deadline',
                'occasion' => ucfirst($this->classifier->name($first) ?? $first->locale('nb')->dayName),
                'shared' => $beerLast->date->isSameDay($wineLast->date),
                'date' => $this->fullDate($beerLast->date),
                'beer' => ['date' => $this->fullDate($beerLast->date), 'range' => $this->hours->on($beerLast->date, ProductType::Beer)->range()],
                'wine' => ['date' => $this->fullDate($wineLast->date), 'range' => $this->hours->on($wineLast->date, ProductType::Wine)->range()],
            ],
            'rows' => array_map(fn (CarbonImmutable $day) => $this->deadlineRow($day), $redDays),
            'faq' => $faq,
            'schema' => $this->schema($faq),
        ];
    }

    /** @return array{slug: string, name: string, date: string, deadline: array{date: string, beer: ?string, wine: ?string}} */
    private function deadlineRow(CarbonImmutable $redDay): array
    {
        $beerLast = $this->hours->previousOpen($redDay, ProductType::Beer);
        $wineLast = $this->hours->previousOpen($redDay, ProductType::Wine);

        return [
            'slug' => (new DateSlug($redDay->day, $redDay->month))->toString(),
            'name' => ucfirst($this->classifier->name($redDay) ?? $redDay->locale('nb')->dayName),
            'date' => $this->fullDate($redDay),
            'deadline' => [
                'date' => $this->fullDate($beerLast->date),
                'beer' => $this->hours->on($beerLast->date, ProductType::Beer)->range(),
                'wine' => $this->hours->on($wineLast->date, ProductType::Wine)->range(),
            ],
        ];
    }

    /**
     * A typical Mon–Sun week (no dates) — the month has no red days, so any week is normal.
     *
     * @return list<array{name: string, beer: SalesWindow, wine: SalesWindow}>
     */
    private function week(CarbonImmutable $monthStart): array
    {
        $monday = $monthStart;

        while ($monday->dayOfWeekIso !== 1) {
            $monday = $monday->addDay();
        }

        $rows = [];

        for ($i = 0; $i < 7; $i++) {
            $day = $monday->addDays($i);
            $rows[] = [
                'name' => ucfirst($day->locale('nb')->dayName),
                'beer' => $this->hours->on($day, ProductType::Beer),
                'wine' => $this->hours->on($day, ProductType::Wine),
            ];
        }

        return $rows;
    }
}
