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
        /*
        |--------------------------------------------------------------------------
        | WEATHER RISK
        |--------------------------------------------------------------------------
        */

        $weather = $this->weatherService->getWeather(
            $country->latitude,
            $country->longitude
        );

        $weatherRisk = $this->weatherService->calculateWeatherRisk($weather);

        /*
        |--------------------------------------------------------------------------
        | INFLATION RISK
        |--------------------------------------------------------------------------
        */

        $inflation = $this->worldBankService->getInflation($country->iso3)
            ?? $country->inflation_rate;

        $inflationRisk = $this->worldBankService
            ->calculateInflationRisk($inflation);

        /*
        |--------------------------------------------------------------------------
        | CURRENCY RISK
        |--------------------------------------------------------------------------
        */

        $currencyRisk = $this->exchangeRateService
            ->getVolatility($country->currency_code);

        /*
        |--------------------------------------------------------------------------
        | NEWS RISK
        |--------------------------------------------------------------------------
        */

        $articles = $this->newsService->search(
            $country->name,
            10
        );

        $newsRisk = $this->newsService
            ->calculateNewsRisk($articles);

        /*
        |--------------------------------------------------------------------------
        | CACHE NEWS
        |--------------------------------------------------------------------------
        */

        $this->cacheNewsArticles(
            $country,
            $articles
        );

        /*
        |--------------------------------------------------------------------------
        | TOTAL RISK
        |--------------------------------------------------------------------------
        */

        $totalRisk = round(

            ($weatherRisk * $this->weatherWeight)

            +

            ($inflationRisk * $this->inflationWeight)

            +

            ($currencyRisk * $this->currencyWeight)

            +

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

        foreach (Country::orderBy('name')->get() as $country) {

            try {

                $results[] = $this->calculateForCountry($country);

            } catch (\Throwable $e) {

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

    /**
     * Simpan berita ke news_cache
     */
    protected function cacheNewsArticles(
        Country $country,
        array $articles
    ): void
    {

        foreach ($articles as $article) {

            $title = trim($article['title'] ?? '');

            $description = trim($article['description'] ?? '');

            $text = strtolower($title . ' ' . $description);

            if ($title == '') {
                continue;
            }

            $analysis = $this->newsService
                ->analyzeSentiment($title . ' ' . $description);

            $category = $this->newsService
                ->classifyCategory($title . ' ' . $description);
                            /*
            |--------------------------------------------------------------------------
            | Skip berita yang tidak berkaitan dengan negara
            |--------------------------------------------------------------------------
            */

            $countryName = strtolower($country->name);

            if (
                !str_contains($text, $countryName) &&
                !str_contains(strtolower($title), $countryName)
            ) {
                continue;
            }

            /*
            |--------------------------------------------------------------------------
            | Simpan / Update berita
            |--------------------------------------------------------------------------
            */

            $country->newsCache()->updateOrCreate(

                [
                    'country_id' => $country->id,
                    'url' => $article['url'] ?? '',
                ],

                [

                    'title' => $title,

                    'description' => $description,

                    'image' => $article['image'] ?? null,

                    'source' => $article['source']['name'] ?? 'Unknown',

                    'sentiment' => $analysis['sentiment'],

                    'category' => $category,

                    'positive_score' => $analysis['positiveScore'],

                    'negative_score' => $analysis['negativeScore'],

                    'published_at' => !empty($article['publishedAt'])
                        ? Carbon::parse($article['publishedAt'])
                        : now(),

                ]

            );

        }

    }
    }