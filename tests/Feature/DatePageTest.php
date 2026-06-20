<?php

use Carbon\CarbonImmutable;

beforeEach(function () {
    CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-06-18', 'Europe/Oslo'));
});

afterEach(function () {
    CarbonImmutable::setTestNow();
});

it('renders a normal open day', function () {
    $this->get('/18-juni')
        ->assertOk()
        ->assertSee('Åpningstider for ølsalg og Vinmonopolet 18. juni 2026')
        ->assertSee('Åpent')
        ->assertSee('kan du kjøpe øl i butikk til 20:00')   // lede
        ->assertSee('08–20');                                // hero range
});

it('puts the holiday name and the date in the heading for named days', function () {
    $this->get('/25-mars') // skjærtorsdag 2027
        ->assertSee('Skjærtorsdag 25. mars 2027')                                    // eyebrow: name + date
        ->assertSee('Åpningstider for ølsalg og Vinmonopolet skjærtorsdag 2027')      // h1: name (date is in the eyebrow)
        ->assertSee('er det ikke ølsalg i butikk')                                    // lede prose
        ->assertSee('<title>Ølsalg skjærtorsdag 25. mars 2027', false);               // title keeps both
});

it('links to the season hub and shows the cluster table on a date inside a season', function () {
    $this->get('/24-mars') // inside easter 2027
        ->assertSee('href="/paske"', false)   // hub link
        ->assertSee('Åpningstider i påsken')
        ->assertSee('Skjærtorsdag')           // cluster table rows
        ->assertSee('Langfredag');
});

it('leads with the buy-by deadline on a closed red day', function () {
    $this->get('/17-mai')
        ->assertSee('Kjøp alkohol til')
        ->assertSee('Lørdag 15. mai');   // 2027: pinse coincides, last open day is sat 15 may
});

it('leads with the deadline on a run-up (eve) day', function () {
    $this->get('/30-april')
        ->assertSee('Kjøp alkohol til')
        ->assertSee('08–20');
});

it('embeds valid faq json-ld that mirrors visible content', function () {
    $this->get('/24-desember')
        ->assertSee('"@context":"https://schema.org"', false)
        ->assertSee('"@type":"FAQPage"', false)
        ->assertSee('Er Vinmonopolet åpent 24. desember 2026?');
});

it('sets a canonical url without the year', function () {
    $this->get('/24-desember')
        ->assertSee('<link rel="canonical" href="'.url('/24-desember').'">', false);
});

it('shows upcoming notable days with links to their date pages', function () {
    CarbonImmutable::setTestNow(CarbonImmutable::parse('2026-12-20', 'Europe/Oslo'));

    $this->get('/1-januar')
        ->assertSee('Spesielle dager fremover')
        ->assertSee('href="/25-desember"', false);
});

it('404s on an unknown slug', function () {
    $this->get('/ikke-en-dato')->assertNotFound();
});

it('serves a sitemap listing date urls', function () {
    $this->get('/sitemap.xml')
        ->assertOk()
        ->assertHeader('Content-Type', 'application/xml')
        ->assertSee(url('/16-mai'), false);
});
