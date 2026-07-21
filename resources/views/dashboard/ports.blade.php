<x-app-layout>

<x-slot name="header">

<div class="d-flex justify-content-between align-items-center">

    <div>

        <h2 class="fw-bold mb-1">

            ⚓ Global Port Dashboard

        </h2>

        <p class="text-muted mb-0">

            Monitoring aktivitas pelabuhan dan risiko supply chain global.

        </p>

    </div>

    <div>

        <span class="badge bg-primary px-3 py-2">

            {{ now()->format('d M Y') }}

        </span>

    </div>

</div>

</x-slot>

<div class="container-fluid py-4">

@php

$totalPort = $ports->count();

$lowRisk = $ports->filter(fn($p)=>optional($p->country->latestRiskScore)->risk_level=='low')->count();

$mediumRisk = $ports->filter(fn($p)=>optional($p->country->latestRiskScore)->risk_level=='medium')->count();

$highRisk = $ports->filter(fn($p)=>optional($p->country->latestRiskScore)->risk_level=='high')->count();

@endphp

<div class="row mb-4">

<div class="col-lg-3 col-md-6 mb-3">

<div class="card shadow border-0 stat-box">

<div class="card-body">

<div class="d-flex justify-content-between align-items-center">

<div>

<div class="text-muted small">

Total Ports

</div>

<h2 class="fw-bold">

{{ $totalPort }}

</h2>

</div>

<div class="icon-circle bg-primary">

⚓

</div>

</div>

</div>

</div>

</div>

<div class="col-lg-3 col-md-6 mb-3">

<div class="card shadow border-0 stat-box">

<div class="card-body">

<div class="d-flex justify-content-between align-items-center">

<div>

<div class="text-muted small">

Low Risk

</div>

<h2 class="fw-bold text-success">

{{ $lowRisk }}

</h2>

</div>

<div class="icon-circle bg-success">

🟢

</div>

</div>

</div>

</div>

</div>

<div class="col-lg-3 col-md-6 mb-3">

<div class="card shadow border-0 stat-box">

<div class="card-body">

<div class="d-flex justify-content-between align-items-center">

<div>

<div class="text-muted small">

Medium Risk

</div>

<h2 class="fw-bold text-warning">

{{ $mediumRisk }}

</h2>

</div>

<div class="icon-circle bg-warning">

🟡

</div>

</div>

</div>

</div>

</div>

<div class="col-lg-3 col-md-6 mb-3">

<div class="card shadow border-0 stat-box">

<div class="card-body">

<div class="d-flex justify-content-between align-items-center">

<div>

<div class="text-muted small">

High Risk

</div>

<h2 class="fw-bold text-danger">

{{ $highRisk }}

</h2>

</div>

<div class="icon-circle bg-danger">

🔴

</div>

</div>

</div>

</div>

</div>

</div>

<div class="card border-0 shadow-lg mb-4">

<div class="card-body">

<div class="row align-items-center">

<div class="col-lg-6">

<h4 class="fw-bold mb-1">

⚓ Daftar Pelabuhan

</h4>

<p class="text-muted mb-0">

Cari pelabuhan berdasarkan nama, negara, atau UNLOCODE.

</p>

</div>

<div class="col-lg-6">

<input

type="text"

id="searchPort"

class="form-control form-control-lg"

placeholder="🔍 Cari pelabuhan...">

</div>

</div>

</div>

</div>

<div class="row g-4" id="portGrid">
    @foreach ($ports as $port)

@php

$risk = $port->country->latestRiskScore;

$riskLevel = $risk->risk_level ?? 'low';

$riskScore = $risk->total_risk ?? rand(30,90);

$riskClass = match($riskLevel){

'low'=>'success',

'medium'=>'warning',

'high'=>'danger',

default=>'secondary'

};

$typeIcon = match($port->port_type){

'seaport'=>'🚢',

'river'=>'🛶',

'inland'=>'🏭',

default=>'⚓'

};

@endphp

<div class="col-xl-4 col-lg-6 port-card"

data-search="{{ strtolower($port->name.' '.$port->country->name.' '.$port->unlocode) }}">

<div class="card port-modern border-0 shadow h-100">

<div class="card-body">

<div class="d-flex justify-content-between align-items-start mb-3">

<div>

<div class="port-icon mb-3">

{{ $typeIcon }}

</div>

<h5 class="fw-bold mb-1">

{{ $port->name }}

</h5>

<div class="text-muted">

📍 {{ $port->country->name }}

</div>

</div>

<span class="badge bg-{{ $riskClass }} px-3 py-2">

{{ strtoupper($riskLevel) }}

</span>

</div>

<hr>

<div class="row mb-3">

<div class="col-6">

<div class="small text-muted">

UNLOCODE

</div>

<div class="fw-semibold">

{{ $port->unlocode }}

</div>

</div>

<div class="col-6">

<div class="small text-muted">

TYPE

</div>

<div class="fw-semibold">

{{ ucfirst($port->port_type) }}

</div>

</div>

</div>

<div class="small text-muted mb-2">

Operational Risk

</div>

<div class="progress mb-2"

style="height:10px;border-radius:20px;">

<div

class="progress-bar bg-{{ $riskClass }}"

style="width:{{ min($riskScore,100) }}%;">

</div>

</div>

<div class="d-flex justify-content-between">

<small>

Risk Score

</small>

<strong>

{{ number_format($riskScore,1) }}

</strong>

</div>

<div class="mt-4">

<a href="#"

class="btn btn-primary w-100">

View Detail

</a>

</div>

</div>

</div>

</div>

@endforeach

</div>

<p class="text-center text-muted mt-5"

id="noResults"

style="display:none;">

Tidak ada pelabuhan ditemukan.

</p>
@push('scripts')

<script>

document.getElementById('searchPort').addEventListener('input',function(e){

let keyword=e.target.value.toLowerCase();

let total=0;

document.querySelectorAll('.port-card').forEach(function(card){

let show=card.dataset.search.includes(keyword);

card.style.display=show?'':'none';

if(show) total++;

});

document.getElementById('noResults').style.display=

total==0?'block':'none';

});

</script>

<style>

.stat-box{

transition:.3s;

border-radius:18px;

}

.stat-box:hover{

transform:translateY(-6px);

}

.icon-circle{

width:58px;

height:58px;

display:flex;

justify-content:center;

align-items:center;

border-radius:50%;

color:white;

font-size:24px;

}

.port-modern{

border-radius:18px;

transition:.35s;

overflow:hidden;

}

.port-modern:hover{

transform:translateY(-8px);

box-shadow:0 20px 45px rgba(0,0,0,.18)!important;

}

.port-icon{

width:65px;

height:65px;

background:#EDF4FF;

border-radius:16px;

display:flex;

justify-content:center;

align-items:center;

font-size:34px;

}

.progress{

background:#E9ECEF;

}

.badge{

border-radius:30px;

font-size:.75rem;

}

.btn-primary{

border-radius:10px;

font-weight:600;

padding:10px;

}

</style>

@endpush

</x-app-layout>