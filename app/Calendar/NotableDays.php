<?php

namespace App\Calendar;

use App\Enums\ProductType;
use Carbon\CarbonImmutable;

class NotableDays
{
    public function __construct(
        private DayClassifier $classifier,
        private OpeningHours $hours,
    ) {}

    /** @return list<NotableDay> */
    public function upcoming(CarbonImmutable $from, int $limit = 5, int $maxDays = 120): array
    {
        $found = [];
        $date = $from->startOfDay()->addDay();

        for ($i = 0; $i < $maxDays && count($found) < $limit; $i++, $date = $date->addDay()) {
            if (! $this->isNotable($date)) {
                continue;
            }

            $found[] = new NotableDay(
                date: $date,
                name: $this->classifier->name($date),
                beer: $this->hours->on($date, ProductType::Beer),
                wine: $this->hours->on($date, ProductType::Wine),
            );
        }

        return $found;
    }

    private function isNotable(CarbonImmutable $date): bool
    {
        if ($this->classifier->isNamedHoliday($date)) {
            return true;
        }

        if ($this->classifier->isChristmasEve($date)) {
            return true;
        }

        return $this->classifier->isReducedEve($date) && ! $date->isSaturday();
    }
}
