<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Cache;

class ExchangeRateService
{
    public function getRate(string $baseCurrency = 'USD'): array
    {
        return Cache::remember("exchange_rate_{$baseCurrency}", 3600, function () use ($baseCurrency) {
            $response = Http::get("https://open.er-api.com/v6/latest/{$baseCurrency}");

            if (!$response->successful()) {
                return [];
            }

            return $response->json()['rates'] ?? [];
        });
    }

    public function getVolatility(string $currencyCode): float
    {
        $current = $this->getRate('USD')[$currencyCode] ?? null;

        if ($current === null) return 0;

        $historyKey = "currency_history_{$currencyCode}";
        $history = Cache::get($historyKey, []);
        $history[] = $current;
        $history = array_slice($history, -30);
        Cache::put($historyKey, $history, now()->addDays(30));

        if (count($history) < 2) {
            return 10;
        }

        $changes = [];
        for ($i = 1; $i < count($history); $i++) {
            $changes[] = abs(($history[$i] - $history[$i - 1]) / $history[$i - 1]) * 100;
        }

        $avgChange = array_sum($changes) / count($changes);

        return round(min($avgChange * 20, 100), 2);
    }
}