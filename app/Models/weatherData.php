<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class weatherData extends Model
{
    /** @use HasFactory<\Database\Factories\WeatherDataFactory> */
    use HasFactory;

    protected $fillable = [
        'id',
        'city_id',
        'avg_temperature_date',
        'avg_temperature',
        'max_temperature',
        'min_temperature',
    ];

    public function city(): BelongsTo
    {
        return $this->belongsTo(City::class);
    }
}