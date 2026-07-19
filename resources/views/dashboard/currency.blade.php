<x-app-layout>
    <x-slot name="header"><h4>💱 Kurs Mata Uang</h4></x-slot>

    {{-- WIDGET KONVERTER CEPAT --}}
    <div class="stat-card mb-3" style="border-top: 3px solid #3b5bdb;">
        <div class="stat-label mb-3">🔄 KONVERTER CEPAT</div>
        <div class="row g-2 align-items-end">
            <div class="col-md-3 col-6">
                <label class="form-label small text-muted mb-1">Jumlah</label>
                <input type="number" id="convAmount" class="form-control" value="1" min="0">
            </div>
            <div class="col-md-3 col-6">
                <label class="form-label small text-muted mb-1">Dari</label>
                <select id="convFrom" class="form-select">
                    <option value="USD" selected>USD</option>
                </select>
            </div>
            <div class="col-md-1 text-center d-none d-md-block">
                <span style="font-size: 1.3rem; color: #3b5bdb;">→</span>
            </div>
            <div class="col-md-3 col-6">
                <label class="form-label small text-muted mb-1">Ke</label>
                <select id="convTo" class="form-select">
                    <option value="IDR" selected>IDR</option>
                </select>
            </div>
            <div class="col-md-2 col-6">
                <div class="text-center py-2" style="background: #e6ecfb; border-radius: 8px;">
                    <div class="small text-muted">Hasil</div>
                    <div id="convResult" class="fw-bold" style="color: #2f4bc0;">-</div>
                </div>
            </div>
        </div>
    </div>

    {{-- CARD MATA UANG POPULER --}}
    <div class="stat-label mb-2 mt-4">⭐ MATA UANG POPULER (vs USD)</div>
    <div class="row g-3 mb-4" id="popularCurrencies">
        {{-- diisi otomatis lewat JS --}}
    </div>

    {{-- TABEL LENGKAP --}}
    <div class="stat-card">
        <div class="stat-label mb-3">📋 SEMUA NEGARA</div>
        <input type="text" id="searchCurrency" class="form-control mb-3" placeholder="🔍 Cari negara atau mata uang...">

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead><tr><th>Negara</th><th>Mata Uang</th><th class="text-end">Kurs (per 1 USD)</th></tr></thead>
                <tbody id="currencyTable">
                    @foreach ($countries as $country)
                        <tr class="currency-row">
                            <td class="country-name">
                                @if($country->code)
                                    <span class="fi fi-{{ strtolower($country->code) }} me-2"></span>
                                @endif
                                {{ $country->name }}
                            </td>
                            <td><span class="badge" style="background: #e6ecfb; color: #2f4bc0;">{{ $country->currency_code }}</span></td>
                            <td class="rate text-end fw-bold" data-code="{{ $country->currency_code }}">Loading...</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @push('scripts')
    <script>
        const popularCodes = ['USD', 'EUR', 'JPY', 'GBP', 'AUD', 'SGD', 'CNY', 'IDR'];
        let ratesData = {};

        fetch('https://open.er-api.com/v6/latest/USD')
            .then(res => res.json())
            .then(data => {
                ratesData = data.rates || {};

                document.querySelectorAll('.rate').forEach(el => {
                    const code = el.dataset.code;
                    el.textContent = ratesData[code] ? ratesData[code].toLocaleString(undefined, {maximumFractionDigits: 4}) : '-';
                });

                const container = document.getElementById('popularCurrencies');
                popularCodes.forEach(code => {
                    if (ratesData[code] !== undefined) {
                        const col = document.createElement('div');
                        col.className = 'col-6 col-md-3';
                        col.innerHTML = `
                            <div class="stat-card text-center py-3">
                                <div class="stat-sub mb-1">${code}</div>
                                <div class="fw-bold" style="font-size: 1.2rem; color: #1a1d26;">
                                    ${code === 'USD' ? '1.00' : ratesData[code].toLocaleString(undefined, {maximumFractionDigits: 2})}
                                </div>
                            </div>`;
                        container.appendChild(col);
                    }
                });

                const allCodes = Object.keys(ratesData).sort();
                const fromSelect = document.getElementById('convFrom');
                const toSelect = document.getElementById('convTo');
                fromSelect.innerHTML = '';
                toSelect.innerHTML = '';
                allCodes.forEach(code => {
                    fromSelect.innerHTML += `<option value="${code}" ${code === 'USD' ? 'selected' : ''}>${code}</option>`;
                    toSelect.innerHTML += `<option value="${code}" ${code === 'IDR' ? 'selected' : ''}>${code}</option>`;
                });

                calculateConversion();
            });

        function calculateConversion() {
            const amount = parseFloat(document.getElementById('convAmount').value) || 0;
            const from = document.getElementById('convFrom').value;
            const to = document.getElementById('convTo').value;

            if (ratesData[from] && ratesData[to]) {
                const result = (amount / ratesData[from]) * ratesData[to];
                document.getElementById('convResult').textContent = result.toLocaleString(undefined, {maximumFractionDigits: 2});
            }
        }

        document.getElementById('convAmount').addEventListener('input', calculateConversion);
        document.getElementById('convFrom').addEventListener('change', calculateConversion);
        document.getElementById('convTo').addEventListener('change', calculateConversion);

        document.getElementById('searchCurrency').addEventListener('input', function (e) {
            const keyword = e.target.value.toLowerCase();
            document.querySelectorAll('.currency-row').forEach(row => {
                const name = row.querySelector('.country-name').textContent.toLowerCase();
                row.style.display = name.includes(keyword) ? '' : 'none';
            });
        });
    </script>
    @endpush
</x-app-layout>