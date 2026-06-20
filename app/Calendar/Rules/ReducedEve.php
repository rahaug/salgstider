<?php

namespace App\Calendar\Rules;

use App\Calendar\DayClassifier;
use App\Enums\Band;
use App\Enums\ProductType;
use Carbon\CarbonImmutable;

final class ReducedEve implements SalesRule
{
    public function __construct(private DayClassifier $classifier) {}

    public function appliesTo(CarbonImmutable $date, ProductType $type): bool
    {
        return $this->classifier->isReducedEve($date);
    }

    public function band(CarbonImmutable $date): Band
    {
        return Band::Reduced;
    }

    public function label(CarbonImmutable $date): ?string
    {
        return null;
    }
}
