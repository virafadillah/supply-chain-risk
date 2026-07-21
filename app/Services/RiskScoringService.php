<?php

namespace App\Services;

use App\Models\Country;
use App\Models\RiskScore;
use Carbon\Carbon;

class RiskScoringService
{
    protected OpenMeteoService $weatherService;
    protected WorldBankService $worldBankService;
    protected ExchangeRateService $exchangeRateService;
    protected GNewsService $newsService;

    // Bobot sesuai spesifikasi
    protected float $weatherWeight = 0.30;
    protected float $inflationWeight = 0.20;
    protected float $currencyWeight = 0.10;
    protected float $newsWeight = 0.40;

    public function __construct(
        OpenMeteoService $weatherService,
        WorldBankService $worldBankService,
        ExchangeRateService $exchangeRateService,
        GNewsService $newsService
    ) {
        $this->weatherService = $weatherService;
        $this->worldBankService = $worldBankService;
        $this->exchangeRateService = $exchangeRateService;
        $this->newsService = $newsService;
    }

    public function calculateForCountry(Country $country): RiskScore
    {
        // 1. Weather Risk
        $weather = $this->weatherService->getWeather($country->latitude, $country->longitude);
        $weatherRisk = $this->weatherService->calculateWeatherRisk($weather);

        // 2. Inflation Risk (pakai data World Bank kalau ada, fallback ke data tersimpan di countries)
        $inflation = $this->worldBankService->getInflation($country->iso3) ?? $country->inflation_rate;
        $inflationRisk = $this->worldBankService->calculateInflationRisk($inflation);

        // 3. Currency Risk
        $currencyRisk = $this->exchangeRateService->getVolatility($country->currency_code);

        // 4. News Risk
        $articles = $this->newsService->search($country->name, 10);
        $newsRisk = $this->newsService->calculateNewsRisk($articles);

        // Simpan artikel ke news_cache (opsional, biar bisa ditampilkan di News Intelligence dashboard)
        $this->cacheNewsArticles($country, $articles);

        // Total Risk (weighted)
        $totalRisk = round(
            ($weatherRisk * $this->weatherWeight) +
            ($inflationRisk * $this->inflationWeight) +
            ($currencyRisk * $this->currencyWeight) +
            ($newsRisk * $this->newsWeight),
            2
        );

        $riskLevel = $this->determineRiskLevel($totalRisk);

        return RiskScore::create([
            'country_id' => $country->id,
            'weather_risk' => $weatherRisk,
            'inflation_risk' => $inflationRisk,
            'currency_risk' => $currencyRisk,
            'news_risk' => $newsRisk,
            'total_risk' => $totalRisk,
            'risk_level' => $riskLevel,
            'calculated_at' => Carbon::now(),
        ]);
    }

    public function calculateForAllCountries(): array
    {
        $results = [];

        foreach (Country::all() as $country) {
            try {
                $results[] = $this->calculateForCountry($country);
            } catch (\Exception $e) {
                // Skip negara yang gagal (misal API down), lanjut ke negara berikutnya
                report($e);
                continue;
            }
        }

        return $results;
    }

    protected function determineRiskLevel(float $totalRisk): string
    {
        return match (true) {
            $totalRisk < 30 => 'low',
            $totalRisk < 60 => 'medium',
            default => 'high',
        };
    }

    protected function cacheNewsArticles(Country $country, array $articles): void
    {
        foreach ($articles as $article) {
            $text = ($article['title'] ?? '') . ' ' . ($article['description'] ?? '');
            $analysis = $this->newsService->analyzeSentiment($text);
            $category = $this->newsService->classifyCategory($text);

            $country->newsCache()->updateOrCreate(
                ['url' => $article['url'] ?? null],
                [
                    'title' => $article['title'] ?? 'No title',
                    'description' => $article['description'] ?? null,
                    'source' => $article['source']['name'] ?? null,
                    'sentiment' => $analysis['sentiment'],
                    'category' => $category,
                    'positive_score' => $analysis['positiveScore'],
                    'negative_score' => $analysis['negativeScore'],
                    'published_at' => $article['publishedAt'] ?? null,
                ]
            );
        }
    }
}