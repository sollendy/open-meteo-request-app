<?php

use App\Http\Controllers\WeatherDataController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('form');
});

Route::get('/form-meteo', function () {
    return view('form');
});

Route::post('/weather-data/store', [WeatherDataController::class, 'store']);
Route::get('/cities/{id}/weather-data/aggregadati', [WeatherDataController::class, 'aggregaDati']);