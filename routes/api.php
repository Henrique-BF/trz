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
Route::post('survivors/{survivor}/last-location', [SurvivorController::class, 'updateSurvivorLocation'])
    ->name('survivors.update.last_location');
Route::post('survivors/flag-survivor-infected', [ReportController::class, 'flagSurvivorAsInfected'])
    ->name('flag_survivor_infected');
Route::post('survivors/trade', [TradeController::class, 'trade'])
    ->name('trade');
Route::get('items', [TradeController::class, 'indexItems'])
    ->name('get_items');
Route::get('resume-reports', [ReportController::class, 'index'])
    ->name('get_resume_reports');
