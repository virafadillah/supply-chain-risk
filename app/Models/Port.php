<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Port extends Model
{
    protected $fillable = ['unlocode', 'name', 'country_id', 'latitude', 'longitude', 'port_type'];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}