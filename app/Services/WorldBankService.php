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

    // Ambil data historis (beberapa tahun terakhir) untuk 1 indikator
    // Return: array of ['year' => 2023, 'value' => 12345.6], urut dari tahun terlama ke terbaru
    public function getIndicatorHistory(string $iso3, string $indicator, int $years = 10): array
    {
        try {
            $response = Http::timeout(20)->retry(2, 500)->get(
                "{$this->baseUrl}/country/{$iso3}/indicator/{$indicator}",
                [
                    'format' => 'json',
                    'per_page' => $years,
                ]
            );
        } catch (\Exception $e) {
            report($e);
            return [];
        }

        if (!$response->successful()) {
            return [];
        }

        $data = $response->json();
        $history = [];

        foreach ($data[1] ?? [] as $entry) {
            if ($entry['value'] !== null) {
                $history[] = [
                    'year' => $entry['date'],
                    'value' => (float) $entry['value'],
                ];
            }
        }

        // World Bank ngasih data dari terbaru ke terlama, kita balik jadi terlama ke terbaru (biar cocok buat line chart)
        return array_reverse($history);
    }

    public function getGdpHistory(string $iso3, int $years = 10): array
    {
        return $this->getIndicatorHistory($iso3, 'NY.GDP.MKTP.CD', $years);
    }

    public function getInflationHistory(string $iso3, int $years = 10): array
    {
        return $this->getIndicatorHistory($iso3, 'FP.CPI.TOTL.ZG', $years);
    }
}