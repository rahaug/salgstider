<?php

namespace App\Pages;

use App\Calendar\SalesWindow;
use Carbon\CarbonImmutable;

final readonly class DatePage
{
    public function __construct(
        public CarbonImmutable $date,
        public string $label,
        public string $weekday,
        public bool $isToday,
        public SalesWindow $beer,
        public SalesWindow $wine,
    ) {}
}
