<?php

namespace App\Calendar\Rules;

use App\Enums\Band;
use App\Enums\ProductType;
use Carbon\CarbonImmutable;

final class NormalDay implements SalesRule
{
    public function appliesTo(CarbonImmutable $date, ProductType $type): bool
    {
        return true;
    }

    public function band(CarbonImmutable $date): Band
    {
        return Band::Normal;
    }

    public function label(CarbonImmutable $date): ?string
    {
        return null;
    }
}
