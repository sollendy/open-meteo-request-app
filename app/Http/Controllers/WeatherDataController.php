<?php

namespace App\Http\Controllers;

use App\Models\weatherData;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreweatherDataRequest;
use App\Http\Requests\UpdateweatherDataRequest;

class WeatherDataController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreweatherDataRequest $request)
    {
        //
    }

    /**
     * Display the specified resource.
     */
    public function show(weatherData $weatherData)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(weatherData $weatherData)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateweatherDataRequest $request, weatherData $weatherData)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(weatherData $weatherData)
    {
        //
    }
}
