<?php

namespace App\Calendar\Rules;

use App\Calendar\DayClassifier;
use App\Enums\Band;
use App\Enums\ProductType;
use Carbon\CarbonImmutable;

final class StoreClosingEve implements SalesRule
{
    public function __construct(private DayClassifier $classifier) {}

    public function appliesTo(CarbonImmutable $date, ProductType $type): bool
    {
        return $this->classifier->isStoreClosingEve($date);
    }

    public function band(CarbonImmutable $date): Band
    {
        return Band::EarlyClose;
    }

    public function label(CarbonImmutable $date): ?string
    {
        return null;
    }
}
