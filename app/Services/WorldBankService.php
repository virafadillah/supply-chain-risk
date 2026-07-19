<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WorldBankService
{
    protected string $baseUrl = 'https://api.worldbank.org/v2';

    public function getIndicator(string $iso3, string $indicator): ?float
    {
        try {
            $response = Http::timeout(20)->retry(2, 500)->get(
                "{$this->baseUrl}/country/{$iso3}/indicator/{$indicator}",
                [
                    'format' => 'json',
                    'per_page' => 5,
                ]
            );
        } catch (\Exception $e) {
            report($e);
            return null;
        }

        if (!$response->successful()) {
            return null;
        }

        $data = $response->json();

        foreach ($data[1] ?? [] as $entry) {
            if ($entry['value'] !== null) {
                return (float) $entry['value'];
            }
        }

        return null;
    }

    public function getGdp(string $iso3): ?float
    {
        return $this->getIndicator($iso3, 'NY.GDP.MKTP.CD');
    }

    public function getInflation(string $iso3): ?float
    {
        return $this->getIndicator($iso3, 'FP.CPI.TOTL.ZG');
    }

    public function getPopulation(string $iso3): ?float
    {
        return $this->getIndicator($iso3, 'SP.POP.TOTL');
    }

    public function calculateInflationRisk(?float $inflation): float
    {
        if ($inflation === null) return 0;

        return round(min(max($inflation, 0) / 15 * 100, 100), 2);
    }
}