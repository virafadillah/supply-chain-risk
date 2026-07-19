<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Country extends Model
{
    protected $fillable = [
        'name', 'iso2', 'iso3', 'capital', 'region',
        'currency_code', 'currency_name', 'latitude', 'longitude',
        'population', 'gdp', 'inflation_rate',
    ];

    public function ports(): HasMany
    {
        return $this->hasMany(Port::class);
    }

    public function riskScores(): HasMany
    {
        return $this->hasMany(RiskScore::class);
    }

    public function latestRiskScore()
    {
        return $this->hasOne(RiskScore::class)->latestOfMany('calculated_at');
    }

    public function newsCache(): HasMany
    {
        return $this->hasMany(NewsCache::class);
    }

    public function watchlistedBy(): HasMany
    {
        return $this->hasMany(Watchlist::class);
    }
}