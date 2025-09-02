<?php

namespace App\Http\Controllers;

use App\Models\City;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCityRequest;
use App\Http\Requests\UpdateCityRequest;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CityController extends Controller
{
    /**
     * Display a listing of the resource.
     */

    //funzione per avviare la chiamata alla app di geocoding dopo la ricerca di città per l'utente 

    public function getCityData($name, $country)
    {
        // Create a new Guzzle client instance
        $client = new Client();

        // API endpoint URL with your desired location and units (e.g., London, Metric units)
        $apiUrl = "https://geocoding-api.open-meteo.com/v1/search?name=Berlin&count=10&language=it&format=json";

        try {
            // Make a GET request to the OpenWeather API
            $response = $client->get($apiUrl);

            // Get the response body as an array
            $data = json_decode($response->getBody(), true);

            // Handle the retrieved weather data as needed (e.g., pass it to a view)
            return view('weather', ['weatherData' => $data]);
        } catch (\Exception $e) {
            // Handle any errors that occur during the API request
            return view('api_error', ['error' => $e->getMessage()]);
        }
    }

    //funzione per avviare la chiamata alla app di geocoding dopo la ricerca di città per l'utente 

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
    public function store(StoreCityRequest $request)
    {
        $validated = $request->validate([
            "name" => "required|string",
            "latitude" => "required|numeric",
            "longitude" => "required|numeric",
        ]);

        $name = $validated["name"];
        $latitude = $validated["latitude"];
        $longitude = $validated["longitude"];

        $cittaEsiste = DB::selectOne(
            "SELECT * FROM cities WHERE name = ? AND latitude = ? AND longitude = ? LIMIT 1",
            [$name, $latitude, $longitude]
        );

        if ($cittaEsiste) {
            return response()->json([
                "city" => $cittaEsiste,
                "message" => "ritorno la città già presente"
            ]);
        }

        $insertCity = DB::insert(
            "INSERT INTO cities (name, latitude, longitude) VALUES (?, ?, ?)",
            [$name, $latitude, $longitude]
        );
    }

    /**
     * Display the specified resource.
     */
    public function show(City $city)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(City $city)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateCityRequest $request, City $city)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(City $city)
    {
        //
    }
}
