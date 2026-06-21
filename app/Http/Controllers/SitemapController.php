<?php

namespace App\Http\Controllers;

use App\Pages\DateSlug;
use App\Pages\MonthBuilder;
use App\Pages\Themes;
use Carbon\CarbonImmutable;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(MonthBuilder $months, Themes $themes): Response
    {
        $today = CarbonImmutable::now('Europe/Oslo')->startOfDay();

        $urls = [['loc' => url('/'), 'lastmod' => null]];

        foreach (DateSlug::all() as $slug) {
            $urls[] = [
                'loc' => url('/'.$slug->toString()),
                'lastmod' => $slug->lastChanged($today)->toDateString(),
            ];
        }

        foreach (array_keys(config('themes')) as $slug) {
            $urls[] = [
                'loc' => url('/'.$slug),
                'lastmod' => $themes->lastChanged($themes->find($slug), $today)->toDateString(),
            ];
        }

        foreach (MonthBuilder::MONTHS as $month => $name) {
            $urls[] = [
                'loc' => url('/'.$name),
                'lastmod' => $months->lastChanged($month, $today)->toDateString(),
            ];
        }

        return response(view('sitemap', ['urls' => $urls])->render(), 200, [
            'Content-Type' => 'application/xml',
        ]);
    }
}
