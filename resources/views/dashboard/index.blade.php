<x-app-layout>

<x-slot name="header">

<div class="d-flex justify-content-between align-items-center">

    <div>

        <h2 class="fw-bold mb-1 text-dark">
            🌍 Global Country Dashboard
        </h2>

        <p class="text-muted mb-0">
            Monitoring Supply Chain Risk Intelligence Platform
        </p>

    </div>

    <div class="text-end">

        <span class="badge bg-primary px-3 py-2">
            {{ now()->format('d M Y') }}
        </span>

    </div>

</div>

</x-slot>

<div class="container-fluid py-4">

@if(session('success'))

<div class="alert alert-success shadow-sm">

{{ session('success') }}

</div>

@endif

@php

$totalCountry = $countries->count();

$totalWatchlist = auth()->user()->watchlists->count();

$highRisk = $countries->filter(function($country){

$risk = $country->riskScores()->latest('calculated_at')->first();

return $risk && $risk->risk_level == 'high';

})->count();

$mediumRisk = $countries->filter(function($country){

$risk = $country->riskScores()->latest('calculated_at')->first();

return $risk && $risk->risk_level == 'medium';

})->count();

@endphp

<div class="row mb-4">

<div class="col-lg-3 col-md-6 mb-3">

<div class="card border-0 shadow h-100">

<div class="card-body">

<div class="d-flex justify-content-between">

<div>

<div class="text-muted small">

Total Negara

</div>

<h2 class="fw-bold mb-0">

{{ $totalCountry }}

</h2>

</div>

<div style="font-size:40px">

🌍

</div>

</div>

</div>

</div>

</div>

<div class="col-lg-3 col-md-6 mb-3">

<div class="card border-0 shadow h-100">

<div class="card-body">

<div class="d-flex justify-content-between">

<div>

<div class="text-muted small">

Watchlist

</div>

<h2 class="fw-bold mb-0">

{{ $totalWatchlist }}

</h2>

</div>

<div style="font-size:40px">

⭐

</div>

</div>

</div>

</div>

</div>

<div class="col-lg-3 col-md-6 mb-3">

<div class="card border-0 shadow h-100">

<div class="card-body">

<div class="d-flex justify-content-between">

<div>

<div class="text-muted small">

High Risk

</div>

<h2 class="fw-bold text-danger mb-0">

{{ $highRisk }}

</h2>

</div>

<div style="font-size:40px">

🚨

</div>

</div>

</div>

</div>

</div>

<div class="col-lg-3 col-md-6 mb-3">

<div class="card border-0 shadow h-100">

<div class="card-body">

<div class="d-flex justify-content-between">

<div>

<div class="text-muted small">

Medium Risk

</div>

<h2 class="fw-bold text-warning mb-0">

{{ $mediumRisk }}

</h2>

</div>

<div style="font-size:40px">

📈

</div>

</div>

</div>

</div>

</div>

</div>

<div class="card border-0 shadow">

<div class="card-body">

<div class="d-flex justify-content-between align-items-center mb-4">

<div>

<h4 class="fw-bold mb-1">

Daftar Negara

</h4>

<p class="text-muted mb-0">

Klik salah satu negara untuk melihat detail Supply Chain Risk.

</p>

</div>

<span class="badge bg-primary">

{{ $countries->count() }} Countries

</span>

</div>

<div class="row g-4">
    @foreach ($countries as $country)

@php

$latestRisk = $country->riskScores()->latest('calculated_at')->first();

$badgeClass = match($latestRisk?->risk_level){

'low' => 'bg-success',

'medium' => 'bg-warning text-dark',

'high' => 'bg-danger',

default => 'bg-secondary',

};

$isWatchlisted = auth()->user()->watchlists->contains('country_id',$country->id);

@endphp

<div class="col-xl-4 col-lg-6">

<div class="card border-0 shadow-lg h-100 country-card">

<div class="card-body">

<div class="d-flex justify-content-between align-items-start mb-3">

<div>

<h5 class="fw-bold mb-1">

<a href="{{ route('dashboard.show',$country) }}"
class="text-decoration-none text-dark">

{{ $country->name }}

</a>

</h5>

<div class="text-muted small">

📍 {{ $country->capital }}

</div>

<div class="text-muted small">

🌎 {{ $country->region }}

</div>

</div>

<span class="badge {{ $badgeClass }} px-3 py-2">

{{ $latestRisk ? ucfirst($latestRisk->risk_level) : 'N/A' }}

</span>

</div>

<hr>

<div class="mb-2">

<div class="small text-muted">

Currency

</div>

<div class="fw-semibold">

{{ $country->currency_code }}

</div>

</div>

<div class="mb-3">

<div class="small text-muted">

Risk Score

</div>

@if($latestRisk)

<div class="progress mb-2" style="height:10px;">

<div
class="progress-bar

@if($latestRisk->risk_level=='high')

bg-danger

@elseif($latestRisk->risk_level=='medium')

bg-warning

@else

bg-success

@endif"

role="progressbar"

style="width:{{ min($latestRisk->total_risk,100) }}%;">

</div>

</div>

<div class="fw-bold">

{{ number_format($latestRisk->total_risk,1) }}

</div>

@else

<span class="text-muted">

Belum ada data

</span>

@endif

</div>

<form action="{{ route('watchlist.toggle',$country) }}"
method="POST">

@csrf

<button
type="submit"
class="btn w-100 {{ $isWatchlisted ? 'btn-primary' : 'btn-outline-primary' }}">

@if($isWatchlisted)

★ Watchlisted

@else

☆ Tambah ke Watchlist

@endif

</button>

</form>

<div class="mt-3">

<a href="{{ route('dashboard.show',$country) }}"
class="btn btn-outline-secondary w-100">

Lihat Detail →

</a>

</div>

</div>

</div>

</div>

@endforeach

</div>

</div>

</div>

<hr class="my-5">

<div class="card border-0 shadow-lg">

<div class="card-body">

<div class="d-flex justify-content-between align-items-center mb-4">

<div>

<h4 class="fw-bold">

📊 Risk Comparison

</h4>

<p class="text-muted mb-0">

Top 10 Negara dengan Risiko Supply Chain Tertinggi

</p>

</div>

</div>

<div style="height:500px">

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

        const colors = [
            '#2563eb',
            '#f59e0b',
            '#22c55e',
            '#ef4444'
        ];

        new Chart(document.getElementById('riskComparisonChart'),{

            type:'bar',

            data:{

                labels:data.labels,

                datasets:data.datasets.map((dataset,index)=>({

                    label:dataset.label,

                    data:dataset.data,

                    backgroundColor:colors[index],

                    borderRadius:8,

                    borderSkipped:false

                }))

            },

            options:{

                responsive:true,

                maintainAspectRatio:false,

                indexAxis:'y',

                plugins:{

                    legend:{

                        position:'bottom',

                        labels:{

                            usePointStyle:true,

                            pointStyle:'circle'

                        }

                    }

                },

                scales:{

                    x:{

                        beginAtZero:true,

                        stacked:true,

                        grid:{

                            color:'#e5e7eb'

                        }

                    },

                    y:{

                        stacked:true,

                        grid:{

                            display:false

                        }

                    }

                }

            }

        });

    });

});
</script>

<style>

.country-card{

    transition:.35s;

    border-radius:18px;

    overflow:hidden;

}

.country-card:hover{

    transform:translateY(-8px);

    box-shadow:0 20px 40px rgba(0,0,0,.15)!important;

}

.country-card .card-body{

    padding:1.5rem;

}

.progress{

    border-radius:20px;

}

.progress-bar{

    border-radius:20px;

}

.card{

    border-radius:18px;

}

.badge{

    font-size:.78rem;

    font-weight:600;

}

.btn{

    border-radius:10px;

}

.card h5{

    font-weight:700;

}

.card-title{

    font-weight:700;

}

.shadow-lg{

    box-shadow:0 12px 30px rgba(0,0,0,.08)!important;

}

</style>
@endpush

</x-app-layout>