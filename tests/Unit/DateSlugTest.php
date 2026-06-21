<?php

use App\Pages\DateSlug;
use Carbon\CarbonImmutable;

it('parses a valid slug', function () {
    $slug = DateSlug::parse('16-mai');

    expect($slug)->not->toBeNull()
        ->and($slug->day)->toBe(16)
        ->and($slug->month)->toBe(5);
});

it('rejects invalid slugs', function (string $slug) {
    expect(DateSlug::parse($slug))->toBeNull();
})->with(['paske', '32-mai', '16-foo', 'mai', '0-mai']);

it('round-trips slug and label', function () {
    $slug = DateSlug::parse('24-desember');

    expect($slug->toString())->toBe('24-desember')
        ->and($slug->label())->toBe('24. desember');
});

it('resolves the next occurrence', function () {
    $from = CarbonImmutable::parse('2026-06-18', 'Europe/Oslo');

    expect(DateSlug::parse('16-mai')->nextOccurrence($from)->toDateString())->toBe('2027-05-16')
        ->and(DateSlug::parse('24-desember')->nextOccurrence($from)->toDateString())->toBe('2026-12-24');
});

it('reports when a date page last rolled over', function () {
    $from = CarbonImmutable::parse('2026-06-18', 'Europe/Oslo');

    // 16 mai 2026 has passed, so the page now shows 2027 — it flipped the day after 16 mai 2026.
    expect(DateSlug::parse('16-mai')->lastChanged($from)->toDateString())->toBe('2026-05-17')
        // 24 des 2026 is still upcoming, so it has shown 2026 since the day after 24 des 2025.
        ->and(DateSlug::parse('24-desember')->lastChanged($from)->toDateString())->toBe('2025-12-25');
});

it('skips non-leap years for 29 feb', function () {
    $from = CarbonImmutable::parse('2026-06-18', 'Europe/Oslo');

    // Next 29 feb is 2028; the page has shown that since the day after 29 feb 2024.
    expect(DateSlug::parse('29-februar')->lastChanged($from)->toDateString())->toBe('2024-03-01');
});

it('enumerates every day of the year including 29 feb', function () {
    expect(DateSlug::all())->toHaveCount(366);
});
