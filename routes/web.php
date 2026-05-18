<?php

use App\Http\Controllers\CatalogController;
use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;
use Laravel\Fortify\Features;

// ─── Public Routes ───────────────────────────────────────────────
Route::get('/', [HomeController::class, 'index'])->name('home');

// Sitemap
Route::get('/sitemap.xml', [\App\Http\Controllers\SitemapController::class, 'index'])->name('sitemap');

// Catalog
Route::get('/catalog', [CatalogController::class, 'index'])->name('catalog.index');
Route::get('/catalog/{slug}', [CatalogController::class, 'show'])->name('catalog.show');

// ─── Authenticated Routes ────────────────────────────────────────
Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');
});

// ─── Admin Routes ────────────────────────────────────────────────
require __DIR__.'/admin.php';

// ─── Settings Routes ─────────────────────────────────────────────
require __DIR__.'/settings.php';
