<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Port;
use App\Services\OpenMeteoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\View\View;

class DashboardController extends Controller
{
    protected OpenMeteoService $weatherService;

    public function __construct(OpenMeteoService $weatherService)
    {
        $this->weatherService = $weatherService;
    }

    public function index(): View
    {
        $countries = Country::orderBy('name')->get();
        auth()->user()->load('watchlists');

        return view('dashboard.index', compact('countries'));
    }

    public function show(Country $country): View
    {
        $country->load(['latestRiskScore', 'ports', 'newsCache' => function ($query) {
            $query->latest('published_at')->limit(5);
        }]);

        return view('dashboard.show', compact('country'));
    }

    public function map(): View
    {
        return view('dashboard.map');
    }

    public function compare(Request $request): View
    {
        $countries = Country::orderBy('name')->get();

        $countryA = null;
        $countryB = null;

        if ($request->filled('country_a')) {
            $countryA = Country::with('latestRiskScore')->find($request->country_a);
        }

        if ($request->filled('country_b')) {
            $countryB = Country::with('latestRiskScore')->find($request->country_b);
        }

        return view('dashboard.compare', compact('countries', 'countryA', 'countryB'));
    }

    public function currency(): View
    {
        $countries = Country::orderBy('name')->get();
        return view('dashboard.currency', compact('countries'));
    }

    public function news(): View
    {
        $news = \App\Models\NewsCache::with('country')->latest('published_at')->paginate(15);
        return view('dashboard.news', compact('news'));
    }

    public function portsList(): View
    {
        $ports = Port::with('country.latestRiskScore')->orderBy('name')->get();
        return view('dashboard.ports', compact('ports'));
    }

    public function searchCountry(Request $request): RedirectResponse
    {
        $country = Country::where('name', 'like', '%' . $request->input('name') . '%')->first();

        if (!$country) {
            return back()->with('error', 'Negara tidak ditemukan.');
        }

        return redirect()->route('dashboard.show', $country);
    }

    public function riskComparisonData(): JsonResponse
    {
        $countries = Country::with('latestRiskScore')
            ->get()
            ->filter(fn($c) => $c->latestRiskScore !== null)
            ->sortByDesc(fn($c) => $c->latestRiskScore->total_risk ?? 0)
            ->take(10)
            ->values();

        return response()->json([
            'labels' => $countries->pluck('name'),
            'datasets' => [
                [
                    'label' => 'Weather Risk',
                    'data' => $countries->map(fn($c) => $c->latestRiskScore->weather_risk ?? 0),
                ],
                [
                    'label' => 'Inflation Risk',
                    'data' => $countries->map(fn($c) => $c->latestRiskScore->inflation_risk ?? 0),
                ],
                [
                    'label' => 'Currency Risk',
                    'data' => $countries->map(fn($c) => $c->latestRiskScore->currency_risk ?? 0),
                ],
                [
                    'label' => 'News Risk',
                    'data' => $countries->map(fn($c) => $c->latestRiskScore->news_risk ?? 0),
                ],
            ],
        ]);
    }

    public function riskHistoryData(Country $country): JsonResponse
    {
        $history = $country->riskScores()
            ->orderBy('calculated_at')
            ->get(['total_risk', 'calculated_at']);

        return response()->json([
            'labels' => $history->map(fn($h) => $h->calculated_at->format('d M H:i')),
            'data' => $history->pluck('total_risk'),
        ]);
    }

    public function mapData(): JsonResponse
    {
        $ports = Port::with('country.latestRiskScore')->get();

        $portMarkers = $ports->map(function ($port) {
            return [
                'type' => 'port',
                'name' => $port->name,
                'unlocode' => $port->unlocode,
                'country' => $port->country->name,
                'lat' => $port->latitude,
                'lng' => $port->longitude,
                'port_type' => $port->port_type,
                'risk_level' => $port->country->latestRiskScore->risk_level ?? 'unknown',
                'total_risk' => $port->country->latestRiskScore->total_risk ?? null,
            ];
        });

        $countries = Country::with('latestRiskScore')
            ->whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();

        $countryMarkers = $countries->map(function ($country) {
            return [
                'type' => 'country',
                'name' => $country->name,
                'unlocode' => null,
                'country' => $country->name,
                'lat' => $country->latitude,
                'lng' => $country->longitude,
                'port_type' => null,
                'risk_level' => $country->latestRiskScore->risk_level ?? 'unknown',
                'total_risk' => $country->latestRiskScore->total_risk ?? null,
            ];
        });

        return response()->json($portMarkers->merge($countryMarkers));
    }

    // GET /chart-data/weather-map
    // Data cuaca real-time per negara (pakai cache + paralel fetch biar cepat)
    public function weatherMapData(): JsonResponse
    {
        $countries = Country::whereNotNull('latitude')
            ->whereNotNull('longitude')
            ->get();

        $needFetch = [];
        $weatherData = [];

        foreach ($countries as $country) {
            $cached = Cache::get("weather_country_{$country->id}");
            if ($cached) {
                $weatherData[$country->id] = $cached;
            } else {
                $needFetch[] = $country;
            }
        }

        if (count($needFetch) > 0) {
            $responses = Http::pool(function ($pool) use ($needFetch) {
                foreach ($needFetch as $country) {
                    $pool->as((string) $country->id)
                        ->timeout(15)
                        ->get('https://api.open-meteo.com/v1/forecast', [
                            'latitude' => $country->latitude,
                            'longitude' => $country->longitude,
                            'current' => 'temperature_2m,wind_speed_10m,precipitation',
                            'timezone' => 'auto',
                        ]);
                }
            });

            foreach ($needFetch as $country) {
                $response = $responses[(string) $country->id] ?? null;

                // Hasil pool bisa berupa Response (sukses/gagal HTTP) ATAU ConnectionException
                // (gagal konek total). Cek instanceof dulu sebelum panggil ->successful().
                $data = ($response instanceof \Illuminate\Http\Client\Response && $response->successful())
                    ? $response->json()
                    : [];

                $weather = [
                    'temperature' => $data['current']['temperature_2m'] ?? null,
                    'wind_speed' => $data['current']['wind_speed_10m'] ?? null,
                    'precipitation' => $data['current']['precipitation'] ?? null,
                ];

                Cache::put("weather_country_{$country->id}", $weather, 1800);
                $weatherData[$country->id] = $weather;
            }
        }

        $markers = $countries->map(function (Country $country) use ($weatherData) {
            $weather = $weatherData[$country->id] ?? ['temperature' => null, 'wind_speed' => null, 'precipitation' => null];

            return [
                'name' => $country->name,
                'lat' => $country->latitude,
                'lng' => $country->longitude,
                'temperature' => $weather['temperature'],
                'wind_speed' => $weather['wind_speed'],
                'precipitation' => $weather['precipitation'],
                'condition' => $this->classifyWeatherCondition($weather),
            ];
        });

        return response()->json($markers);
    }

    protected function classifyWeatherCondition(array $weather): string
    {
        $wind = $weather['wind_speed'] ?? 0;
        $rain = $weather['precipitation'] ?? 0;

        if ($wind >= 60) {
            return 'badai';
        }

        if ($rain >= 10) {
            return 'hujan_lebat';
        }

        if ($wind >= 30) {
            return 'angin_kencang';
        }

        if ($rain > 0) {
            return 'hujan_ringan';
        }

        return 'cerah';
    }
}