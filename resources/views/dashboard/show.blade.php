<x-app-layout>

@php
    $risk = $country->latestRiskScore;

    $riskClass = match($risk?->risk_level){
        'low' => 'low',
        'medium' => 'medium',
        'high' => 'high',
        default => 'low',
    };

    $riskColor = match($risk?->risk_level){
        'low' => '#22c55e',
        'medium' => '#f59e0b',
        'high' => '#ef4444',
        default => '#94a3b8',
    };

    $isWatchlisted = auth()->user()
        ->watchlists()
        ->where('country_id',$country->id)
        ->exists();

    $flagCode = $country->code ? strtolower($country->code) : null;
@endphp

<style>

.country-header{

    background:linear-gradient(135deg,#1e3a8a,#3b82f6);

    border-radius:22px;

    padding:35px;

    color:#fff;

    margin-bottom:25px;

    overflow:hidden;

    position:relative;

}

.country-header::before{

    content:"";

    position:absolute;

    right:-70px;

    top:-70px;

    width:220px;

    height:220px;

    border-radius:50%;

    background:rgba(255,255,255,.08);

}

.country-top{

    display:flex;

    justify-content:space-between;

    align-items:center;

    gap:25px;

    position:relative;

    z-index:2;

}

.country-info{

    display:flex;

    align-items:center;

    gap:20px;

}

.flag-box{

    width:90px;

    height:90px;

    background:white;

    border-radius:20px;

    display:flex;

    justify-content:center;

    align-items:center;

    font-size:60px;

    box-shadow:0 15px 40px rgba(0,0,0,.2);

}

.country-name{

    font-size:34px;

    font-weight:700;

    margin-bottom:8px;

}

.country-detail{

    opacity:.9;

    display:flex;

    gap:10px;

    flex-wrap:wrap;

}

.country-detail span{

    background:rgba(255,255,255,.12);

    padding:5px 12px;

    border-radius:999px;

}

.country-buttons{

    display:flex;

    flex-direction:column;

    gap:12px;

    align-items:end;

}

.risk-status{

    padding:10px 18px;

    border-radius:999px;

    font-weight:700;

    background:white;

}

.watch-btn{

    background:white;

    border:none;

    border-radius:12px;

    padding:12px 22px;

    font-weight:600;

    color:#1e3a8a;

    transition:.3s;

}

.watch-btn:hover{

    transform:translateY(-3px);

    box-shadow:0 12px 30px rgba(0,0,0,.18);

}

.info-grid{

    display:grid;

    grid-template-columns:repeat(4,1fr);

    gap:18px;

    margin-top:28px;

}

.info-card{

    background:rgba(255,255,255,.12);

    backdrop-filter:blur(10px);

    border-radius:18px;

    padding:18px;

}

.info-card small{

    opacity:.8;

}

.info-card h4{

    margin-top:8px;

    margin-bottom:0;

    font-size:28px;

    font-weight:700;

}

@media(max-width:991px){

.country-top{

flex-direction:column;

align-items:flex-start;

}

.country-buttons{

align-items:flex-start;

}

.info-grid{

grid-template-columns:repeat(2,1fr);

}

}

@media(max-width:576px){

.info-grid{

grid-template-columns:1fr;

}

.flag-box{

width:70px;

height:70px;

font-size:42px;

}

.country-name{

font-size:28px;

}

}

</style>

<div class="country-header">

<div class="country-top">

<div class="country-info">

<div class="flag-box">

@if($flagCode)

<span class="fi fi-{{ $flagCode }}"></span>

@else

🌍

@endif

</div>

<div>

<div class="country-name">

{{ $country->name }}

</div>

<div class="country-detail">

<span>📍 {{ $country->capital }}</span>

<span>🌎 {{ $country->region }}</span>

<span>💱 {{ $country->currency_code }}</span>

</div>

</div>

</div>

<div class="country-buttons">

<div class="risk-status"

style="color:{{ $riskColor }}">

● {{ ucfirst($risk->risk_level ?? 'N/A') }} Risk

</div>

<form action="{{ route('watchlist.toggle',$country) }}" method="POST">

@csrf

<button class="watch-btn">

@if($isWatchlisted)

★ Watchlisted

@else

☆ Tambah Watchlist

@endif

</button>

</form>

</div>

</div>

<div class="info-grid">

<div class="info-card">

<small>GDP</small>

<h4>

{{ $country->gdp ? '$'.number_format($country->gdp/1000000000,2).'B' : '-' }}

</h4>

</div>

<div class="info-card">

<small>Inflation</small>

<h4>

{{ $country->inflation_rate ?? '-' }}%

</h4>

</div>

<div class="info-card">

<small>Risk Score</small>

<h4>

{{ $risk->total_risk ?? '-' }}

</h4>

</div>

<div class="info-card">

<small>Currency</small>

<h4>

{{ $country->currency_code }}

</h4>

</div>

</div>

</div>
<div class="row g-4 mb-4">

    {{-- RISK GAUGE --}}
    <div class="col-lg-4">

        <div class="stat-card h-100">

            <div class="d-flex justify-content-between align-items-center mb-3">

                <h5 class="mb-0 fw-bold">
                    📊 Risk Score
                </h5>

                <span class="risk-badge {{ $riskClass }}">
                    {{ ucfirst($risk->risk_level ?? 'N/A') }}
                </span>

            </div>

            <div style="max-width:260px;margin:auto;position:relative">

                <canvas id="gaugeChart"></canvas>

                <div style="position:absolute;left:0;right:0;bottom:15px;text-align:center;">

                    <div style="font-size:42px;font-weight:700;color:#3b5bdb">

                        {{ $risk->total_risk ?? '-' }}

                    </div>

                    <div class="text-muted">

                        Total Risk

                    </div>

                </div>

            </div>

            <hr>

            <div class="row text-center">

                <div class="col">

                    <small class="text-muted">

                        Level

                    </small>

                    <div class="fw-bold">

                        {{ ucfirst($risk->risk_level ?? '-') }}

                    </div>

                </div>

                <div class="col">

                    <small class="text-muted">

                        Updated

                    </small>

                    <div class="fw-bold">

                        {{ optional($risk)->created_at?->format('d M Y') ?? '-' }}

                    </div>

                </div>

            </div>

        </div>

    </div>

    {{-- BREAKDOWN --}}
    <div class="col-lg-4">

        <div class="stat-card h-100">

            <h5 class="fw-bold mb-4">

                📈 Risk Breakdown

            </h5>

            @php

            $items=[

                ['Weather',$risk->weather_risk ?? 0,'#4f46e5'],

                ['Inflation',$risk->inflation_risk ?? 0,'#22c55e'],

                ['Currency',$risk->currency_risk ?? 0,'#f59e0b'],

                ['News',$risk->news_risk ?? 0,'#ef4444']

            ];

            @endphp

            @foreach($items as $item)

            <div class="mb-4">

                <div class="d-flex justify-content-between mb-1">

                    <span>

                        {{ $item[0] }}

                    </span>

                    <strong>

                        {{ number_format($item[1],1) }}

                    </strong>

                </div>

                <div class="progress" style="height:12px;border-radius:30px;background:#edf2f7">

                    <div class="progress-bar"

                         style="width:{{ min($item[1],100) }}%;
                         background:{{ $item[2] }};
                         border-radius:30px;">

                    </div>

                </div>

            </div>

            @endforeach

        </div>

    </div>

    {{-- WEATHER --}}
    <div class="col-lg-4">

        <div class="stat-card h-100">

            <div class="d-flex justify-content-between">

                <h5 class="fw-bold">

                    🌦 Live Weather

                </h5>

                <span class="badge bg-primary">

                    LIVE

                </span>

            </div>

            <div class="text-center mt-4">

                <div style="font-size:60px">

                    ☀️

                </div>

                <div id="tempValue"

                     style="font-size:44px;font-weight:700">

                    -

                </div>

                <div id="weatherDesc"

                     class="text-muted">

                    Loading...

                </div>

            </div>

            <hr>

            <div class="row text-center">

                <div class="col-6 mb-3">

                    <div class="small text-muted">

                        Wind

                    </div>

                    <div id="detailWind"

                         class="fw-bold">

                        -

                    </div>

                </div>

                <div class="col-6 mb-3">

                    <div class="small text-muted">

                        Rain

                    </div>

                    <div id="detailRain"

                         class="fw-bold">

                        -

                    </div>

                </div>

                <div class="col-6">

                    <div class="small text-muted">

                        Temperature

                    </div>

                    <div id="detailTemp"

                         class="fw-bold">

                        -

                    </div>

                </div>

                <div class="col-6">

                    <div class="small text-muted">

                        Storm

                    </div>

                    <div id="detailStorm"

                         class="fw-bold">

                        -

                    </div>

                </div>

            </div>

        </div>

    </div>

</div>
<div class="row g-4 mb-4">

    {{-- ===================== PORT ===================== --}}
    <div class="col-lg-6">

        <div class="stat-card h-100">

            <div class="d-flex justify-content-between align-items-center mb-4">

                <h5 class="fw-bold mb-0">
                    ⚓ Pelabuhan Utama
                </h5>

                <span class="badge bg-primary">
                    {{ $country->ports->count() }}
                </span>

            </div>

            @forelse($country->ports as $port)

            <div class="d-flex align-items-center justify-content-between mb-3 p-3 rounded-4"

                 style="background:#f8fafc;">

                <div class="d-flex align-items-center">

                    <div style="width:55px;
                                height:55px;
                                border-radius:15px;
                                background:#e0e7ff;
                                display:flex;
                                align-items:center;
                                justify-content:center;
                                font-size:28px;
                                margin-right:15px;">

                        🚢

                    </div>

                    <div>

                        <div class="fw-bold">

                            {{ $port->name }}

                        </div>

                        <small class="text-muted">

                            {{ $port->unlocode }}

                        </small>

                    </div>

                </div>

                <div class="text-end">

                    <span class="badge bg-light text-dark">

                        {{ ucfirst($port->port_type) }}

                    </span>

                </div>

            </div>

            @empty

            <div class="text-center py-5">

                <div style="font-size:55px;">
                    ⚓
                </div>

                <div class="text-muted mt-3">

                    Belum ada data pelabuhan.

                </div>

            </div>

            @endforelse

        </div>

    </div>



    {{-- ===================== NEWS ===================== --}}
    <div class="col-lg-6">

        <div class="stat-card h-100">

            <div class="d-flex justify-content-between align-items-center mb-4">

                <h5 class="fw-bold mb-0">

                    📰 Berita Terbaru

                </h5>

                <span class="badge bg-success">

                    Live

                </span>

            </div>

            @forelse($country->newsCache as $news)

            @php

            $badge=match($news->sentiment){

                'positive'=>'success',

                'neutral'=>'warning',

                'negative'=>'danger',

                default=>'secondary'

            };

            @endphp

            <div class="d-flex mb-4">

                <div style="width:60px;
                            height:60px;
                            border-radius:15px;
                            background:#dbeafe;
                            display:flex;
                            justify-content:center;
                            align-items:center;
                            font-size:28px;
                            flex-shrink:0;">

                    📰

                </div>

                <div class="ms-3 flex-grow-1">

                    <div class="fw-semibold">

                        {{ $news->title }}

                    </div>

                    <div class="small text-muted mt-1">

                        {{ \Illuminate\Support\Str::limit($news->description,90) }}

                    </div>

                    <div class="mt-2">

                        <span class="badge bg-{{ $badge }}">

                            {{ ucfirst($news->sentiment) }}

                        </span>

                    </div>

                </div>

            </div>

            @empty

            <div class="text-center py-5">

                <div style="font-size:60px">

                    📰

                </div>

                <div class="text-muted mt-3">

                    Belum ada berita.

                </div>

            </div>

            @endforelse

        </div>

    </div>

</div>



<div class="stat-card">

    <div class="d-flex justify-content-between align-items-center mb-4">

        <h5 class="fw-bold mb-0">

            📈 Risk Trend

        </h5>

        <span class="badge bg-primary">

            Historical Data

        </span>

    </div>

    <div style="height:360px;">

        <canvas id="riskHistoryChart"></canvas>

    </div>

</div>
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {

    /* ===========================
       RISK GAUGE
    =========================== */

    const riskValue = {{ $risk->total_risk ?? 0 }};

    const gaugeColor =
        riskValue < 30
            ? '#22c55e'
            : riskValue < 60
                ? '#f59e0b'
                : '#ef4444';

    new Chart(document.getElementById('gaugeChart'),{

        type:'doughnut',

        data:{
            datasets:[{
                data:[riskValue,100-riskValue],
                backgroundColor:[
                    gaugeColor,
                    '#edf2f7'
                ],
                borderWidth:0,
                hoverOffset:0
            }]
        },

        options:{
            responsive:true,
            maintainAspectRatio:false,
            circumference:180,
            rotation:270,
            cutout:'78%',
            animation:{
                animateRotate:true,
                duration:1800
            },
            plugins:{
                legend:{
                    display:false
                },
                tooltip:{
                    enabled:false
                }
            }
        }

    });



    /* ===========================
       WEATHER API
    =========================== */

    fetch("https://api.open-meteo.com/v1/forecast?latitude={{ $country->latitude }}&longitude={{ $country->longitude }}&current=temperature_2m,wind_speed_10m,precipitation")

    .then(res=>res.json())

    .then(data=>{

        const c=data.current||{};

        document.getElementById('tempValue').innerHTML=
            (c.temperature_2m ?? '-')+"°C";

        document.getElementById('detailTemp').innerHTML=
            (c.temperature_2m ?? '-')+"°C";

        document.getElementById('detailWind').innerHTML=
            (c.wind_speed_10m ?? '-')+" km/h";

        document.getElementById('detailRain').innerHTML=
            (c.precipitation ?? 0)+" mm";

        document.getElementById('detailStorm').innerHTML=
            (c.wind_speed_10m>40)
                ? 'High'
                : 'Low';

        document.getElementById('weatherDesc').innerHTML=
            'Realtime Weather';

    })

    .catch(()=>{

        document.getElementById('weatherDesc').innerHTML=
            'Weather unavailable';

    });




    /* ===========================
       RISK HISTORY
    =========================== */

    fetch("{{ route('chart.risk-history',$country) }}")

    .then(res=>res.json())

    .then(data=>{

        new Chart(

            document.getElementById('riskHistoryChart'),

            {

                type:'line',

                data:{

                    labels:data.labels,

                    datasets:[{

                        label:'Risk Score',

                        data:data.data,

                        borderColor:'#2563eb',

                        backgroundColor:'rgba(37,99,235,.15)',

                        fill:true,

                        tension:.35,

                        pointRadius:5,

                        pointBackgroundColor:'#2563eb',

                        pointHoverRadius:7

                    }]

                },

                options:{

                    responsive:true,

                    maintainAspectRatio:false,

                    interaction:{
                        intersect:false,
                        mode:'index'
                    },

                    plugins:{
                        legend:{
                            display:false
                        }
                    },

                    scales:{

                        y:{

                            beginAtZero:true,

                            grid:{
                                color:'#edf2f7'
                            }

                        },

                        x:{

                            grid:{
                                display:false
                            }

                        }

                    }

                }

            }

        );

    });





    /* ===========================
       CARD HOVER
    =========================== */

    document.querySelectorAll('.stat-card').forEach(function(card){

        card.style.transition='.3s';

        card.addEventListener('mouseenter',function(){

            card.style.transform='translateY(-6px)';

            card.style.boxShadow='0 18px 40px rgba(0,0,0,.12)';

        });

        card.addEventListener('mouseleave',function(){

            card.style.transform='translateY(0px)';

            card.style.boxShadow='';

        });

    });

});
</script>
@endpush

</x-app-layout>