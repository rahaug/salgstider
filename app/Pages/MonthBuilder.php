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

    public function lastChanged(int $month, CarbonImmutable $today): CarbonImmutable
    {
        $year = $today->month > $month ? $today->year + 1 : $today->year;

        return CarbonImmutable::create($year - 1, $month, 1, 0, 0, 0, 'Europe/Oslo')->addMonth();
    }

    /** @return array{prev: array{slug: string, name: string}, next: array{slug: string, name: string}} */
    private function nav(int $month): array
    {
        $prev = $month === 1 ? 12 : $month - 1;
        $next = $month === 12 ? 1 : $month + 1;

        return [
            'prev' => ['slug' => self::MONTHS[$prev], 'name' => ucfirst(self::MONTHS[$prev])],
            'next' => ['slug' => self::MONTHS[$next], 'name' => ucfirst(self::MONTHS[$next])],
        ];
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
        $nav = $this->nav($month);

        if ($redDays === []) {
            return [
                'name' => $name,
                'heading' => $heading,
                'year' => $year,
                'nav' => $nav,
                'state' => 'empty',
                'canonical' => url('/'.$name),
                'description' => "Det er ingen røde dager i {$name} {$year} – vanlige åpningstider for ølsalg i butikk og Vinmonopolet hele måneden.",
                'avvik' => ['scope' => $name, 'date' => null],
                'week' => $this->week($start),
            ];
        }

        return [
            'name' => $name,
            'heading' => $heading,
            'year' => $year,
            'nav' => $nav,
            'state' => 'avvik',
            'canonical' => url('/'.$name),
            'description' => "Røde dager og salgstider for øl og Vinmonopolet i {$name} {$year}.",
            'intro' => "Her ser du åpningstider og de røde dagene i {$name} {$year} og når du må handle før hver av dem.",
            'storeClosingNote' => $storeClosingNote,
            'avvik' => ['scope' => $name, 'date' => $redDays[0]],
            'week' => $this->week($start),
            'rows' => array_map(fn (CarbonImmutable $day) => $this->deadlineRow($day), $redDays),
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
                'shared' => $beerLast->date->isSameDay($wineLast->date),
                'date' => $this->fullDate($beerLast->date),
                'beer' => ['date' => $beerLast->date->locale('nb')->isoFormat('D. MMMM'), 'range' => $this->hours->on($beerLast->date, ProductType::Beer)->range()],
                'wine' => ['date' => $wineLast->date->locale('nb')->isoFormat('D. MMMM'), 'range' => $this->hours->on($wineLast->date, ProductType::Wine)->range()],
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

        while ($this->weekHasHoliday($monday)) {
            $monday = $monday->addDays(7);
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

    private function weekHasHoliday(CarbonImmutable $monday): bool
    {
        for ($i = 0; $i < 7; $i++) {
            $day = $monday->addDays($i);

            if ($this->classifier->isNamedHoliday($day) || $this->classifier->isChristmasEve($day)) {
                return true;
            }
        }

        return false;
    }
}
