<?php

use App\Calendar\DayClassifier;
use App\Calendar\NotableDays;
use App\Calendar\OpeningHours;
use Carbon\CarbonImmutable;

beforeEach(function () {
    $classifier = new DayClassifier;
    $this->notable = new NotableDays($classifier, new OpeningHours($classifier));
});

it('lists holiday-driven days and skips ordinary weekends', function () {
    $from = CarbonImmutable::parse('2026-03-30', 'Europe/Oslo');

    $dates = array_map(
        fn ($day) => $day->date->toDateString(),
        $this->notable->upcoming($from, limit: 5),
    );

    expect($dates)->toBe([
        '2026-04-01', // onsdag før skjærtorsdag (weekday reduced eve)
        '2026-04-02', // skjærtorsdag
        '2026-04-03', // langfredag
        '2026-04-05', // 1. påskedag (sunday, but a named holiday → kept)
        '2026-04-06', // 2. påskedag
    ]);
});

it('returns nothing for a quiet stretch inside the horizon', function () {
    $from = CarbonImmutable::parse('2026-06-01', 'Europe/Oslo');

    expect($this->notable->upcoming($from, maxDays: 20))->toBe([]);
});

it('respects the limit', function () {
    $from = CarbonImmutable::parse('2026-03-30', 'Europe/Oslo');

    expect($this->notable->upcoming($from, limit: 2))->toHaveCount(2);
});
