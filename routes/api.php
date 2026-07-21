<?php

use App\Http\Controllers\RiskScoreController;
use App\Http\Controllers\Api\CountryController;
use App\Http\Controllers\Api\PortController;
use App\Http\Controllers\Api\NewsController;
use App\Http\Controllers\Api\CurrencyController;
use Illuminate\Support\Facades\Route;

Route::get('/risk', [RiskScoreController::class, 'calculateAll']);
Route::get('/risk/{country}', [RiskScoreController::class, 'calculate']);

Route::get('/countries', [CountryController::class, 'index']);
Route::get('/countries/{country}', [CountryController::class, 'show']);

Route::get('/ports', [PortController::class, 'index']);
Route::get('/ports/{port}', [PortController::class, 'show']);

Route::get('/news', [NewsController::class, 'index']);

Route::get('/currency', [CurrencyController::class, 'index']);
Route::get('/currency/{country}', [CurrencyController::class, 'show']);