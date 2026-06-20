<?php

namespace App\Pages;

use App\Calendar\DayClassifier;
use App\Calendar\OpeningHours;
use App\Calendar\SalesWindow;
use App\Enums\ProductType;
use Carbon\CarbonImmutable;

abstract class PageBuilder
{
    public function __construct(
        protected DayClassifier $classifier,
        protected OpeningHours $hours,
    ) {}

    /**
     * A single row for the shared <x-hours-table> component.
     *
     * @return array{slug: string, iso: string, name: string, date: string, beer: SalesWindow, wine: SalesWindow, current: bool}
     */
    protected function row(CarbonImmutable $date, bool $current = false): array
    {
        return [
            'slug' => (new DateSlug($date->day, $date->month))->toString(),
            'iso' => $date->toDateString(),
            'name' => ucfirst($this->classifier->name($date) ?? $this->classifier->eveName($date) ?? $date->locale('nb')->dayName),
            'date' => $date->locale('nb')->isoFormat('D. MMMM'),
            'beer' => $this->hours->on($date, ProductType::Beer),
            'wine' => $this->hours->on($date, ProductType::Wine),
            'current' => $current,
        ];
    }

    protected function fullDate(CarbonImmutable $date): string
    {
        return ucfirst($date->locale('nb')->isoFormat('dddd D. MMMM'));
    }

    /** @param list<array{q: string, a: string}> $faq */
    protected function schema(array $faq): string
    {
        return json_encode([
            '@context' => 'https://schema.org',
            '@type' => 'FAQPage',
            'mainEntity' => array_map(fn (array $item) => [
                '@type' => 'Question',
                'name' => $item['q'],
                'acceptedAnswer' => ['@type' => 'Answer', 'text' => $item['a']],
            ], $faq),
        ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
}
