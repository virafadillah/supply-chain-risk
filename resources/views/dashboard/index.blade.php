<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Global Country Dashboard
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <p class="text-gray-600 mb-4">Pilih negara untuk melihat detail risiko rantai pasok:</p>

                <div class="row g-3">
                    @foreach ($countries as $country)
                        @php
                            $latestRisk = $country->riskScores()->latest('calculated_at')->first();
                            $badgeClass = match($latestRisk?->risk_level) {
                                'low' => 'bg-success',
                                'medium' => 'bg-warning text-dark',
                                'high' => 'bg-danger',
                                default => 'bg-secondary',
                            };
                            $isWatchlisted = auth()->user()->watchlists->contains('country_id', $country->id);
                        @endphp
                        <div class="col-md-4 col-sm-6">
                            <div class="card h-100 shadow-sm border-0">
                                <div class="card-body">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <h5 class="card-title mb-0 text-dark">
                                            <a href="{{ route('dashboard.show', $country) }}" class="text-decoration-none text-dark">
                                                {{ $country->name }}
                                            </a>
                                        </h5>
                                        <span class="badge {{ $badgeClass }}">
                                            {{ $latestRisk ? ucfirst($latestRisk->risk_level) : 'N/A' }}
                                        </span>
                                    </div>
                                    <p class="text-muted small mb-1">{{ $country->capital }} &bull; {{ $country->region }}</p>
                                    <p class="text-muted small mb-3">
                                        Currency: {{ $country->currency_code }}
                                        @if($latestRisk)
                                            &bull; Risk Score: <strong>{{ $latestRisk->total_risk }}</strong>
                                        @endif
                                    </p>

                                    <form action="{{ route('watchlist.toggle', $country) }}" method="POST">
                                        @csrf
                                        <button type="submit" class="btn btn-sm w-100 {{ $isWatchlisted ? 'btn-warning' : 'btn-outline-primary' }}">
                                            {{ $isWatchlisted ? '★ Watchlisted' : '☆ Tambah ke Watchlist' }}
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <hr class="my-4">

                <h5 class="mb-3">Risk Comparison — Top 10 Negara Berisiko Tertinggi</h5>
                <div style="position: relative; height: 420px;">
                    <canvas id="riskComparisonChart"></canvas>
                </div>

            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            fetch('{{ route('chart.risk-comparison') }}')
                .then(res => res.json())
                .then(data => {
                    const colors = ['#2a78d6', '#eda100', '#1baf7a', '#e34948'];

                    new Chart(document.getElementById('riskComparisonChart'), {
                        type: 'bar',
                        data: {
                            labels: data.labels,
                            datasets: data.datasets.map((ds, i) => ({
                                label: ds.label,
                                data: ds.data,
                                backgroundColor: colors[i],
                                borderRadius: 3,
                            })),
                        },
                        options: {
                            indexAxis: 'y',
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                x: { stacked: true, beginAtZero: true, title: { display: true, text: 'Risk Score' } },
                                y: { stacked: true },
                            },
                            plugins: {
                                legend: { position: 'bottom' },
                            },
                        },
                    });
                });
        });
    </script>
    @endpush
</x-app-layout>