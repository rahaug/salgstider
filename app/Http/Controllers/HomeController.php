<?php

namespace App\Http\Controllers;

use App\Pages\HomeBuilder;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(HomeBuilder $home): View
    {
        return view('pages.home', $home->build());
    }
}
