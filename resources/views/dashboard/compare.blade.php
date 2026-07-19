<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Country Comparison Engine
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <form method="GET" action="{{ route('compare') }}" class="row g-3 mb-4 align-items-end">
                    <div class="col-md-5">
                        <label class="form-label fw-semibold">Negara 1</label>
                        <select name="country_a" class="form-select" onchange="this.form.submit()">
                            <option value="">-- Pilih Negara --</option>
                            @foreach ($countries as $country)
                                <option value="{{ $country->id }}" {{ request('country_a') == $country->id ? 'selected' : '' }}>
                                    {{ $country->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-2 d-flex align-items-center justify-content-center">
                        <span class="fw-bold fs-5 text-muted">VS</span>
                    </div>
                    <div class="col-md-5">
                        <label class="form-label fw-semibold">Negara 2</label>
                        <select name="country_b" class="form-select" onchange="this.form.submit()">
                            <option value="">-- Pilih Negara --</option>
                            @foreach ($countries as $country)
                                <option value="{{ $country->id }}" {{ request('country_b') == $country->id ? 'selected' : '' }}>
                                    {{ $country->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>

                @if ($countryA && $countryB)
                    @php
                        $riskA = $countryA->latestRiskScore;
                        $riskB = $countryB->latestRiskScore;

                        $totalA = $riskA->total_risk ?? null;
                        $totalB = $riskB->total_risk ?? null;

                        $badgeClass = fn($level) => match($level) {
                            'low' => 'bg-success',
                            'medium' => 'bg-warning text-dark',
                            'high' => 'bg-danger',
                            default => 'bg-secondary',
                        };

                        $compare = function ($valA, $valB, $nameA, $nameB) {
                            if ($valA === null || $valB === null) return '-';
                            if ((float) $valA == (float) $valB) return 'Seri';
                            return ((float) $valA < (float) $valB) ? $nameA : $nameB;
                        };

                        $rows = [
                            ['label' => 'Capital', 'a' => $countryA->capital, 'b' => $countryB->capital, 'compare' => null],
                            ['label' => 'Region', 'a' => $countryA->region, 'b' => $countryB->region, 'compare' => null],
                            ['label' => 'Currency', 'a' => $countryA->currency_code, 'b' => $countryB->currency_code, 'compare' => null],
                            ['label' => 'Population', 'a' => $countryA->population ? number_format($countryA->population) : '-', 'b' => $countryB->population ? number_format($countryB->population) : '-', 'compare' => null],
                            ['label' => 'GDP', 'a' => $countryA->gdp ? number_format($countryA->gdp) : '-', 'b' => $countryB->gdp ? number_format($countryB->gdp) : '-', 'compare' => null],
                            ['label' => 'Inflation Rate', 'a' => $countryA->inflation_rate ?? '-', 'b' => $countryB->inflation_rate ?? '-',
                                'compare' => $compare($countryA->inflation_rate, $countryB->inflation_rate, $countryA->name, $countryB->name)],
                            ['label' => 'Weather Risk', 'a' => $riskA->weather_risk ?? '-', 'b' => $riskB->weather_risk ?? '-',
                                'compare' => $compare($riskA->weather_risk ?? null, $riskB->weather_risk ?? null, $countryA->name, $countryB->name)],
                            ['label' => 'Inflation Risk', 'a' => $riskA->inflation_risk ?? '-', 'b' => $riskB->inflation_risk ?? '-',
                                'compare' => $compare($riskA->inflation_risk ?? null, $riskB->inflation_risk ?? null, $countryA->name, $countryB->name)],
                            ['label' => 'Currency Risk', 'a' => $riskA->currency_risk ?? '-', 'b' => $riskB->currency_risk ?? '-',
                                'compare' => $compare($riskA->currency_risk ?? null, $riskB->currency_risk ?? null, $countryA->name, $countryB->name)],
                            ['label' => 'News Risk', 'a' => $riskA->news_risk ?? '-', 'b' => $riskB->news_risk ?? '-',
                                'compare' => $compare($riskA->news_risk ?? null, $riskB->news_risk ?? null, $countryA->name, $countryB->name)],
                        ];

                        $totalCompare = $compare($totalA, $totalB, $countryA->name, $countryB->name);
                    @endphp

                    {{-- Kartu skor besar --}}
                    <div class="row g-3 align-items-center mb-3">
                        <div class="col-md-5">
                            <div class="stat-card text-center">
                                <div class="text-muted mb-1">📍</div>
                                <p class="fw-semibold mb-1">{{ $countryA->name }}</p>
                                <div class="stat-value" style="font-size: 2.2rem;">{{ $totalA ?? 'N/A' }}</div>
                                <span class="badge {{ $badgeClass($riskA->risk_level ?? null) }}">
                                    {{ $riskA ? ucfirst($riskA->risk_level) . ' Risk' : 'N/A' }}
                                </span>
                            </div>
                        </div>
                        <div class="col-md-2 text-center text-muted fw-semibold">vs</div>
                        <div class="col-md-5">
                            <div class="stat-card text-center">
                                <div class="text-muted mb-1">📍</div>
                                <p class="fw-semibold mb-1">{{ $countryB->name }}</p>
                                <div class="stat-value" style="font-size: 2.2rem;">{{ $totalB ?? 'N/A' }}</div>
                                <span class="badge {{ $badgeClass($riskB->risk_level ?? null) }}">
                                    {{ $riskB ? ucfirst($riskB->risk_level) . ' Risk' : 'N/A' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    @if ($totalCompare !== '-' && $totalCompare !== 'Seri')
                        <div class="text-center rounded-3 py-2 mb-4" style="background: rgba(59,91,219,0.08); color: #2f4bc0;">
                            <strong>{{ $totalCompare }}</strong> lebih aman untuk saat ini
                        </div>
                    @endif

                    <h5 class="mb-3">Perbandingan Detail</h5>
                    <div class="table-responsive mb-4">
                        <table class="table table-bordered align-middle text-center">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 22%;" class="text-start">Indikator</th>
                                    <th>{{ $countryA->name }}</th>
                                    <th>{{ $countryB->name }}</th>
                                    <th style="width: 18%;">Lebih Baik</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($rows as $row)
                                    <tr>
                                        <td class="fw-semibold text-start">{{ $row['label'] }}</td>
                                        <td>{{ $row['a'] }}</td>
                                        <td>{{ $row['b'] }}</td>
                                        <td>
                                            @if ($row['compare'] === null)
                                                <span class="text-muted">—</span>
                                            @elseif ($row['compare'] === 'Seri')
                                                <span class="text-muted">Seri</span>
                                            @else
                                                <span class="badge" style="background: rgba(59,91,219,0.12); color: #2f4bc0;">{{ $row['compare'] }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                @endforeach

                                <tr class="table-warning">
                                    <td class="fw-semibold text-start">Total Risk Score</td>
                                    <td class="fw-bold">{{ $totalA ?? '-' }} ({{ ucfirst($riskA->risk_level ?? 'N/A') }})</td>
                                    <td class="fw-bold">{{ $totalB ?? '-' }} ({{ ucfirst($riskB->risk_level ?? 'N/A') }})</td>
                                    <td>
                                        @if ($totalCompare === 'Seri' || $totalCompare === '-')
                                            <span class="text-muted">{{ $totalCompare }}</span>
                                        @else
                                            <span class="badge" style="background:#3b5bdb; color:#fff;">{{ $totalCompare }}</span>
                                        @endif
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <h5 class="mb-3">Visual Comparison</h5>
                    <div style="position: relative; height: 350px;">
                        <canvas id="compareChart"></canvas>
                    </div>

                    @push('scripts')
                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                            new Chart(document.getElementById('compareChart'), {
                                type: 'radar',
                                data: {
                                    labels: ['Weather Risk', 'Inflation Risk', 'Currency Risk', 'News Risk', 'Total Risk'],
                                    datasets: [
                                        {
                                            label: '{{ $countryA->name }}',
                                            data: [
                                                {{ $riskA->weather_risk ?? 0 }},
                                                {{ $riskA->inflation_risk ?? 0 }},
                                                {{ $riskA->currency_risk ?? 0 }},
                                                {{ $riskA->news_risk ?? 0 }},
                                                {{ $riskA->total_risk ?? 0 }},
                                            ],
                                            borderColor: '#3b5bdb',
                                            backgroundColor: 'rgba(59, 91, 219, 0.15)',
                                        },
                                        {
                                            label: '{{ $countryB->name }}',
                                            data: [
                                                {{ $riskB->weather_risk ?? 0 }},
                                                {{ $riskB->inflation_risk ?? 0 }},
                                                {{ $riskB->currency_risk ?? 0 }},
                                                {{ $riskB->news_risk ?? 0 }},
                                                {{ $riskB->total_risk ?? 0 }},
                                            ],
                                            borderColor: '#7ba1e8',
                                            backgroundColor: 'rgba(123, 161, 232, 0.15)',
                                        },
                                    ],
                                },
                                options: {
                                    responsive: true,
                                    maintainAspectRatio: false,
                                    scales: {
                                        r: { beginAtZero: true, max: 100 },
                                    },
                                },
                            });
                        });
                    </script>
                    @endpush
                @else
                    <p class="text-muted">Pilih 2 negara di atas untuk membandingkan.</p>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>