<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class);

Route::get('/sitemap.xml', SitemapController::class);

Route::get('/{slug}', [PageController::class, 'show']);
