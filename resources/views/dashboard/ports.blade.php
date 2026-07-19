<x-app-layout>
    <x-slot name="header"><h4>⚓ Pelabuhan</h4></x-slot>

    <div class="stat-card mb-3">
        <input type="text" id="searchPort" class="form-control" placeholder="🔍 Cari nama pelabuhan, negara, atau UNLOCODE...">
    </div>

    <div class="row g-3" id="portGrid">
        @foreach ($ports as $port)
            @php
                $riskClass = match($port->country->latestRiskScore->risk_level ?? null) {
                    'low' => 'low', 'medium' => 'medium', 'high' => 'high', default => 'low',
                };
                $typeIcon = match($port->port_type) {
                    'seaport' => '🚢',
                    'river' => '🛶',
                    'inland' => '🏭',
                    default => '⚓',
                };
                $flagCode = $port->country->code ? strtolower($port->country->code) : null;
            @endphp
            <div class="col-md-4 col-sm-6 port-card"
                 data-search="{{ strtolower($port->name . ' ' . $port->country->name . ' ' . $port->unlocode) }}">
                <div class="stat-card h-100" style="border-top: 3px solid #3b5bdb;">
                    <div class="d-flex justify-content-between align-items-start mb-2">
                        <div class="d-flex align-items-center gap-2">
                            <span style="font-size: 1.5rem;">{{ $typeIcon }}</span>
                            <div>
                                <div class="fw-semibold">{{ $port->name }}</div>
                                <div class="text-muted small">{{ $port->unlocode }}</div>
                            </div>
                        </div>
                        <span class="risk-badge {{ $riskClass }}" style="font-size:.7rem;">
                            {{ ucfirst($port->country->latestRiskScore->risk_level ?? 'N/A') }}
                        </span>
                    </div>

                    <hr class="my-2">

                    <div class="d-flex align-items-center gap-2 small text-muted">
                        @if($flagCode)
                            <span class="fi fi-{{ $flagCode }}" style="border-radius: 3px;"></span>
                        @endif
                        <span>{{ $port->country->name }}</span>
                        <span>&bull;</span>
                        <span>{{ ucfirst($port->port_type) }}</span>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <p class="text-muted text-center mt-4" id="noResults" style="display: none;">Tidak ada pelabuhan yang cocok.</p>

    @push('scripts')
    <script>
        document.getElementById('searchPort').addEventListener('input', function (e) {
            const keyword = e.target.value.toLowerCase();
            let visibleCount = 0;

            document.querySelectorAll('.port-card').forEach(function (card) {
                const match = card.dataset.search.includes(keyword);
                card.style.display = match ? '' : 'none';
                if (match) visibleCount++;
            });

            document.getElementById('noResults').style.display = visibleCount === 0 ? 'block' : 'none';
        });
    </script>
    @endpush
</x-app-layout>