<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class OpenMeteoService
{
    public function getWeather(float $lat, float $lon): array
    {
        try {
            $response = Http::timeout(20)
                ->retry(2, 500)
                ->get('https://api.open-meteo.com/v1/forecast', [
                    'latitude' => $lat,
                    'longitude' => $lon,
                    'current' => 'temperature_2m,wind_speed_10m,precipitation',
                    'timezone' => 'auto',
                ]);

            if (!$response->successful()) {
                return ['temperature' => null, 'wind_speed' => null, 'precipitation' => null];
            }

            $data = $response->json();

            return [
                'temperature' => $data['current']['temperature_2m'] ?? null,
                'wind_speed' => $data['current']['wind_speed_10m'] ?? null,
                'precipitation' => $data['current']['precipitation'] ?? null,
            ];
        } catch (\Exception $e) {
            report($e);
            // Kalau API gagal total, kembalikan data kosong (bukan error) supaya negara lain tetap diproses
            return ['temperature' => null, 'wind_speed' => null, 'precipitation' => null];
        }
    }

    public function calculateWeatherRisk(array $weather): float
    {
        $windRisk = min(($weather['wind_speed'] ?? 0) / 60 * 100, 100);
        $rainRisk = min(($weather['precipitation'] ?? 0) / 20 * 100, 100);

        return round(($windRisk * 0.5) + ($rainRisk * 0.5), 2);
    }
}