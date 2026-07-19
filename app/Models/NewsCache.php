<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class NewsCache extends Model
{
    protected $table = 'news_cache';

    protected $fillable = [
        'country_id', 'title', 'description', 'url', 'source',
        'sentiment', 'positive_score', 'negative_score', 'published_at',
    ];

    protected $casts = ['published_at' => 'datetime'];

    public function country(): BelongsTo
    {
        return $this->belongsTo(Country::class);
    }
}