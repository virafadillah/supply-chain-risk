<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\WatchlistController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/{country}', [DashboardController::class, 'show'])->name('dashboard.show');

    Route::get('/map', [DashboardController::class, 'map'])->name('map');
    Route::get('/compare', [DashboardController::class, 'compare'])->name('compare');
    Route::get('/currency', [DashboardController::class, 'currency'])->name('currency');
    Route::get('/news', [DashboardController::class, 'news'])->name('news');
    Route::get('/ports', [DashboardController::class, 'portsList'])->name('ports');
    Route::get('/search-country', [DashboardController::class, 'searchCountry'])->name('search.country');

    Route::get('/watchlist', [WatchlistController::class, 'index'])->name('watchlist');
    Route::post('/watchlist/{country}/toggle', [WatchlistController::class, 'toggle'])->name('watchlist.toggle');

    Route::get('/chart-data/risk-comparison', [DashboardController::class, 'riskComparisonData'])->name('chart.risk-comparison');
    Route::get('/chart-data/risk-history/{country}', [DashboardController::class, 'riskHistoryData'])->name('chart.risk-history');
    Route::get('/chart-data/map', [DashboardController::class, 'mapData'])->name('chart.map');
    Route::get('/chart-data/weather-map', [DashboardController::class, 'weatherMapData'])->name('chart.weather-map');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('users', \App\Http\Controllers\Admin\UserController::class)->except(['show', 'create', 'store']);
    Route::resource('ports', \App\Http\Controllers\Admin\PortController::class)->except(['show']);
    Route::resource('articles', \App\Http\Controllers\Admin\ArticleController::class)->except(['show']);
});

require __DIR__.'/auth.php';