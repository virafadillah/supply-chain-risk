<x-app-layout>

<x-slot name="header">

<div class="d-flex justify-content-between align-items-center">

<div>

<h3 class="fw-bold mb-1">

⭐ Watchlist Monitoring

</h3>

<p class="text-muted mb-0">

Monitor negara yang menjadi prioritas Supply Chain Anda

</p>

</div>

<div>

<span class="badge bg-primary px-3 py-2">

{{ $watchlists->count() }} Negara

</span>

</div>

</div>

</x-slot>

@php

$totalWatchlist = $watchlists->count();

$high = 0;

$medium = 0;

$low = 0;

foreach($watchlists as $watch){

$risk = optional($watch->country->latestRiskScore)->risk_level;

if($risk=='high') $high++;

if($risk=='medium') $medium++;

if($risk=='low') $low++;

}

@endphp

<div class="container-fluid py-4">

@if(session('success'))

<div class="alert alert-success shadow-sm">

{{ session('success') }}

</div>

@endif

<div class="row g-3 mb-4">

<div class="col-md-3">

<div class="stat-card text-center">

<div class="stat-label">

🌍 Total Watchlist

</div>

<div class="stat-value">

{{ $totalWatchlist }}

</div>

</div>

</div>

<div class="col-md-3">

<div class="stat-card text-center">

<div class="stat-label">

🔴 High Risk

</div>

<div class="stat-value text-danger">

{{ $high }}

</div>

</div>

</div>

<div class="col-md-3">

<div class="stat-card text-center">

<div class="stat-label">

🟡 Medium Risk

</div>

<div class="stat-value text-warning">

{{ $medium }}

</div>

</div>

</div>

<div class="col-md-3">

<div class="stat-card text-center">

<div class="stat-label">

🟢 Low Risk

</div>

<div class="stat-value text-success">

{{ $low }}

</div>

</div>

</div>

</div>

<div class="stat-card mb-4">

<input

type="text"

id="searchWatch"

class="form-control"

placeholder="🔍 Cari Negara...">

</div>

@if($watchlists->isEmpty())

<div class="stat-card text-center py-5">

<h4 class="mb-3">

Belum ada negara di Watchlist

</h4>

<p class="text-muted">

Tambahkan negara dari Dashboard untuk mulai melakukan monitoring.

</p>

<a

href="{{ route('dashboard') }}"

class="btn btn-primary">

Buka Dashboard

</a>

</div>

@else

<div class="row g-4" id="watchGrid">
    @foreach($watchlists as $watchlist)

@php

$country = $watchlist->country;

$latestRisk = $country->latestRiskScore;

$risk = $latestRisk->risk_level ?? 'unknown';

$badgeClass = match($risk){

'low'=>'low',

'medium'=>'medium',

'high'=>'high',

default=>'low',

};

$score = $latestRisk->total_risk ?? '-';

@endphp

<div

class="col-lg-4 col-md-6 watch-card"

data-search="{{ strtolower($country->name) }}">

<div

class="stat-card h-100"

style="border-top:4px solid #3b5bdb;transition:.3s;">

<div class="d-flex justify-content-between align-items-start mb-3">

<div>

<h5 class="fw-bold mb-1">

<a

href="{{ route('dashboard.show',$country) }}"

class="text-decoration-none text-dark">

{{ $country->name }}

</a>

</h5>

<div class="text-muted small">

{{ $country->capital }}

•

{{ $country->region }}

</div>

</div>

<span class="risk-badge {{ $badgeClass }}">

{{ ucfirst($risk) }}

</span>

</div>

<hr>

<div class="row text-center mb-3">

<div class="col-6">

<div class="small text-muted">

Risk Score

</div>

<div

class="fw-bold"

style="font-size:24px;color:#2f4bc0;">

{{ $score }}

</div>

</div>

<div class="col-6">

<div class="small text-muted">

Status

</div>

<div class="fw-bold">

{{ strtoupper($country->currency_code) }}

</div>

</div>

</div>

<form

action="{{ route('watchlist.toggle',$country) }}"

method="POST">

@csrf

<button

type="submit"

class="btn btn-outline-danger w-100">

🗑 Hapus dari Watchlist

</button>

</form>

</div>

</div>

@endforeach
</div>

@endif

</div>

@push('scripts')

<script>

document.getElementById('searchWatch').addEventListener('input', function () {

    const keyword = this.value.toLowerCase();

    let total = 0;

    document.querySelectorAll('.watch-card').forEach(function(card){

        const text = card.dataset.search;

        if(text.includes(keyword)){

            card.style.display='';

            total++;

        }else{

            card.style.display='none';

        }

    });

});

document.querySelectorAll('.watch-card .stat-card').forEach(function(card){

    card.addEventListener('mouseenter',function(){

        card.style.transform='translateY(-8px)';

        card.style.boxShadow='0 20px 40px rgba(0,0,0,.12)';

    });

    card.addEventListener('mouseleave',function(){

        card.style.transform='translateY(0)';

        card.style.boxShadow='';

    });

});

</script>

@endpush

</x-app-layout>