<?php

use App\Http\Controllers\ReportController;
use App\Http\Controllers\SurvivorController;
use App\Http\Controllers\TradeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::apiResource('survivors', SurvivorController::class)->except('destroy');
Route::post('survivors/{survivor}/last-location', [SurvivorController::class, 'updateSurvivorLocation']);
Route::post('survivors/flag-survivor-infected', [ReportController::class, 'flagSurvivorAsInfected']);
Route::get('resume-reports', [ReportController::class, 'index']);
Route::post('survivors/trade', [TradeController::class, 'trade'])->name('trade');
