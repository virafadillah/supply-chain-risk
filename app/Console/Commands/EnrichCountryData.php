<?php

namespace App\Console\Commands;

use App\Models\Country;
use App\Services\WorldBankService;
use Illuminate\Console\Command;

class EnrichCountryData extends Command
{
    protected $signature = 'countries:enrich';
    protected $description = 'Ambil dan simpan GDP, Inflasi, Populasi dari World Bank untuk Top 30 negara';

    protected array $topCountries = [
        'USA', 'CHN', 'DEU', 'JPN', 'IND', 'GBR', 'FRA', 'ITA', 'BRA', 'CAN',
        'RUS', 'KOR', 'AUS', 'MEX', 'IDN', 'NLD', 'SAU', 'TUR', 'CHE', 'POL',
        'ARE', 'SGP', 'THA', 'ZAF', 'VNM', 'MYS', 'PHL', 'EGY', 'NGA', 'ARG',
    ];

    public function handle(WorldBankService $worldBank): void
    {
        $countries = Country::whereIn('iso3', $this->topCountries)->get();

        foreach ($countries as $country) {
            try {
                $gdp = $worldBank->getGdp($country->iso3);
                $inflation = $worldBank->getInflation($country->iso3);
                $population = $worldBank->getPopulation($country->iso3);

                $country->update([
                    'gdp' => $gdp ?? $country->gdp,
                    'inflation_rate' => $inflation ?? $country->inflation_rate,
                    'population' => $population ?? $country->population,
                ]);

                $this->line("✓ {$country->name}: GDP={$gdp}, Inflation={$inflation}");
            } catch (\Exception $e) {
                $this->line("✗ {$country->name} (gagal, dilewati)");
                report($e);
                continue;
            }
        }

        $this->info('Selesai memperbarui data negara.');
    }
}