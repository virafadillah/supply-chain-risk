<?php

use App\Http\Controllers\RiskScoreController;
use Illuminate\Support\Facades\Route;

Route::get('/risk', [RiskScoreController::class, 'calculateAll']);
Route::get('/risk/{country}', [RiskScoreController::class, 'calculate']);