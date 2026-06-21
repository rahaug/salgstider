<?php

use App\Calendar\DayClassifier;
use Carbon\CarbonImmutable;

$name = fn (string $date): ?string => app(DayClassifier::class)
    ->namedDay(CarbonImmutable::parse($date, 'Europe/Oslo')->startOfDay());

it('names every notable day in 2026', function (string $date, string $expected) use ($name) {
    expect($name($date))->toBe($expected);
})->with([
    'nyttårsaften' => ['2026-12-31', 'Nyttårsaften'],
    'første nyttårsdag' => ['2026-01-01', 'første nyttårsdag'],
    'fastelavn' => ['2026-02-15', 'fastelavn'],
    'kvinnedagen' => ['2026-03-08', 'kvinnedagen'],
    'palmesøndag' => ['2026-03-29', 'palmesøndag'],
    'skjærtorsdag' => ['2026-04-02', 'skjærtorsdag'],
    'langfredag' => ['2026-04-03', 'langfredag'],
    'påskeaften' => ['2026-04-04', 'Påskeaften'],
    'første påskedag' => ['2026-04-05', 'første påskedag'],
    'andre påskedag' => ['2026-04-06', 'andre påskedag'],
    'arbeidernes dag' => ['2026-05-01', 'arbeidernes dag'],
    'frigjøringsdagen' => ['2026-05-08', 'frigjøringsdagen'],
    'Kristi himmelfartsdag' => ['2026-05-14', 'Kristi himmelfartsdag'],
    'grunnlovsdagen' => ['2026-05-17', 'grunnlovsdagen'],
    'pinseaften' => ['2026-05-23', 'Pinseaften'],
    'første pinsedag' => ['2026-05-24', 'første pinsedag'],
    'andre pinsedag' => ['2026-05-25', 'andre pinsedag'],
    'bots- og bededag' => ['2026-10-25', 'bots- og bededag'],
    'allehelgensdag' => ['2026-11-01', 'allehelgensdag'],
    'julaften' => ['2026-12-24', 'Julaften'],
    'første juledag' => ['2026-12-25', 'første juledag'],
    'andre juledag' => ['2026-12-26', 'andre juledag'],
]);

it('moves easter-relative observances with the year', function () use ($name) {
    expect($name('2027-02-07'))->toBe('fastelavn')        // easter 2027 = 28 mar
        ->and($name('2027-03-21'))->toBe('palmesøndag');
});

it('returns null for an ordinary day', function () use ($name) {
    expect($name('2026-06-18'))->toBeNull();
});
