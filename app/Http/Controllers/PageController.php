<?php

namespace App\Http\Controllers;

use App\Pages\DatePageBuilder;
use App\Pages\DateSlug;
use App\Pages\HubBuilder;
use App\Pages\MonthBuilder;
use App\Pages\Themes;
use Illuminate\View\View;

class PageController extends Controller
{
    public function show(string $slug, DatePageBuilder $datePage, MonthBuilder $months, HubBuilder $hub, Themes $themes): View
    {
        if ($dateSlug = DateSlug::parse($slug)) {
            return view('pages.date', $datePage->build($dateSlug));
        }

        if (($month = $months->resolve($slug)) !== null) {
            return view('pages.month', $months->build($month));
        }

        if ($theme = $themes->find($slug)) {
            return view('pages.hub', $hub->build($theme));
        }

        abort(404);
    }
}
