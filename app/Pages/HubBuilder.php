<?php

namespace App\Pages;

use App\Calendar\ClusterDay;
use App\Calendar\DayClassifier;
use App\Calendar\OpeningHours;
use App\Enums\ProductType;
use Carbon\CarbonImmutable;

class HubBuilder extends PageBuilder
{
    public function __construct(
        DayClassifier $classifier,
        OpeningHours $hours,
        private Themes $themes,
    ) {
        parent::__construct($classifier, $hours);
    }

    /**
     * @param  array<string, mixed>  $theme
     * @return array<string, mixed>
     */
    public function build(array $theme): array
    {
        $today = CarbonImmutable::now('Europe/Oslo')->startOfDay();
        ['year' => $year, 'days' => $days, 'first' => $first] = $this->themes->cluster($theme, $today);

        $beerLast = $this->hours->previousOpen($first, ProductType::Beer);
        $wineLast = $this->hours->previousOpen($first, ProductType::Wine);
        $beerWin = $this->hours->on($beerLast->date, ProductType::Beer);
        $wineWin = $this->hours->on($wineLast->date, ProductType::Wine);
        $shared = $beerLast->date->isSameDay($wineLast->date);

        $faq = [
            [
                'q' => "Når må jeg kjøpe øl før {$theme['name']} {$year}?",
                'a' => "Siste salg av øl før {$theme['name']} er {$beerWin->closes} {$this->fullDate($beerLast->date)}.",
            ],
            [
                'q' => "Er Vinmonopolet åpent i {$theme['name']} {$year}?",
                'a' => "Vinmonopolet er stengt på helligdagene i {$theme['name']}. Siste salg er {$wineWin->closes} {$this->fullDate($wineLast->date)}.",
            ],
        ];

        return [
            'theme' => $theme,
            'year' => $year,
            'storeClosingNote' => array_filter($days, fn (ClusterDay $d) => $this->classifier->isStoreClosingEve($d->date)) !== [],
            'hero' => [
                'mode' => 'deadline',
                'occasion' => $theme['name'],
                'shared' => $shared,
                'date' => $this->fullDate($beerLast->date),
                'beer' => ['date' => $this->fullDate($beerLast->date), 'range' => $beerWin->range(), 'open' => $beerWin->open],
                'wine' => ['date' => $this->fullDate($wineLast->date), 'range' => $wineWin->range(), 'open' => $wineWin->open],
            ],
            'timeline' => array_map(fn (ClusterDay $d) => $this->row($d->date), $days),
            'faq' => $faq,
            'schema' => $this->schema($faq),
            'canonical' => url('/'.$theme['slug']),
            'description' => "Salgstider for øl og Vinmonopolet i {$theme['name']} {$year}. Se når du må handle.",
        ];
    }
}
