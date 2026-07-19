<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class RiskScore extends Model
{
    protected $fillable = [
        'country_id', 'weather_risk', 'inflation_risk',
        'currency_risk', 'news_risk', 'total_risk', 'risk_level', 'calculated_at',
    ];

    protected $casts = ['calculated_at' => 'datetime'];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}