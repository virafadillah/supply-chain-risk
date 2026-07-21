<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Country::with('latestRiskScore')->orderBy('name');

        if ($request->filled('region')) {
            $query->where('region', $request->input('region'));
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->input('search') . '%');
        }

        $countries = $query->get()->map(function (Country $country) {
            return $this->transform($country);
        });

        return response()->json([
            'total' => $countries->count(),
            'data' => $countries,
        ]);
    }

    public function show(Country $country): JsonResponse
    {
        $country->load(['latestRiskScore', 'ports']);

        return response()->json([
            'data' => $this->transform($country, detailed: true),
        ]);
    }

    protected function transform(Country $country, bool $detailed = false): array
    {
        $data = [
            'id' => $country->id,
            'name' => $country->name,
            'iso2' => $country->iso2,
            'iso3' => $country->iso3,
            'capital' => $country->capital,
            'region' => $country->region,
            'currency_code' => $country->currency_code,
            'currency_name' => $country->currency_name,
            'latitude' => $country->latitude,
            'longitude' => $country->longitude,
            'population' => $country->population,
            'gdp' => $country->gdp,
            'inflation_rate' => $country->inflation_rate,
            'risk_level' => $country->latestRiskScore->risk_level ?? null,
            'total_risk' => $country->latestRiskScore->total_risk ?? null,
        ];

        if ($detailed) {
            $data['ports'] = $country->ports->map(fn ($port) => [
                'id' => $port->id,
                'name' => $port->name,
                'unlocode' => $port->unlocode,
            ]);
        }

        return $data;
    }
}