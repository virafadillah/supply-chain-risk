<x-app-layout>

<x-slot name="header">

<div class="d-flex justify-content-between align-items-center">

<div>

<h2 class="fw-bold mb-1">

💱 Currency Intelligence Dashboard

</h2>

<p class="text-muted mb-0">

Live Global Exchange Rate Monitoring

</p>

</div>

<div>

<span class="badge bg-primary px-3 py-2">

USD Base Currency

</span>

</div>

</div>

</x-slot>

<div class="container-fluid py-4">

<div class="row g-4 mb-4">

<div class="col-lg-3 col-md-6">

<div class="card shadow border-0 stat-card h-100">

<div class="card-body">

<div class="d-flex justify-content-between">

<div>

<div class="text-muted small">

Supported Countries

</div>

<h2 class="fw-bold">

{{ $countries->count() }}

</h2>

<div class="small text-muted">

Countries

</div>

</div>

<div style="font-size:42px">

🌍

</div>

</div>

</div>

</div>

</div>

<div class="col-lg-3 col-md-6">

<div class="card shadow border-0 stat-card h-100">

<div class="card-body">

<div class="d-flex justify-content-between">

<div>

<div class="text-muted small">

Base Currency

</div>

<h2 class="fw-bold text-primary">

USD

</h2>

<div class="small text-muted">

Exchange Rate

</div>

</div>

<div style="font-size:42px">

💵

</div>

</div>

</div>

</div>

</div>

<div class="col-lg-3 col-md-6">

<div class="card shadow border-0 stat-card h-100">

<div class="card-body">

<div class="d-flex justify-content-between">

<div>

<div class="text-muted small">

API Status

</div>

<h2 class="fw-bold text-success">

ONLINE

</h2>

<div class="small text-muted">

Live Data

</div>

</div>

<div style="font-size:42px">

🛰️

</div>

</div>

</div>

</div>

</div>

<div class="col-lg-3 col-md-6">

<div class="card shadow border-0 stat-card h-100">

<div class="card-body">

<div class="d-flex justify-content-between">

<div>

<div class="text-muted small">

Currencies

</div>

<h2 class="fw-bold">

<span id="totalCurrency">0</span>

</h2>

<div class="small text-muted">

Available

</div>

</div>

<div style="font-size:42px">

💹

</div>

</div>

</div>

</div>

</div>

</div>
<div class="card shadow border-0 mb-4">

    <div class="card-body">

        <div class="row align-items-center">

            <div class="col-lg-4">

                <h4 class="fw-bold mb-1">

                    🔄 Smart Currency Converter

                </h4>

                <p class="text-muted mb-0">

                    Konversi mata uang secara real-time menggunakan kurs terbaru.

                </p>

            </div>

            <div class="col-lg-2">

                <label class="form-label small text-muted">

                    Amount

                </label>

                <input
                    type="number"
                    id="convAmount"
                    class="form-control"
                    value="1"
                    min="0">

            </div>

            <div class="col-lg-2">

                <label class="form-label small text-muted">

                    From

                </label>

                <select
                    id="convFrom"
                    class="form-select">

                    <option value="USD">

                        USD

                    </option>

                </select>

            </div>

            <div class="col-lg-1 text-center">

                <div style="font-size:34px;color:#3b82f6">

                    ⇄

                </div>

            </div>

            <div class="col-lg-2">

                <label class="form-label small text-muted">

                    To

                </label>

                <select
                    id="convTo"
                    class="form-select">

                    <option value="IDR">

                        IDR

                    </option>

                </select>

            </div>

            <div class="col-lg-1">

                <div
                    class="rounded-4 text-center p-3"
                    style="background:#EDF4FF;">

                    <div
                        class="small text-muted">

                        Result

                    </div>

                    <h4
                        id="convResult"
                        class="fw-bold text-primary mb-0">

                        -

                    </h4>

                </div>

            </div>

        </div>

    </div>

</div>



<div class="d-flex justify-content-between align-items-center mb-3">

    <div>

        <h4 class="fw-bold mb-1">

            ⭐ Popular Currency

        </h4>

        <p class="text-muted mb-0">

            Most traded currencies in the global market.

        </p>

    </div>

    <span class="badge bg-success">

        LIVE

    </span>

</div>



<div
    class="row g-4 mb-5"
    id="popularCurrencies">

</div>



<div class="card shadow border-0">

    <div class="card-body">

        <div class="row mb-3">

            <div class="col-lg-6">

                <h4 class="fw-bold mb-1">

                    🌍 Exchange Rate Table

                </h4>

                <p class="text-muted mb-0">

                    Semua mata uang berdasarkan 1 USD.

                </p>

            </div>

            <div class="col-lg-6">

                <input
                    type="text"
                    id="searchCurrency"
                    class="form-control"
                    placeholder="🔍 Cari negara atau kode mata uang...">

            </div>

        </div>

        <div class="table-responsive">

            <table
                class="table align-middle table-hover">

                <thead class="table-light">

                <tr>

                    <th>

                        Country

                    </th>

                    <th>

                        Currency

                    </th>

                    <th class="text-end">

                        Exchange Rate

                    </th>

                </tr>

                </thead>

                <tbody
                    id="currencyTable">
                    @foreach ($countries as $country)

<tr class="currency-row">

    <td class="country-name">

        {{ $country->name }}

    </td>

    <td>

        <span class="badge bg-primary">

            {{ $country->currency_code }}

        </span>

    </td>

    <td
        class="rate text-end fw-bold"
        data-code="{{ $country->currency_code }}">

        Loading...

    </td>

</tr>

@endforeach

</tbody>

</table>

</div>

</div>

</div>

</div>

@push('scripts')

<script>

const popularCodes = [

'USD',

'EUR',

'JPY',

'GBP',

'AUD',

'CAD',

'SGD',

'CNY',

'IDR'

];

let ratesData = {};

fetch('https://open.er-api.com/v6/latest/USD')

.then(response=>response.json())

.then(data=>{

ratesData=data.rates;

document.getElementById('totalCurrency').textContent=Object.keys(ratesData).length;

document.querySelectorAll('.rate').forEach(function(el){

const code=el.dataset.code;

el.innerHTML=ratesData[code]

? ratesData[code].toLocaleString(undefined,{maximumFractionDigits:4})

: '-';

});

const popular=document.getElementById('popularCurrencies');

popular.innerHTML='';

popularCodes.forEach(function(code){

if(ratesData[code]){

popular.innerHTML+=`

<div class="col-xl-3 col-lg-4 col-md-6">

<div class="card shadow-sm border-0 h-100">

<div class="card-body text-center">

<h6 class="text-muted">${code}</h6>

<h3 class="fw-bold text-primary">

${ratesData[code].toLocaleString(undefined,{maximumFractionDigits:2})}

</h3>

</div>

</div>

</div>

`;

}

});

const allCodes=Object.keys(ratesData).sort();

const from=document.getElementById('convFrom');

const to=document.getElementById('convTo');

from.innerHTML='';

to.innerHTML='';

allCodes.forEach(function(code){

from.innerHTML+=`<option value="${code}" ${code==='USD'?'selected':''}>${code}</option>`;

to.innerHTML+=`<option value="${code}" ${code==='IDR'?'selected':''}>${code}</option>`;

});

calculateConversion();

});

function calculateConversion(){

const amount=parseFloat(document.getElementById('convAmount').value)||0;

const from=document.getElementById('convFrom').value;

const to=document.getElementById('convTo').value;

if(ratesData[from]&&ratesData[to]){

const result=(amount/ratesData[from])*ratesData[to];

document.getElementById('convResult').innerHTML=

result.toLocaleString(undefined,{maximumFractionDigits:2});

}

}

document.getElementById('convAmount').addEventListener('input',calculateConversion);

document.getElementById('convFrom').addEventListener('change',calculateConversion);

document.getElementById('convTo').addEventListener('change',calculateConversion);

document.getElementById('searchCurrency').addEventListener('input',function(){

const keyword=this.value.toLowerCase();

document.querySelectorAll('.currency-row').forEach(function(row){

const text=row.innerText.toLowerCase();

row.style.display=text.includes(keyword)?'':'none';

});

});

</script>

@endpush

</x-app-layout>