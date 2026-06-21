<?php

namespace App\Http\Controllers;

use App\Pages\DateSlug;
use App\Pages\MonthBuilder;
use Illuminate\Http\Response;

class SitemapController extends Controller
{
    public function __invoke(): Response
    {
        $urls = [
            url('/'),
            ...array_map(
                fn (DateSlug $slug) => url('/'.$slug->toString()),
                DateSlug::all(),
            ),
        ];

        foreach (array_keys(config('themes')) as $slug) {
            $urls[] = url('/'.$slug);
        }

        foreach (MonthBuilder::MONTHS as $name) {
            $urls[] = url('/'.$name);
        }

        return response(view('sitemap', ['urls' => $urls])->render(), 200, [
            'Content-Type' => 'application/xml',
        ]);
    }
}
