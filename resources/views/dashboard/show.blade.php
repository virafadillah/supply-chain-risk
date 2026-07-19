<x-app-layout>
    @php
        $risk = $country->latestRiskScore;
        $riskClass = match($risk?->risk_level) {
            'low' => 'low', 'medium' => 'medium', 'high' => 'high', default => 'low',
        };
        $isWatchlisted = auth()->user()->watchlists()->where('country_id', $country->id)->exists();

        $flagCode = $country->code ? strtolower($country->code) : null;
    @endphp

    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
        <div class="d-flex align-items-center gap-3">
            @if($flagCode)
                <span class="fi fi-{{ $flagCode }}" style="font-size: 2.8rem; width: 1.5em; border-radius: 6px; box-shadow: 0 1px 4px rgba(0,0,0,0.15);"></span>
            @else
                <div style="font-size: 2.5rem;">🏳️</div>
            @endif
            <div>
                <h4 class="mb-1">{{ $country->name }}</h4>
                <p class="text-muted mb-0">{{ $country->capital }} &bull; {{ $country->region }} &bull; {{ $country->currency_code }}</p>
            </div>
        </div>
        <div class="d-flex gap-2">
            <span class="risk-badge {{ $riskClass }}">● {{ ucfirst($risk->risk_level ?? 'N/A') }} Risk</span>
            <form action="{{ route('watchlist.toggle', $country) }}" method="POST">
                @csrf
                <button class="btn btn-sm {{ $isWatchlisted ? 'btn-warning' : 'btn-outline-secondary' }}">
                    {{ $isWatchlisted ? '★ Watchlisted' : '☆ Watchlist' }}
                </button>
            </form>
        </div>
    </div>

    <div class="row g-3 mt-2">
        <div class="col-md-3 col-6">
            <div class="stat-card" style="border-top: 3px solid #3b5bdb;">
                <div class="stat-label">
                    <span class="stat-icon" style="background: #e6ecfb; color: #2f4bc0;">📈</span> GDP
                </div>
                <div class="stat-value">{{ $country->gdp ? '$' . number_format($country->gdp / 1e9, 2) . 'B' : '-' }}</div>
                <div class="stat-sub">Data terbaru</div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="stat-card" style="border-top: 3px solid #f0b429;">
                <div class="stat-label">
                    <span class="stat-icon" style="background: #fdf3d8; color: #b8860b;">%</span> Inflasi
                </div>
                <div class="stat-value">{{ $country->inflation_rate ?? '-' }}%</div>
                <div class="stat-sub">Annual %</div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="stat-card" style="border-top: 3px solid #6b9bd8;">
                <div class="stat-label">
                    <span class="stat-icon" style="background: #e3edfb; color: #3b6bc9;">🌡️</span> Suhu
                </div>
                <div class="stat-value" id="tempValue">-</div>
                <div class="stat-sub" id="weatherDesc">Loading...</div>
            </div>
        </div>
        <div class="col-md-3 col-6">
            <div class="stat-card" style="border-top: 3px solid #3b5bdb;">
                <div class="stat-label">
                    <span class="stat-icon" style="background: #e6ecfb; color: #2f4bc0;">💱</span> Kurs (vs USD)
                </div>
                <div class="stat-value">{{ $country->currency_code }}</div>
                <div class="stat-sub">Live rate</div>
            </div>
        </div>
    </div>

    <div class="row g-3 mt-1">
        <div class="col-md-4">
            <div class="stat-card text-center">
                <div class="stat-label mb-2">⚠ RISK SCORE</div>
                <div style="max-width: 220px; margin: 0 auto;">
                    <canvas id="gaugeChart"></canvas>
                </div>
                <div class="stat-value" id="riskScoreValue" style="font-size: 2rem;">{{ $risk->total_risk ?? '-' }}</div>
                <div class="text-muted small" id="riskLevelLabel">Risk Level: {{ ucfirst($risk->risk_level ?? 'N/A') }}</div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-label mb-3">📋 BREAKDOWN RISIKO</div>

                @php
                    $breakdown = [
                        ['label' => 'Risiko Cuaca & Badai', 'value' => $risk->weather_risk ?? 0, 'weight' => 30, 'color' => '#6b9bd8'],
                        ['label' => 'Risiko Inflasi', 'value' => $risk->inflation_risk ?? 0, 'weight' => 20, 'color' => '#22c55e'],
                        ['label' => 'Volatilitas Kurs', 'value' => $risk->currency_risk ?? 0, 'weight' => 10, 'color' => '#f0b429'],
                        ['label' => 'Sentimen Berita', 'value' => $risk->news_risk ?? 0, 'weight' => 40, 'color' => '#3b5bdb'],
                    ];
                @endphp

                @foreach ($breakdown as $item)
                    <div class="mb-3">
                        <div class="d-flex justify-content-between small mb-1">
                            <span>{{ $item['label'] }}</span>
                            <strong>{{ $item['value'] }}</strong>
                        </div>
                        <div class="progress-dark">
                            <div class="fill" style="width: {{ min($item['value'], 100) }}%; background: {{ $item['color'] }};"></div>
                        </div>
                        <div class="text-muted" style="font-size: .7rem;">
                            Bobot {{ $item['weight'] }}% &bull; Kontribusi {{ round($item['value'] * $item['weight'] / 100, 2) }}
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="col-md-4">
            <div class="stat-card">
                <div class="stat-label mb-3">☁️ DETAIL CUACA</div>
                <div class="row g-2">
                    <div class="col-6">
                        <div class="stat-card text-center py-3">
                            <div style="font-size: 1.3rem;">🌡️</div>
                            <div id="detailTemp" class="fw-bold">-</div>
                            <div class="stat-sub">Suhu</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="stat-card text-center py-3">
                            <div style="font-size: 1.3rem;">💨</div>
                            <div id="detailWind" class="fw-bold">-</div>
                            <div class="stat-sub">Angin</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="stat-card text-center py-3">
                            <div style="font-size: 1.3rem;">🌧️</div>
                            <div id="detailRain" class="fw-bold">-</div>
                            <div class="stat-sub">Curah Hujan</div>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="stat-card text-center py-3">
                            <div style="font-size: 1.3rem;">⚠️</div>
                            <div id="detailStorm" class="fw-bold">-</div>
                            <div class="stat-sub">Storm Risk</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-3 mt-1">
        <div class="col-md-6">
            <div class="stat-card" style="border-left: 4px solid #3b5bdb;">
                <div class="stat-label mb-3">⚓ PELABUHAN</div>
                @forelse ($country->ports as $port)
                    <div class="d-flex justify-content-between border-bottom py-2" style="border-color: var(--border-soft) !important;">
                        <span>{{ $port->name }}</span>
                        <span class="text-muted small">{{ $port->unlocode }} &bull; {{ ucfirst($port->port_type) }}</span>
                    </div>
                @empty
                    <p class="text-muted">Belum ada data pelabuhan.</p>
                @endforelse
            </div>
        </div>
        <div class="col-md-6">
            <div class="stat-card" style="border-left: 4px solid #3b5bdb;">
                <div class="stat-label mb-3">📰 BERITA TERKAIT</div>
                @forelse ($country->newsCache as $news)
                    @php
                        $sc = match($news->sentiment) { 'positive' => 'low', 'negative' => 'high', default => 'medium' };
                    @endphp
                    <div class="border-bottom py-2" style="border-color: var(--border-soft) !important;">
                        <div class="small">{{ Str::limit($news->title, 60) }}</div>
                        <span class="risk-badge {{ $sc }}" style="font-size:.65rem; padding:.15rem .5rem;">{{ ucfirst($news->sentiment ?? 'N/A') }}</span>
                    </div>
                @empty
                    <p class="text-muted">Belum ada berita ter-cache.</p>
                @endforelse
            </div>
        </div>
    </div>

    <div class="stat-card mt-3">
        <div class="stat-label mb-3">📈 RISK TREND</div>
        <div style="position: relative; height: 280px;">
            <canvas id="riskHistoryChart"></canvas>
        </div>
    </div>

    <style>
        .stat-icon {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 22px;
            height: 22px;
            border-radius: 50%;
            font-size: .75rem;
            margin-right: .3rem;
        }
    </style>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const totalRisk = {{ $risk->total_risk ?? 0 }};
            const gaugeColor = totalRisk < 30 ? '#22c55e' : totalRisk < 60 ? '#f0b429' : '#3b5bdb';

            new Chart(document.getElementById('gaugeChart'), {
                type: 'doughnut',
                data: {
                    datasets: [{
                        data: [totalRisk, 100 - totalRisk],
                        backgroundColor: [gaugeColor, '#e4e7eb'],
                        borderWidth: 0,
                    }],
                },
                options: {
                    circumference: 180,
                    rotation: 270,
                    cutout: '75%',
                    plugins: { legend: { display: false }, tooltip: { enabled: false } },
                },
            });

            fetch('https://api.open-meteo.com/v1/forecast?latitude={{ $country->latitude }}&longitude={{ $country->longitude }}&current=temperature_2m,wind_speed_10m,precipitation')
                .then(res => res.json())
                .then(data => {
                    const c = data.current || {};
                    document.getElementById('tempValue').textContent = (c.temperature_2m ?? '-') + '°C';
                    document.getElementById('weatherDesc').textContent = 'Live data';
                    document.getElementById('detailTemp').textContent = (c.temperature_2m ?? '-') + '°C';
                    document.getElementById('detailWind').textContent = (c.wind_speed_10m ?? '-') + ' km/h';
                    document.getElementById('detailRain').textContent = (c.precipitation ?? '0') + ' mm';
                    document.getElementById('detailStorm').textContent = (c.wind_speed_10m > 40 ? 'High' : 'Low');
                })
                .catch(() => {
                    document.getElementById('weatherDesc').textContent = 'Gagal memuat cuaca';
                });

            fetch('{{ route('chart.risk-history', $country) }}')
                .then(res => res.json())
                .then(data => {
                    new Chart(document.getElementById('riskHistoryChart'), {
                        type: 'line',
                        data: {
                            labels: data.labels,
                            datasets: [{
                                label: 'Total Risk Score',
                                data: data.data,
                                borderColor: '#3b5bdb',
                                backgroundColor: 'rgba(59, 91, 219, 0.15)',
                                tension: 0.3,
                                fill: true,
                            }],
                        },
                        options: {
                            responsive: true,
                            maintainAspectRatio: false,
                            scales: {
                                y: { beginAtZero: true, grid: { color: '#e4e7eb' } },
                                x: { grid: { color: '#e4e7eb' } },
                            },
                            plugins: { legend: { labels: { color: '#1a1d26' } } },
                        },
                    });
                });
        });
    </script>
    @endpush
</x-app-layout>