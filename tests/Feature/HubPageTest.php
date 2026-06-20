<?php

use App\Pages\Themes;
use Carbon\CarbonImmutable;

beforeEach(function () {
    CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-06-18', 'Europe/Oslo'));
});

afterEach(function () {
    CarbonImmutable::setTestNow();
});

it('renders the paske hub with a compact deadline summary', function () {
    $this->get('/paske')
        ->assertOk()
        ->assertSee('påsken 2027')                 // next easter after today
        ->assertSee('Kjøp alkohol til påsken innen')
        ->assertSee('Onsdag 24. mars')             // the deadline eve
        ->assertSee('08–18')
        ->assertSee('10–16')
        ->assertDontSee('Siste sjanse til å kjøpe alkohol til påsken')
        ->assertSee('href="/27-mars"', false)      // påskeaften links to its date page
        ->assertSee('Hvorfor feirer vi påske?');   // EF cross-link
});

it('labels cluster days by holiday name with date and links', function () {
    $this->get('/paske')
        ->assertSee('Skjærtorsdag')                // named holiday
        ->assertSee('Langfredag')
        ->assertSee('Påskeaften')                  // eve name (an open mid-cluster buy window)
        ->assertSee('27. mars')                    // date shown alongside the name
        ->assertSee('href="/25-mars"', false);     // links to the date page
});

it('shows the current easter until it passes, then switches to next year', function () {
    $themes = app(Themes::class);
    $paske = $themes->find('paske');

    $year = function () use ($themes, $paske) {
        return $themes->cluster($paske, CarbonImmutable::now('Europe/Oslo')->startOfDay())['year'];
    };

    CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-03-30', 'Europe/Oslo')); // holy week 2026
    expect($year())->toBe(2026);

    CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-04-06', 'Europe/Oslo')); // 2. påskedag 2026
    expect($year())->toBe(2026);

    CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-04-07', 'Europe/Oslo')); // day after
    expect($year())->toBe(2027);
});

it('renders the jul hub with a split deadline (Pol shuts on julaften)', function () {
    $this->get('/jul')
        ->assertOk()
        ->assertSee('julen 2026')
        ->assertSee('Kjøp alkohol til julen innen')
        ->assertSee('Torsdag 24. desember')   // beer: last chance julaften (til 16:00)
        ->assertSee('Onsdag 23. desember')    // wine: earlier, Pol shut on julaften
        ->assertSee('Julaften')                // cluster table row
        ->assertSee('må butikkene stenge kl. 16'); // store-closing note (julaften in cluster)
});

it('does not show the store-closing note on a season without one (paske has it, but a plain date does not)', function () {
    $this->get('/18-juni')->assertDontSee('må butikkene stenge kl. 16');
});

it('includes /paske in the sitemap', function () {
    $this->get('/sitemap.xml')
        ->assertSee(url('/paske'), false);
});
