<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Services\ExchangeRateService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CurrencyController extends Controller
{
    protected ExchangeRateService $exchangeRateService;

    public function __construct(ExchangeRateService $exchangeRateService)
    {
        $this->exchangeRateService = $exchangeRateService;
    }

    public function index(Request $request): JsonResponse
    {
        $base = strtoupper($request->input('base', 'USD'));
        $rates = $this->exchangeRateService->getRate($base);

        $countries = Country::whereNotNull('currency_code')->get(['name', 'currency_code']);

        $data = $countries->map(function (Country $country) use ($rates, $base) {
            return [
                'country' => $country->name,
                'currency_code' => $country->currency_code,
                'rate' => $rates[$country->currency_code] ?? null,
                'base' => $base,
            ];
        })->unique('currency_code')->values();

        return response()->json([
            'base' => $base,
            'total' => $data->count(),
            'data' => $data,
        ]);
    }

    public function show(Country $country): JsonResponse
    {
        if (!$country->currency_code) {
            return response()->json(['message' => 'Negara ini tidak memiliki data mata uang.'], 404);
        }

        $rates = $this->exchangeRateService->getRate('USD');
        $volatility = $this->exchangeRateService->getVolatility($country->currency_code);

        return response()->json([
            'data' => [
                'country' => $country->name,
                'currency_code' => $country->currency_code,
                'currency_name' => $country->currency_name,
                'rate_to_usd' => $rates[$country->currency_code] ?? null,
                'volatility_risk' => $volatility,
            ],
        ]);
    }
}