<?php

use Carbon\CarbonImmutable;

beforeEach(function () {
    CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-06-18', 'Europe/Oslo'));
});

afterEach(function () {
    CarbonImmutable::setTestNow();
});

it('lists each red day and its deadline for a month with avvik', function () {
    $this->get('/mai')
        ->assertOk()
        ->assertSee('mai 2027')                 // upcoming May
        ->assertSee('Røde dager i mai')
        ->assertSee('Siste frist')
        ->assertSee('href="/1-mai"', false)     // 1. mai links to its date page
        ->assertSee('href="/17-mai"', false);   // 17. mai too
});

it('shows the no-avvik state with a normal week for a quiet month', function () {
    $this->get('/august')
        ->assertOk()
        ->assertSee('august 2026')
        ->assertSee('Det er ingen avvik for ølsalg og Vinmonopolet i august 2026')
        ->assertSee('Vanlige åpningstider')
        ->assertSee('Mandag')                   // dateless weekday rows
        ->assertSee('Søndag')
        ->assertDontSee('Røde dager');          // no avvik table
});

it('resolves all twelve months', function () {
    foreach (['januar', 'februar', 'mars', 'april', 'mai', 'juni', 'juli', 'august', 'september', 'oktober', 'november', 'desember'] as $name) {
        $this->get("/{$name}")->assertOk();
    }
});

it('includes the month pages in the sitemap', function () {
    $this->get('/sitemap.xml')
        ->assertSee(url('/mai'), false)
        ->assertSee(url('/desember'), false);
});
