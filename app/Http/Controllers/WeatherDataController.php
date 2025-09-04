<?php

namespace App\Http\Controllers;

use App\Models\weatherData;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreweatherDataRequest;
use App\Http\Requests\UpdateweatherDataRequest;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;

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
    //         'cityId' => 'required|integer',
    //         'name' => 'required|string',
    //         'country' => 'required|string',
    //         'latitude' => 'required|string',
    //         'longitude' => 'required|string',
    //         'start_date' => 'required|date',
    //         'end_date' => 'required|date'
    //     ]);

    //     $citiId = $request->cityId;
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
    //             "INSERT INTO cities (id, name, country, latitude, longitude, created_at, updated_at) VALUES (?, ?, ?, ?, ?, NOW(), NOW())",
    //             [$citiId, $cityName, $country, $lat, $lon]
    //         );

    //         $city = DB::selectOne(
    //             "SELECT * FROM cities WHERE name = ? AND country = ? AND latitude = ? AND longitude = ? LIMIT 1",
    //             [$cityName, $country, $lat, $lon]
    //         );
    //     }

    //     $apiUrl = "https://archive-api.open-meteo.com/v1/archive?" . http_build_query([
    //         'latitude' => $lat,
    //         'longitude' => $lon,
    //         'hourly' => 'temperature_2m',
    //         'start_date' => $startDate,
    //         'end_date' => $endDate,
    //     ]);

    //     $response = Http::get($apiUrl);

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


    //     $data = $response->json();


    //     $timestamps = $data['hourly']['time'] ?? [];
    //     $temperatures = $data['hourly']['temperature_2m'] ?? [];

    //     $startRangeDate =  substr($timestamps[0], 0, 10);
    //     $currentTemperatureCollector = [];
    //     $counter = 0;
    //     // Salvataggio dei dati meteo nel database
    //     while ($counter < count($timestamps)) {
    //         foreach ($timestamps as $index => $timestamp) {
    //             $currentDate = substr($timestamp, 0, 10); // Estraggo la data (YYYY-MM-DD)
    //             $counter++;
    //             if ($currentDate == $startRangeDate) {
    //                 $temp = $temperatures[$index];
    //                 $currentTemperatureCollector[] = $temp;
    //             } else if ($currentDate != $startRangeDate) {
    //                 $minTemperature = min($currentTemperatureCollector);
    //                 $maxTemperature = max($currentTemperatureCollector);

    //                 // Calcolo le statistiche aggregate
    //                 $avgTemperature = 0;
    //                 if (count($currentTemperatureCollector) > 0) {
    //                     $avgTemperature = array_sum($currentTemperatureCollector) / count($currentTemperatureCollector);
    //                 }

    //                 DB::insert(
    //                     "INSERT INTO weather_data (city_id, avg_temperature_date, avg_temperature, max_temperature, min_temperature, created_at, updated_at) 
    //                     VALUES (?, ?, ?, ?, ?, NOW(), NOW())",
    //                     [
    //                         $city->id,
    //                         $startRangeDate,
    //                         $avgTemperature,
    //                         $maxTemperature,
    //                         $minTemperature
    //                     ]
    //                 );

    //                 $startRangeDate = $currentDate;
    //             }
    //         }
    //     }

    //     $insertedWeatherData = DB::select(
    //         "SELECT * FROM weather_data wd JOIN cities c ON 
    //             wd.city_id = c.id 
    //             WHERE city_id = ? 
    //             AND avg_temperature_date BETWEEN ? AND ?
    //             ORDER BY avg_temperature_date ASC",
    //         [$city->id, $startDate, $endDate]
    //     );

    //     $response = [];

    //     foreach ($insertedWeatherData as $value) {
    //         $response[] = [
    //             'city_name' => $value->name,
    //             'period' => $value->avg_temperature_date,
    //             'temperature' => [
    //                 'avg' => $value->avg_temperature,
    //                 'min' => $value->min_temperature,
    //                 'max' => $value->max_temperature,
    //             ]
    //         ];
    //     }

    //     return response()->json(['message' => 'Operazione ben riuscita', 'data' => $response]);
    // }

    public function store(StoreweatherDataRequest $request)
    {
        $validated = $request->validate([
            'cityId' => 'required|integer',
            'name' => 'required|string',
            'country' => 'required|string',
            'latitude' => 'required|string',
            'longitude' => 'required|string',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date'
        ]);

        try {

            $citiId = $validated['cityId'];
            $cityName = $validated['name'];
            $country = $validated['country'];
            $lat = $validated['latitude'];
            $lon = $validated['longitude'];
            $startDate = $validated['start_date'];
            $endDate = $validated['end_date'];

            $city = DB::selectOne(
                "SELECT * FROM cities WHERE name = ? AND country = ? AND latitude = ? AND longitude = ? LIMIT 1",
                [$cityName, $country, $lat, $lon]
            );

            if (!$city) {
                DB::insert(
                    "INSERT INTO cities (id, name, country, latitude, longitude, created_at, updated_at) VALUES (?, ?, ?, ?, ?, NOW(), NOW())",
                    [$citiId, $cityName, $country, $lat, $lon]
                );

                $city = DB::selectOne(
                    "SELECT * FROM cities WHERE name = ? AND country = ? AND latitude = ? AND longitude = ? LIMIT 1",
                    [$cityName, $country, $lat, $lon]
                );

                if (!$city) {
                    return response()->json(['error' => 'Impossibile salvare la città.'], 500);
                }
            }

            $apiUrl = "https://archive-api.open-meteo.com/v1/archive?" . http_build_query([
                'latitude' => $lat,
                'longitude' => $lon,
                'hourly' => 'temperature_2m',
                'start_date' => $startDate,
                'end_date' => $endDate,
            ]);

            $response = Http::get($apiUrl);

            if (!$response->ok()) {
                return response()->json([
                    'error' => 'Errore nella chiamata all\'API Open Meteo.',
                    'status_code' => $response->status(),
                    'message' => $response->json(),
                ], $response->status());
            }

            $data = $response->json();
            $timestamps = $data['hourly']['time'] ?? [];
            $temperatures = $data['hourly']['temperature_2m'] ?? [];

            if (empty($timestamps) || empty($temperatures) || count($timestamps) !== count($temperatures)) {
                return response()->json(['error' => 'Dati meteo non validi o incompleti.'], 422);
            }

            $startRangeDate = substr($timestamps[0], 0, 10);
            $currentTemperatureCollector = [];
            $processedDates = [];

            foreach ($timestamps as $index => $timestamp) {
                $currentDate = substr($timestamp, 0, 10);
                $temp = $temperatures[$index];

                if ($currentDate === $startRangeDate) {
                    $currentTemperatureCollector[] = $temp;
                } else {
                    if (!in_array($startRangeDate, $processedDates)) {
                        $this->insertWeatherData($city->id, $startRangeDate, $currentTemperatureCollector);
                        $processedDates[] = $startRangeDate;
                    }

                    $startRangeDate = $currentDate;
                    $currentTemperatureCollector = [$temp];
                }
            }

            if (!in_array($startRangeDate, $processedDates)) {
                $this->insertWeatherData($city->id, $startRangeDate, $currentTemperatureCollector);
            }

            $insertedWeatherData = DB::select(
                "SELECT * FROM weather_data wd JOIN cities c ON wd.city_id = c.id 
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
                        'avg' => round($value->avg_temperature, 1),
                        'min' => round($value->min_temperature, 1),
                        'max' => round($value->max_temperature, 1),
                    ]
                ];
            }

            return response()->json(['message' => 'Operazione completata con successo.', 'data' => $response]);
        } catch (\Exception $e) {
            Log::error("Errore nel salvataggio dati meteo in weatherDataController: " . $e->getMessage());
            return response()->json(['error' => 'Errore interno del server.'], 500);
        }
    }

    private function insertWeatherData($cityId, $date, $temperatures)
    {
        if (empty($temperatures)) return;

        $minTemperature = min($temperatures);
        $maxTemperature = max($temperatures);
        $avgTemperature = array_sum($temperatures) / count($temperatures);

        DB::insert(
            "INSERT INTO weather_data (city_id, avg_temperature_date, avg_temperature, max_temperature, min_temperature, created_at, updated_at) 
         VALUES (?, ?, ?, ?, ?, NOW(), NOW())",
            [$cityId, $date, $avgTemperature, $maxTemperature, $minTemperature]
        );
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
            'to' => 'required|date|after_or_equal:from'
        ]);

        try {
            $city = DB::selectOne("SELECT name FROM cities WHERE id = ?", [$id]);

            if (!$city) {
                return response()->json([
                    'message' => 'Città non trovata.'
                ], 404);
            }

            $stats = DB::selectOne("
            SELECT 
                AVG(avg_temperature) as avg,
                MIN(min_temperature) as min,
                MAX(max_temperature) as max
            FROM weather_data
            WHERE city_id = ?
            AND avg_temperature_date BETWEEN ? AND ?
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
        } catch (\Exception $e) {
            Log::error("Errore nel controller aggregaDati in weatherDataController: " . $e->getMessage());
            return response()->json(['error' => 'Errore interno del server.'], 500);
        }
    }
}
