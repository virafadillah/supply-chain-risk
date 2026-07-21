<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Country Comparison Engine
        </h2>
    </x-slot>

    <style>
        .compare-hero {
            background: linear-gradient(135deg, #3b5bdb 0%, #2f4bc0 100%);
            border-radius: 16px;
            padding: 28px 24px;
            color: #fff;
            margin-bottom: 28px;
        }
        .compare-hero h4 { font-weight: 700; margin-bottom: 4px; }
        .compare-hero p { opacity: 0.85; margin-bottom: 0; font-size: 0.92rem; }

        .picker-row { position: relative; }
        .picker-card {
            background: #fff;
            border: 1.5px solid #e5e9f5;
            border-radius: 14px;
            padding: 18px;
            transition: border-color .15s ease, box-shadow .15s ease;
        }
        .picker-card:focus-within {
            border-color: #3b5bdb;
            box-shadow: 0 0 0 4px rgba(59,91,219,0.12);
        }
        .picker-card label {
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: .04em;
            color: #7a85a3;
            font-weight: 700;
            margin-bottom: 6px;
            display: block;
        }
        .picker-card select {
            border: none;
            padding: 0;
            font-size: 1.05rem;
            font-weight: 600;
            color: #1f2937;
            background: transparent;
            width: 100%;
        }
        .picker-card select:focus { outline: none; box-shadow: none; }

        .vs-badge {
            width: 52px;
            height: 52px;
            background: #fff;
            border: 3px solid #3b5bdb;
            color: #3b5bdb;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 800;
            font-size: 0.95rem;
            margin: 0 auto;
            box-shadow: 0 4px 10px rgba(59,91,219,0.18);
        }

        .empty-compare {
            text-align: center;
            padding: 56px 20px;
            border: 1.5px dashed #d7ddf0;
            border-radius: 16px;
            background: #f8f9fd;
        }
        .empty-compare .icon-circle {
            width: 72px;
            height: 72px;
            border-radius: 50%;
            background: rgba(59,91,219,0.1);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 18px;
        }
        .empty-compare h5 { font-weight: 700; color: #1f2937; margin-bottom: 6px; }
        .empty-compare p { color: #8a91a8; font-size: 0.9rem; max-width: 380px; margin: 0 auto; }
        .quick-chip {
            border: 1.5px solid #e5e9f5;
            background: #fff;
            color: #3b5bdb;
            border-radius: 999px;
            padding: 6px 16px;
            font-size: 0.85rem;
            font-weight: 600;
            margin: 4px;
            transition: all .15s ease;
        }
        .quick-chip:hover {
            background: #3b5bdb;
            color: #fff;
            border-color: #3b5bdb;
        }
    </style>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <div class="compare-hero">
                    <h4>⚖️ Bandingkan Risiko Dua Negara</h4>
                    <p>Pilih dua negara untuk melihat perbandingan skor risiko, cuaca, inflasi, kurs, dan berita secara berdampingan.</p>
                </div>

                <form method="GET" action="{{ route('compare') }}" class="mb-4">
                    <div class="row g-3 align-items-center picker-row">
                        <div class="col-md-5">
                            <div class="picker-card">
                                <label for="country_a">Negara Pertama</label>
                                <select id="country_a" name="country_a" class="form-select" onchange="this.form.submit()">
                                    <option value="">-- Pilih Negara --</option>
                                    @foreach ($countries as $country)
                                        <option value="{{ $country->id }}" {{ request('country_a') == $country->id ? 'selected' : '' }}>
                                            {{ $country->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="col-md-2">
                            <div class="vs-badge">VS</div>
                        </div>
                        <div class="col-md-5">
                            <div class="picker-card">
                                <label for="country_b">Negara Kedua</label>
                                <select id="country_b" name="country_b" class="form-select" onchange="this.form.submit()">
                                    <option value="">-- Pilih Negara --</option>
                                    @foreach ($countries as $country)
                                        <option value="{{ $country->id }}" {{ request('country_b') == $country->id ? 'selected' : '' }}>
                                            {{ $country->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
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
                    <div class="empty-compare">
                        <div class="icon-circle">
                            <svg width="34" height="34" viewBox="0 0 24 24" fill="none" stroke="#3b5bdb" stroke-width="1.8">
                                <circle cx="11" cy="11" r="7"/>
                                <line x1="21" y1="21" x2="16.65" y2="16.65"/>
                            </svg>
                        </div>
                        <h5>Belum Ada Negara Dipilih</h5>
                        <p>Gunakan dropdown di atas untuk memilih 2 negara. Perbandingan skor risiko, cuaca, kurs, dan berita akan muncul otomatis di sini.</p>

                        @if($countries->count() > 0)
                        <div class="mt-4">
                            <p class="text-muted small mb-2">Coba mulai dengan:</p>
                            @foreach ($countries->take(5) as $c)
                                <a href="{{ route('compare', ['country_a' => $c->id]) }}" class="quick-chip d-inline-block text-decoration-none">
                                    {{ $c->name }}
                                </a>
                            @endforeach
                        </div>
                        @endif
                    </div>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>