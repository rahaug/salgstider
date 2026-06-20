<?php

namespace App\Calendar;

use Carbon\CarbonImmutable;

final readonly class ClusterDay
{
    public function __construct(
        public CarbonImmutable $date,
        public SalesWindow $beer,
        public SalesWindow $wine,
    ) {}
}
