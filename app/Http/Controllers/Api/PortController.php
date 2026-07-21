<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Port;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PortController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = Port::with('country.latestRiskScore')->orderBy('name');

        if ($request->filled('country')) {
            $query->whereHas('country', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->input('country') . '%');
            });
        }

        if ($request->filled('search')) {
            $query->where('name', 'like', '%' . $request->input('search') . '%');
        }

        $ports = $query->get()->map(fn (Port $port) => $this->transform($port));

        return response()->json([
            'total' => $ports->count(),
            'data' => $ports,
        ]);
    }

    public function show(Port $port): JsonResponse
    {
        $port->load('country.latestRiskScore');

        return response()->json([
            'data' => $this->transform($port),
        ]);
    }

    protected function transform(Port $port): array
    {
        return [
            'id' => $port->id,
            'name' => $port->name,
            'unlocode' => $port->unlocode,
            'port_type' => $port->port_type,
            'latitude' => $port->latitude,
            'longitude' => $port->longitude,
            'country' => [
                'id' => $port->country->id,
                'name' => $port->country->name,
                'iso3' => $port->country->iso3,
                'risk_level' => $port->country->latestRiskScore->risk_level ?? null,
            ],
        ];
    }
}