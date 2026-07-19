<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Favorite Monitoring List
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">

                @if (session('success'))
                    <div class="alert alert-success">{{ session('success') }}</div>
                @endif

                <p class="text-gray-600 mb-4">Negara yang sedang Bacin pantau:</p>

                @if ($watchlists->isEmpty())
                    <p class="text-muted">Belum ada negara yang ditambahkan ke watchlist. Kunjungi <a href="{{ route('dashboard') }}">Dashboard</a> untuk menambahkan.</p>
                @else
                    <div class="row g-3">
                        @foreach ($watchlists as $watchlist)
                            @php
                                $country = $watchlist->country;
                                $latestRisk = $country->latestRiskScore;
                                $badgeClass = match($latestRisk?->risk_level) {
                                    'low' => 'bg-success',
                                    'medium' => 'bg-warning text-dark',
                                    'high' => 'bg-danger',
                                    default => 'bg-secondary',
                                };
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
                                            @if($latestRisk)
                                                Risk Score: <strong>{{ $latestRisk->total_risk }}</strong>
                                            @endif
                                        </p>

                                        <form action="{{ route('watchlist.toggle', $country) }}" method="POST">
                                            @csrf
                                            <button type="submit" class="btn btn-sm btn-outline-danger w-100">
                                                Hapus dari Watchlist
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>