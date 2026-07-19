<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Services\RiskScoringService;
use Illuminate\Http\JsonResponse;

class RiskScoreController extends Controller
{
    protected RiskScoringService $riskScoringService;

    public function __construct(RiskScoringService $riskScoringService)
    {
        $this->riskScoringService = $riskScoringService;
    }

    // GET /api/risk/{country}
    public function calculate(Country $country): JsonResponse
    {
        $riskScore = $this->riskScoringService->calculateForCountry($country);

        return response()->json([
            'country' => $country->name,
            'risk_score' => $riskScore,
        ]);
    }

    // GET /api/risk (semua negara)
    public function calculateAll(): JsonResponse
    {
        $results = $this->riskScoringService->calculateForAllCountries();

        return response()->json([
            'total_countries' => count($results),
            'results' => $results,
        ]);
    }
}