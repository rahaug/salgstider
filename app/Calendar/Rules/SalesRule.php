<?php

namespace App\Calendar\Rules;

use App\Enums\Band;
use App\Enums\ProductType;
use Carbon\CarbonImmutable;

interface SalesRule
{
    public function appliesTo(CarbonImmutable $date, ProductType $type): bool;

    public function band(CarbonImmutable $date): Band;

    public function label(CarbonImmutable $date): ?string;
}
