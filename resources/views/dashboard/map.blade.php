<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Global Port & Risk Map
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                <p class="text-gray-600 mb-3">Peta risiko semua negara (titik kecil) dan lokasi pelabuhan utama (titik besar berlabel). Beralih ke "Weather View" untuk melihat kondisi cuaca real-time (hujan, badai, angin kencang) per negara.</p>

                <div class="mb-3 flex gap-2">
                    <button id="btnRiskView" type="button" class="px-4 py-2 rounded bg-indigo-600 text-white text-sm font-medium">Risk View</button>
                    <button id="btnWeatherView" type="button" class="px-4 py-2 rounded bg-gray-200 text-gray-700 text-sm font-medium">Weather View</button>
                    <span id="weatherLoading" class="text-sm text-gray-500 self-center hidden">Memuat data cuaca...</span>
                </div>

                <div id="worldMap" style="height: 600px; width: 100%; border-radius: 8px;"></div>

                <div id="weatherLegend" class="mt-3 hidden text-sm text-gray-600 space-x-4">
                    <span><span class="inline-block w-3 h-3 rounded-full align-middle" style="background:#dc3545"></span> Badai</span>
                    <span><span class="inline-block w-3 h-3 rounded-full align-middle" style="background:#0d6efd"></span> Hujan Lebat</span>
                    <span><span class="inline-block w-3 h-3 rounded-full align-middle" style="background:#6f42c1"></span> Angin Kencang</span>
                    <span><span class="inline-block w-3 h-3 rounded-full align-middle" style="background:#20c997"></span> Hujan Ringan</span>
                    <span><span class="inline-block w-3 h-3 rounded-full align-middle" style="background:#ffc107"></span> Cerah</span>
                </div>

            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const map = L.map('worldMap').setView([10, 20], 2);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; OpenStreetMap contributors',
                maxZoom: 18,
            }).addTo(map);

            const riskColors = {
                low: '#198754',
                medium: '#ffc107',
                high: '#dc3545',
                unknown: '#6c757d',
            };

            const weatherColors = {
                badai: '#dc3545',
                hujan_lebat: '#0d6efd',
                angin_kencang: '#6f42c1',
                hujan_ringan: '#20c997',
                cerah: '#ffc107',
            };

            const weatherLabels = {
                badai: 'Badai',
                hujan_lebat: 'Hujan Lebat',
                angin_kencang: 'Angin Kencang',
                hujan_ringan: 'Hujan Ringan',
                cerah: 'Cerah',
            };

            let riskLayer = L.layerGroup().addTo(map);
            let weatherLayer = L.layerGroup();
            let weatherLoaded = false;

            const btnRisk = document.getElementById('btnRiskView');
            const btnWeather = document.getElementById('btnWeatherView');
            const weatherLoading = document.getElementById('weatherLoading');
            const weatherLegend = document.getElementById('weatherLegend');

            function loadRiskLayer() {
                fetch('{{ route('chart.map') }}')
                    .then(res => res.json())
                    .then(markers => {
                        markers.forEach(m => {
                            const color = riskColors[m.risk_level] || riskColors.unknown;
                            const isPort = m.type === 'port';

                            const marker = L.circleMarker([m.lat, m.lng], {
                                radius: isPort ? 9 : 4,
                                fillColor: color,
                                color: isPort ? '#fff' : color,
                                weight: isPort ? 2 : 1,
                                opacity: 1,
                                fillOpacity: isPort ? 0.9 : 0.6,
                            });

                            marker.bindPopup(`
                                <strong>${m.name}</strong><br>
                                ${m.unlocode ? m.unlocode + '<br>' : ''}
                                <em>${m.country}</em><br>
                                ${m.port_type ? 'Type: ' + m.port_type + '<br>' : ''}
                                Risk: <strong>${m.risk_level ?? 'N/A'}</strong>
                                ${m.total_risk !== null ? ' (' + m.total_risk + ')' : ''}
                            `);

                            marker.addTo(riskLayer);
                        });
                    });
            }

            function loadWeatherLayer() {
                if (weatherLoaded) return;
                weatherLoading.classList.remove('hidden');

                fetch('{{ route('chart.weather-map') }}')
                    .then(res => res.json())
                    .then(markers => {
                        markers.forEach(m => {
                            const color = weatherColors[m.condition] || weatherColors.cerah;

                            const marker = L.circleMarker([m.lat, m.lng], {
                                radius: 7,
                                fillColor: color,
                                color: '#fff',
                                weight: 1.5,
                                opacity: 1,
                                fillOpacity: 0.85,
                            });

                            marker.bindPopup(`
                                <strong>${m.name}</strong><br>
                                Kondisi: <strong>${weatherLabels[m.condition] ?? m.condition}</strong><br>
                                Suhu: ${m.temperature !== null ? m.temperature + '°C' : 'N/A'}<br>
                                Curah hujan: ${m.precipitation !== null ? m.precipitation + ' mm' : 'N/A'}<br>
                                Kecepatan angin: ${m.wind_speed !== null ? m.wind_speed + ' km/j' : 'N/A'}
                            `);

                            marker.addTo(weatherLayer);
                        });

                        weatherLoaded = true;
                        weatherLoading.classList.add('hidden');
                    });
            }

            btnRisk.addEventListener('click', function () {
                map.removeLayer(weatherLayer);
                riskLayer.addTo(map);
                weatherLegend.classList.add('hidden');
                btnRisk.classList.replace('bg-gray-200', 'bg-indigo-600');
                btnRisk.classList.replace('text-gray-700', 'text-white');
                btnWeather.classList.replace('bg-indigo-600', 'bg-gray-200');
                btnWeather.classList.replace('text-white', 'text-gray-700');
            });

            btnWeather.addEventListener('click', function () {
                map.removeLayer(riskLayer);
                loadWeatherLayer();
                weatherLayer.addTo(map);
                weatherLegend.classList.remove('hidden');
                btnWeather.classList.replace('bg-gray-200', 'bg-indigo-600');
                btnWeather.classList.replace('text-gray-700', 'text-white');
                btnRisk.classList.replace('bg-indigo-600', 'bg-gray-200');
                btnRisk.classList.replace('text-white', 'text-gray-700');
            });

            loadRiskLayer();
        });
    </script>
    @endpush
</x-app-layout>