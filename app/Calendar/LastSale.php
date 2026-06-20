<?php

namespace App\Calendar;

use Carbon\CarbonImmutable;

final readonly class LastSale
{
    public function __construct(
        public CarbonImmutable $date,
        public string $closes,
    ) {}
}
