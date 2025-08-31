<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class City extends Model
{
    /** @use HasFactory<\Database\Factories\CityFactory> */
    use HasFactory;

    protected $fillable = [
        'id',
        'name',
        'latitude',
        'longitude',
    ];

    public function weatherData(): HasMany
    {
        return $this->hasMany(weatherData::class);
    }
}