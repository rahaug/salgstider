<?php

namespace App\Calendar;

use Carbon\CarbonImmutable;

final readonly class NotableDay
{
    public function __construct(
        public CarbonImmutable $date,
        public ?string $name,
        public SalesWindow $beer,
        public SalesWindow $wine,
    ) {}
}
