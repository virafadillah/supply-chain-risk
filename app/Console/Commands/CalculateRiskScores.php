<?php

namespace App\Console\Commands;

use App\Models\Country;
use App\Services\RiskScoringService;
use Illuminate\Console\Command;

class CalculateRiskScores extends Command
{
    protected $signature = 'risk:calculate {--all : Hitung untuk semua negara, bukan hanya top list}';
    protected $description = 'Calculate risk scores for top countries (or all with --all)';

    // Top 30 negara: major economies + hub perdagangan/logistik global
    protected array $topCountries = [
        'USA', 'CHN', 'DEU', 'JPN', 'IND', 'GBR', 'FRA', 'ITA', 'BRA', 'CAN',
        'RUS', 'KOR', 'AUS', 'MEX', 'IDN', 'NLD', 'SAU', 'TUR', 'CHE', 'POL',
        'ARE', 'SGP', 'THA', 'ZAF', 'VNM', 'MYS', 'PHL', 'EGY', 'NGA', 'ARG',
    ];

    public function handle(RiskScoringService $riskScoringService): void
    {
        if ($this->option('all')) {
            $countries = Country::all();
            $this->info('Mode: SEMUA negara (' . $countries->count() . ' negara). Ini akan memakan waktu lama.');
        } else {
            $countries = Country::whereIn('iso3', $this->topCountries)->get();
            $this->info('Mode: Top ' . $countries->count() . ' negara.');
        }

        $processed = 0;
        $failed = 0;

        foreach ($countries as $country) {
            try {
                $riskScoringService->calculateForCountry($country);
                $processed++;
                $this->line("✓ {$country->name}");
            } catch (\Exception $e) {
                $failed++;
                $this->line("✗ {$country->name} (gagal: " . $e->getMessage() . ")");
                report($e);
                continue;
            }
        }

        $this->info("Selesai! {$processed} negara berhasil, {$failed} gagal.");
    }
}