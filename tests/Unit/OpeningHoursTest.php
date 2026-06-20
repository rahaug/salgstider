<?php

use App\Calendar\DayClassifier;
use App\Calendar\OpeningHours;
use App\Enums\ProductType;
use Carbon\CarbonImmutable;

beforeEach(function () {
    $this->hours = new OpeningHours(new DayClassifier);
});

it('resolves beer sales', function (string $date, bool $open, ?string $closes) {
    $window = $this->hours->on(CarbonImmutable::parse($date), ProductType::Beer);

    expect($window->open)->toBe($open);

    if ($open) {
        expect($window->closes)->toBe($closes);
    }
})->with([
    'normal wednesday' => ['2026-06-17', true, '20:00'],
    'saturday (eve of sunday)' => ['2026-06-20', true, '18:00'],
    'sunday' => ['2026-06-21', false, null],
    'wed before ascension (exception)' => ['2026-05-13', true, '20:00'],
    'ascension day' => ['2026-05-14', false, null],
    'sat before 17 may' => ['2026-05-16', true, '18:00'],
    '17 may (sunday)' => ['2026-05-17', false, null],
    'julaften (shops close 16:00)' => ['2026-12-24', true, '16:00'],
    'påskeaften (shops close 16:00)' => ['2027-03-27', true, '16:00'],
    'good friday' => ['2026-04-03', false, null],
    'first day of christmas' => ['2026-12-25', false, null],
]);

it('finds the last sale before a closed day', function () {
    $beer = $this->hours->previousOpen(CarbonImmutable::parse('2026-05-17'), ProductType::Beer);
    expect($beer->date->toDateString())->toBe('2026-05-16')
        ->and($beer->closes)->toBe('18:00');

    $wine = $this->hours->previousOpen(CarbonImmutable::parse('2026-12-25'), ProductType::Wine);
    expect($wine->date->toDateString())->toBe('2026-12-23')
        ->and($wine->closes)->toBe('18:00');

    $beerXmas = $this->hours->previousOpen(CarbonImmutable::parse('2026-12-25'), ProductType::Beer);
    expect($beerXmas->date->toDateString())->toBe('2026-12-24')
        ->and($beerXmas->closes)->toBe('16:00');
});

it('gets the critical season days right', function () {
    $beer = fn (string $date) => $this->hours->on(CarbonImmutable::parse($date), ProductType::Beer);
    $wine = fn (string $date) => $this->hours->on(CarbonImmutable::parse($date), ProductType::Wine);

    // Julaften: shops must close 16:00 (helligdagsloven); Vinmonopolet closed
    expect($beer('2026-12-24')->open)->toBeTrue()
        ->and($beer('2026-12-24')->closes)->toBe('16:00')
        ->and($wine('2026-12-24')->open)->toBeFalse();

    // Påskeaften 2027: shops close 16:00; Vinmonopolet reduced to 16:00
    expect($beer('2027-03-27')->open)->toBeTrue()
        ->and($beer('2027-03-27')->closes)->toBe('16:00')
        ->and($wine('2027-03-27')->closes)->toBe('16:00');

    // Pinseaften 2027 (15 May, day before 1. pinsedag): shops close 16:00
    expect($beer('2027-05-15')->closes)->toBe('16:00');

    // 1. nyttårsdag: alt stengt
    expect($beer('2027-01-01')->open)->toBeFalse()
        ->and($wine('2027-01-01')->open)->toBeFalse();
});

it('detects a buy deadline on the run-up to a holiday closure', function () {
    // Sat 16 May 2026 is open; next day is 17. mai (closed) → deadline
    expect($this->hours->deadlineBefore(CarbonImmutable::parse('2026-05-16'), ProductType::Beer))
        ->toBe('grunnlovsdagen');

    // Wine: 23 Dec 2026 open; next day is julaften (Pol closed) → deadline
    expect($this->hours->deadlineBefore(CarbonImmutable::parse('2026-12-23'), ProductType::Wine))
        ->toBe('Julaften');

    // Ordinary Saturday before an ordinary Sunday → no deadline (routine)
    expect($this->hours->deadlineBefore(CarbonImmutable::parse('2026-06-06'), ProductType::Beer))
        ->toBeNull();

    // A closed day itself is not a buy deadline
    expect($this->hours->deadlineBefore(CarbonImmutable::parse('2026-05-17'), ProductType::Beer))
        ->toBeNull();
});

it('resolves wine sales', function (string $date, bool $open, ?string $closes) {
    $window = $this->hours->on(CarbonImmutable::parse($date), ProductType::Wine);

    expect($window->open)->toBe($open);

    if ($open) {
        expect($window->closes)->toBe($closes);
    }
})->with([
    'normal wednesday' => ['2026-06-17', true, '18:00'],
    'saturday (eve of sunday)' => ['2026-06-20', true, '16:00'],
    'sunday' => ['2026-06-21', false, null],
    'wed before ascension (exception)' => ['2026-05-13', true, '18:00'],
    'julaften (closed)' => ['2026-12-24', false, null],
    'first day of christmas' => ['2026-12-25', false, null],
]);
