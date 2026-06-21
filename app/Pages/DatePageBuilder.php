<?php

namespace App\Pages;

use App\Calendar\ClusterDay;
use App\Calendar\DayClassifier;
use App\Calendar\LastSale;
use App\Calendar\NotableDay;
use App\Calendar\NotableDays;
use App\Calendar\OpeningHours;
use App\Calendar\SalesWindow;
use App\Enums\ProductType;
use Carbon\CarbonImmutable;

class DatePageBuilder extends PageBuilder
{
    public function __construct(
        DayClassifier $classifier,
        OpeningHours $hours,
        private NotableDays $notable,
        private Themes $themes,
    ) {
        parent::__construct($classifier, $hours);
    }

    /** @return array<string, mixed> */
    public function build(DateSlug $slug): array
    {
        $today = CarbonImmutable::now('Europe/Oslo')->startOfDay();
        $date = $slug->nextOccurrence($today);

        $page = new DatePage(
            date: $date,
            label: $slug->label(),
            weekday: $date->locale('nb')->dayName,
            isToday: $date->isSameDay($today),
            beer: $this->hours->on($date, ProductType::Beer),
            wine: $this->hours->on($date, ProductType::Wine),
        );

        $holidayName = $this->classifier->namedDay($date);

        $beerLast = $page->beer->open ? null : $this->hours->previousOpen($date, ProductType::Beer);
        $wineLast = $page->wine->open ? null : $this->hours->previousOpen($date, ProductType::Wine);
        $beerDeadline = $page->beer->open ? $this->hours->deadlineBefore($date, ProductType::Beer) : null;
        $wineDeadline = $page->wine->open ? $this->hours->deadlineBefore($date, ProductType::Wine) : null;

        $faq = $this->faq($page, $holidayName, $beerLast, $wineLast, $beerDeadline, $wineDeadline);
        $season = $this->themes->clusterFor($date);

        return [
            'titleSubject' => $holidayName ? "{$holidayName} {$page->label}" : $page->label,
            'eyebrow' => ucfirst($holidayName ?? $page->weekday)." {$page->label} {$page->date->year}",
            'heading' => $holidayName ?? $page->label,
            'year' => $page->date->year,
            'month' => MonthBuilder::MONTHS[$date->month],
            'nav' => $this->nav($date),
            'lede' => $this->lede($page, $holidayName, $beerLast, $wineLast, $beerDeadline, $wineDeadline),
            'hero' => $this->hero($page, $holidayName, $beerLast, $wineLast, $beerDeadline, $wineDeadline),
            'canonical' => url('/'.$slug->toString()),
            'description' => "Når stenger ølsalget {$page->label}? Åpningstider for øl i butikk og Vinmonopolet {$page->label} {$page->date->year}.",
            'faq' => $faq,
            'schema' => $this->schema($faq),
            'cluster' => $season === null ? null : [
                'slug' => $season['theme']['slug'],
                'name' => $season['theme']['name'],
                'rows' => array_map(fn (ClusterDay $d) => $this->row($d->date, $d->date->isSameDay($date)), $season['days']),
            ],
            'storeClosingNote' => $this->classifier->isStoreClosingEve($date)
                || ($season !== null && array_filter($season['days'], fn (ClusterDay $d) => $this->classifier->isStoreClosingEve($d->date)) !== []),
            'upcoming' => $season === null ? array_map(fn (NotableDay $d) => $this->row($d->date), $this->notable->upcoming($today)) : [],
        ];
    }

    /** @return array{prev: array{slug: string, name: string}, next: array{slug: string, name: string}} */
    private function nav(CarbonImmutable $date): array
    {
        return [
            'prev' => $this->navItem($date->subDay()),
            'next' => $this->navItem($date->addDay()),
        ];
    }

    /** @return array{slug: string, name: string} */
    private function navItem(CarbonImmutable $date): array
    {
        $slug = new DateSlug($date->day, $date->month);

        return ['slug' => $slug->toString(), 'name' => $slug->label()];
    }

    private function hero(DatePage $page, ?string $holidayName, ?LastSale $beerLast, ?LastSale $wineLast, ?string $beerDeadline, ?string $wineDeadline): array
    {
        $deadline = $beerDeadline ?? $wineDeadline;
        $pageOpen = $page->beer->open || $page->wine->open;

        if ($pageOpen && $deadline === null) {
            return [
                'mode' => 'open',
                'shared' => true,
                'date' => ucfirst($page->weekday).' '.$page->label,
                'beer' => $this->heroProduct($page, ProductType::Beer, $page->beer, $beerLast),
                'wine' => $this->heroProduct($page, ProductType::Wine, $page->wine, $wineLast),
            ];
        }

        $beer = $this->heroProduct($page, ProductType::Beer, $page->beer, $beerLast);
        $wine = $this->heroProduct($page, ProductType::Wine, $page->wine, $wineLast);

        return [
            'mode' => $pageOpen ? 'deadline' : 'closed',
            'occasion' => $pageOpen ? $deadline : ($holidayName ?? $page->weekday),
            'heading' => ucfirst($holidayName ?? $page->weekday).' · '.$page->label,
            'shared' => $beer['iso'] === $wine['iso'],
            'date' => $beer['date'],
            'beer' => $beer,
            'wine' => $wine,
        ];
    }

    /** @return array{date: string, shortDate: string, iso: string, range: string, open: bool} */
    private function heroProduct(DatePage $page, ProductType $type, SalesWindow $window, ?LastSale $last): array
    {
        if ($window->open) {
            return [
                'date' => $this->fullDate($page->date),
                'shortDate' => ucfirst($page->date->locale('nb')->isoFormat('dddd D. MMM')),
                'iso' => $page->date->toDateString(),
                'range' => $window->range() ?? 'Stengt',
                'open' => true,
            ];
        }

        $date = $last?->date ?? $page->date;
        $on = $this->hours->on($date, $type);

        return [
            'date' => $this->fullDate($date),
            'shortDate' => ucfirst($date->locale('nb')->isoFormat('dddd D. MMM')),
            'iso' => $date->toDateString(),
            'range' => $on->range() ?? 'Stengt',
            'open' => $on->open,
        ];
    }

    private function lede(DatePage $page, ?string $holidayName, ?LastSale $beerLast, ?LastSale $wineLast, ?string $beerDeadline, ?string $wineDeadline): string
    {
        $day = ucfirst($holidayName ?? $page->weekday)." {$page->label} {$page->date->year}";

        $beer = $page->beer->open
            ? "kan du kjøpe øl i butikk til {$page->beer->closes}"
            : ($beerLast ? "er det ikke ølsalg i butikk – siste salg er {$beerLast->closes} {$this->fullDate($beerLast->date)}" : 'er det ikke ølsalg i butikk');

        $wine = $page->wine->open
            ? "Vinmonopolet har åpent til {$page->wine->closes}"
            : ($wineLast ? "Vinmonopolet er stengt – siste salg er {$wineLast->closes} {$this->fullDate($wineLast->date)}" : 'Vinmonopolet er stengt');

        $deadline = $beerDeadline ?? $wineDeadline;
        $note = $deadline ? " Dette er siste sjanse til å handle før {$deadline}." : '';

        return "{$day} {$beer}. {$wine}.{$note}";
    }

    /** @return list<array{q: string, a: string}> */
    private function faq(DatePage $page, ?string $holidayName, ?LastSale $beerLast, ?LastSale $wineLast, ?string $beerDeadline, ?string $wineDeadline): array
    {
        $year = $page->date->year;

        return [
            [
                'q' => "Er det ølsalg {$page->label} {$year}?",
                'a' => $page->beer->open
                    ? "Ja, du kan kjøpe øl i butikk til {$page->beer->closes} {$page->label} {$year}."
                        .($beerDeadline ? " Dette er siste salg før {$beerDeadline}." : '')
                    : "Nei, det er ikke ølsalg {$page->label} {$year} ({$page->beer->reason})."
                        .($beerLast ? " Siste salg er {$beerLast->closes} {$this->fullDate($beerLast->date)}." : ''),
            ],
            [
                'q' => "Er Vinmonopolet åpent {$page->label} {$year}?",
                'a' => $page->wine->open
                    ? "Ja, Vinmonopolet har åpent til {$page->wine->closes} {$page->label} {$year}."
                        .($wineDeadline ? " Dette er siste salg før {$wineDeadline}." : '')
                    : "Nei, Vinmonopolet er stengt {$page->label} {$year} ({$page->wine->reason})."
                        .($wineLast ? " Du må handle innen {$wineLast->closes} {$this->fullDate($wineLast->date)}." : ''),
            ],
        ];
    }
}
