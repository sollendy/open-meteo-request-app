<?php

namespace App\Http\Controllers;

use App\Models\weatherData;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreweatherDataRequest;
use App\Http\Requests\UpdateweatherDataRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Request;

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
    // public function store(StoreweatherDataRequest $request)
    // {
    //     $request->validate([
    //         'name' => 'required|string',
    //         'country' => 'required|string',
    //         'latitude' => 'required|string',
    //         'longitude' => 'required|string',
    //         'start_date' => 'required|date',
    //         'end_date' => 'required|date'
    //     ]);

    //     $cityName = $request->name;
    //     $country = $request->country;
    //     $lat = $request->latitude;
    //     $lon = $request->longitude;
    //     $startDate = $request->start_date;
    //     $endDate = $request->end_date;

    //     $city = DB::selectOne(
    //         "SELECT * FROM cities WHERE name = ? AND country = ? AND latitude = ? AND longitude = ? LIMIT 1",
    //         [$cityName, $country, $lat, $lon]
    //     );

    //     if (!$city) {
    //         DB::insert(
    //             "INSERT INTO cities (name, country, latitude, longitude, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())",
    //             [$cityName, $country, $lat, $lon]
    //         );

    //         $city = DB::selectOne(
    //             "SELECT * FROM cities WHERE name = ? AND country = ? AND latitude = ? AND longitude = ? LIMIT 1",
    //             [$cityName, $country, $lat, $lon]
    //         );
    //     }

    //     // $apiUrl = "https://archive-api.open-meteo.com/v1/archive";
    //     // $response = Http::get($apiUrl, [
    //     //     'latitude' => $lat,
    //     //     'longitude' => $lon,
    //     //     'start_date' => $startDate,
    //     //     'end_date' => $endDate,
    //     //     'hourly' => 'temperature_2m'
    //     // ]);

    //     // if (!$response->ok()) {
    //     //     // Ottieni il corpo della risposta dell'errore
    //     //     $errorBody = $response->body();

    //     //     // Ottieni il codice di stato HTTP (es. 400, 500)
    //     //     $statusCode = $response->status();

    //     //     // Se la risposta è in formato JSON, puoi decodificarla per ottenere dettagli
    //     //     $errorDetails = $response->json();

    //     //     // Restituisci i dati della richiesta insieme al messaggio di errore
    //     //     return response()->json([
    //     //         'error' => 'Errore API Open Meteo',
    //     //         'status_code' => $statusCode,
    //     //         'message' => $errorDetails['error'] ?? $errorBody,
    //     //         'request_data' => [
    //     //             'latitude' => $lat,
    //     //             'longitude' => $lon,
    //     //             'start_date' => $startDate,
    //     //             'end_date' => $endDate,
    //     //             'hourly' => 'temperature_2m',
    //     //         ],
    //     //     ], $statusCode);
    //     // }


    //     $apiUrl = "https://api.open-meteo.com/v1/forecast?" . http_build_query([
    //         'latitude' => $lat,
    //         'longitude' => $lon,
    //         'hourly' => 'temperature_2m',
    //         'start_date' => $startDate,
    //         'end_date' => $endDate,
    //     ]);

    //     // Effettua la richiesta HTTP
    //     $response = Http::get($apiUrl);

    //     // Verifica se la risposta è OK
    //     if (!$response->ok()) {
    //         return response()->json([
    //             'error' => 'Errore API Open Meteo',
    //             'status_code' => $response->status(),
    //             'message' => $response->json(),
    //             'request_data' => [
    //                 'latitude' => $lat,
    //                 'longitude' => $lon,
    //                 'start_date' => $startDate,
    //                 'end_date' => $endDate,
    //                 'hourly' => 'temperature_2m',
    //             ],
    //         ], $response->status());
    //     }

    //     // Se la risposta è OK, continua con il codice normale
    //     // $data = $response->json();
    //     // return response()->json($data);

    //     $data = $response->json();
    //     var_dump($data);
    //     Log::info("la tua response: ", $data);
    //     $timestamps = $data['hourly']['time'] ?? [];
    //     $temperatures = $data['hourly']['temperature_2m'] ?? [];

    //     $avgTemperature = 0;
    //     if (count($data["temperature_2m"]) > 0) {
    //         $avgTemperature = array_sum($data["temperature_2m"]) / count($data["temperature_2m"]);
    //     } else {
    //         $avgTemperature = 0;
    //     };

    //     $minTemperature = min($data["temperature_2m"]);
    //     $maxTemperature = max($data["temperature_2m"]);

    //     // if (count($timestamps) !== count($temperatures)) {
    //     //     return response()->json(['error' => 'Dati meteo incompleti'], 400);
    //     // }

    //     // foreach ($timestamps as $index => $timestamp) {
    //     //     $date = substr($timestamp, 0, 10);
    //     //     $temp = $temperatures[$index];

    //     //     $wdEsistono = DB::selectOne(
    //     //         "SELECT id FROM weather_data WHERE city_id = ? AND start_date = ? AND end_date = ? LIMIT 1",
    //     //         [$city->id, $date, $date]
    //     //     );

    //     //     if (!$wdEsistono) {
    //     //         DB::insert(
    //     //             "INSERT INTO weather_data (city_id, start_date, end_date, temperature, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())",
    //     //             [$city->id, $date, $date, $temp]
    //     //         );
    //     //     }
    //     // }

    //     return response()->json(['message' => 'Operazione ben riuscita']);
    // }

    public function store(StoreweatherDataRequest $request)
    {
        $request->validate([
            'name' => 'required|string',
            'country' => 'required|string',
            'latitude' => 'required|string',
            'longitude' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date'
        ]);

        $cityName = $request->name;
        $country = $request->country;
        $lat = $request->latitude;
        $lon = $request->longitude;
        $startDate = $request->start_date;
        $endDate = $request->end_date;

        // Controlla se la città è già presente nel database
        $city = DB::selectOne(
            "SELECT * FROM cities WHERE name = ? AND country = ? AND latitude = ? AND longitude = ? LIMIT 1",
            [$cityName, $country, $lat, $lon]
        );

        // Se la città non esiste, la inseriamo nel database
        if (!$city) {
            DB::insert(
                "INSERT INTO cities (name, country, latitude, longitude, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())",
                [$cityName, $country, $lat, $lon]
            );

            // Riconosciamo la città appena creata
            $city = DB::selectOne(
                "SELECT * FROM cities WHERE name = ? AND country = ? AND latitude = ? AND longitude = ? LIMIT 1",
                [$cityName, $country, $lat, $lon]
            );
        }

        // Chiamata API per ottenere i dati meteo
        $apiUrl = "https://api.open-meteo.com/v1/forecast?" . http_build_query([
            'latitude' => $lat,
            'longitude' => $lon,
            'hourly' => 'temperature_2m',
            'start_date' => $startDate,
            'end_date' => $endDate,
        ]);

        // Effettua la richiesta HTTP
        $response = Http::get($apiUrl);

        // Verifica la risposta dell'API
        if (!$response->ok()) {
            return response()->json([
                'error' => 'Errore API Open Meteo',
                'status_code' => $response->status(),
                'message' => $response->json(),
                'request_data' => [
                    'latitude' => $lat,
                    'longitude' => $lon,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'hourly' => 'temperature_2m',
                ],
            ], $response->status());
        }



        // Recupera i dati dalla risposta
        $data = $response->json();


        $timestamps = $data['hourly']['time'] ?? [];
        $temperatures = $data['hourly']['temperature_2m'] ?? [];

        $startRangeDate =  substr($timestamps[0], 0, 10);
        $currentTemperatureCollector = [];
        $counter = 0;
        // Salvataggio dei dati meteo nel database
        while ($counter < count($timestamps)) {
            foreach ($timestamps as $index => $timestamp) {
                $currentDate = substr($timestamp, 0, 10); // Estrarre la data (YYYY-MM-DD)
                $counter++;
                if ($currentDate == $startRangeDate) {
                    $temp = $temperatures[$index];
                    $currentTemperatureCollector[] = $temp;
                } else if ($currentDate != $startRangeDate) {
                    $minTemperature = min($currentTemperatureCollector);
                    $maxTemperature = max($currentTemperatureCollector);

                    // Calcolo delle statistiche aggregate
                    $avgTemperature = 0;
                    if (count($currentTemperatureCollector) > 0) {
                        $avgTemperature = array_sum($currentTemperatureCollector) / count($currentTemperatureCollector);
                    }

                    DB::insert(
                        "INSERT INTO weather_data (city_id, avg_temperature_date, avg_temperature, max_temperature, min_temperature, created_at, updated_at) 
                        VALUES (?, ?, ?, ?, ?, NOW(), NOW())",
                        [
                            $city->id,
                            $startRangeDate,
                            $avgTemperature,
                            $maxTemperature,
                            $minTemperature
                        ]
                    );

                    $startRangeDate = $currentDate;
                }
            }
        }

        $insertedWeatherData = DB::select(
            "SELECT * FROM weather_data wd JOIN cities c ON 
                wd.city_id = c.id 
                WHERE city_id = ? 
                AND avg_temperature_date BETWEEN ? AND ?
                ORDER BY avg_temperature_date ASC",
            [$city->id, $startDate, $endDate]
        );

        $response = [];

        foreach ($insertedWeatherData as $value) {
            $response[] = [
                'city_name' => $value->name,
                'period' => $value->avg_temperature_date,
                'temperature' => [
                    'avg' => $value->avg_temperature,
                    'min' => $value->min_temperature,
                    'max' => $value->max_temperature,
                ]
            ];
        }

        return response()->json(['message' => 'Operazione ben riuscita', 'data' => $response]);
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

    public function aggregaDati(Request $request, $id)
    {
        $request->validate([
            'from' => 'required|date',
            'to' => 'required|date'
        ]);

        $city = DB::selectOne("SELECT name FROM cities WHERE id = ?", [$id]);

        if (!$city) {
            return response()->json([
                'message' => 'Città non trovata.'
            ], 404);
        }

        $stats = DB::selectOne("
        SELECT 
            AVG(temperature) as avg,
            MIN(temperature) as min,
            MAX(temperature) as max
        FROM weather_data
        WHERE city_id = ?
        AND start_date BETWEEN ? AND ?
    ", [$id, $request->input("from"), $request->input("to")]);

        if (!$stats || $stats->avg === null) {
            return response()->json([
                'message' => 'Nessun dato disponibile per il periodo selezionato.'
            ], 404);
        }

        return response()->json([
            'city' => $city->name,
            'period' => "{$request->input("from")} → {$request->input("to")}",
            'temperature' => [
                'avg' => round($stats->avg, 1),
                'min' => round($stats->min, 1),
                'max' => round($stats->max, 1)
            ]
        ]);
    }
}
