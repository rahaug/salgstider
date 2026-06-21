<?php

namespace App\Pages;

use App\Calendar\ClusterDay;
use App\Calendar\DayClassifier;
use App\Calendar\OpeningHours;
use App\Enums\ProductType;
use Carbon\CarbonImmutable;

class Themes
{
    public function __construct(
        private DayClassifier $classifier,
        private OpeningHours $hours,
    ) {}

    /** @return ?array<string, mixed> */
    public function find(string $slug): ?array
    {
        $theme = config("themes.{$slug}");

        return $theme === null ? null : ['slug' => $slug, ...$theme];
    }

    /**
     * @param  array<string, mixed>  $theme
     * @return array{year: int, days: list<ClusterDay>, start: CarbonImmutable, last: CarbonImmutable}
     */
    public function cluster(array $theme, CarbonImmutable $today): array
    {
        return $this->buildCluster($theme, $this->upcomingYear($theme, $today));
    }

    /**
     * Find the season cluster (if any) that the given date falls inside.
     *
     * @return ?array{theme: array<string, mixed>, year: int, days: list<ClusterDay>, start: CarbonImmutable, last: CarbonImmutable}
     */
    public function clusterFor(CarbonImmutable $date): ?array
    {
        foreach (array_keys(config('themes')) as $slug) {
            $theme = $this->find($slug);

            if (($theme['type'] ?? 'cluster') !== 'cluster') {
                continue;
            }

            $built = $this->buildCluster($theme, $date->year);

            if ($date >= $built['start'] && $date <= $built['last']) {
                return ['theme' => $theme, ...$built];
            }
        }

        return null;
    }

    /**
     * @param  array<string, mixed>  $theme
     * @return array{year: int, days: list<ClusterDay>, first: CarbonImmutable, start: CarbonImmutable, last: CarbonImmutable}
     */
    private function buildCluster(array $theme, int $year): array
    {
        $first = $this->classifier->dateOf($year, $theme['first']);
        $last = $this->classifier->dateOf($year, $theme['last']);

        $beerStart = $this->hours->previousOpen($first, ProductType::Beer)?->date ?? $first->subDay();
        $wineStart = $this->hours->previousOpen($first, ProductType::Wine)?->date ?? $first->subDay();
        $start = $beerStart->lessThan($wineStart) ? $beerStart : $wineStart;

        $days = [];

        for ($day = $start; $day <= $last; $day = $day->addDay()) {
            $days[] = new ClusterDay(
                date: $day,
                beer: $this->hours->on($day, ProductType::Beer),
                wine: $this->hours->on($day, ProductType::Wine),
            );
        }

        return ['year' => $year, 'days' => $days, 'first' => $first, 'start' => $start, 'last' => $last];
    }

    /** @param array<string, mixed> $theme */
    public function lastChanged(array $theme, CarbonImmutable $today): CarbonImmutable
    {
        $year = $this->upcomingYear($theme, $today);

        return $this->classifier->dateOf($year - 1, $theme['last'])->addDay();
    }

    /** @param array<string, mixed> $theme */
    private function upcomingYear(array $theme, CarbonImmutable $today): int
    {
        $year = $today->year;

        while (true) {
            $last = $this->classifier->dateOf($year, $theme['last']);

            if ($last !== null && $last >= $today) {
                return $year;
            }

            $year++;
        }
    }
}
