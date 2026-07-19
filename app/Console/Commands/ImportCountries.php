<?php

namespace App\Console\Commands;

use App\Models\Country;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

class ImportCountries extends Command
{
    protected $signature = 'countries:import';
    protected $description = 'Import semua negara dari dataset publik (mledoze/countries)';

    public function handle(): void
    {
        $this->info('Mengambil data negara...');

        $url = 'https://raw.githubusercontent.com/mledoze/countries/master/countries.json';

        try {
            $response = Http::timeout(30)->retry(2, 1000)->get($url);
        } catch (\Exception $e) {
            $this->error('Gagal konek: ' . $e->getMessage());
            return;
        }

        if (!$response->successful()) {
            $this->error('Gagal mengambil data. Status: ' . $response->status());
            return;
        }

        $countries = $response->json();

        if (empty($countries) || !is_array($countries)) {
            $this->error('Data kosong atau tidak valid.');
            return;
        }

        $this->info('Total data diterima: ' . count($countries));

        $imported = 0;

        foreach ($countries as $c) {
            $iso3 = $c['cca3'] ?? null;
            $name = $c['name']['common'] ?? null;

            if (empty($iso3) || empty($name)) {
                continue;
            }

            $currencyCode = null;
            $currencyName = null;

            if (!empty($c['currencies']) && is_array($c['currencies'])) {
                $currencyCode = array_key_first($c['currencies']);
                $currencyName = $c['currencies'][$currencyCode]['name'] ?? null;
            }

            $capital = $c['capital'][0] ?? null;
            $latlng = $c['latlng'] ?? [null, null];

            Country::updateOrCreate(
                ['iso3' => $iso3],
                [
                    'name' => $name,
                    'iso2' => $c['cca2'] ?? substr($iso3, 0, 2),
                    'capital' => $capital,
                    'region' => $c['region'] ?? null,
                    'currency_code' => $currencyCode,
                    'currency_name' => $currencyName,
                    'latitude' => $latlng[0] ?? null,
                    'longitude' => $latlng[1] ?? null,
                    'population' => $c['population'] ?? null,
                ]
            );

            $imported++;
        }

        $this->info("Selesai! {$imported} negara berhasil diimport/diperbarui.");
    }
}