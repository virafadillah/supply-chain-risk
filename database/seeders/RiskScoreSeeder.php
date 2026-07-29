<?php

namespace Database\Seeders;

use App\Models\RiskScore;
use Illuminate\Database\Seeder;

class RiskScoreSeeder extends Seeder
{
    public function run(): void
    {
        $scores = [
            ['id' => 1, 'country_id' => 1, 'weather_risk' => 22.05, 'inflation_risk' => 9.57, 'currency_risk' => 20.00, 'news_risk' => 0.00, 'total_risk' => 10.53, 'risk_level' => 'low', 'calculated_at' => now()],
            ['id' => 2, 'country_id' => 5, 'weather_risk' => 11.85, 'inflation_risk' => 15.86, 'currency_risk' => 20.00, 'news_risk' => 0.00, 'total_risk' => 8.73, 'risk_level' => 'low', 'calculated_at' => now()],
            ['id' => 5, 'country_id' => 2, 'weather_risk' => 25.05, 'inflation_risk' => 10.86, 'currency_risk' => 20.00, 'news_risk' => 0.00, 'total_risk' => 11.69, 'risk_level' => 'low', 'calculated_at' => now()],
            ['id' => 6, 'country_id' => 29, 'weather_risk' => 19.20, 'inflation_risk' => 100.00, 'currency_risk' => 20.00, 'news_risk' => 0.00, 'total_risk' => 27.76, 'risk_level' => 'low', 'calculated_at' => now()],
            ['id' => 7, 'country_id' => 21, 'weather_risk' => 3.60, 'inflation_risk' => 11.68, 'currency_risk' => 20.00, 'news_risk' => 0.00, 'total_risk' => 5.42, 'risk_level' => 'low', 'calculated_at' => now()],
            ['id' => 8, 'country_id' => 73, 'weather_risk' => 46.20, 'inflation_risk' => 7.09, 'currency_risk' => 20.00, 'news_risk' => 0.00, 'total_risk' => 17.28, 'risk_level' => 'low', 'calculated_at' => now()],
            ['id' => 9, 'country_id' => 40, 'weather_risk' => 21.00, 'inflation_risk' => 12.34, 'currency_risk' => 20.00, 'news_risk' => 0.00, 'total_risk' => 10.77, 'risk_level' => 'low', 'calculated_at' => now()],
            ['id' => 10, 'country_id' => 4, 'weather_risk' => 32.85, 'inflation_risk' => 14.37, 'currency_risk' => 20.00, 'news_risk' => 0.00, 'total_risk' => 14.73, 'risk_level' => 'low', 'calculated_at' => now()],
            ['id' => 11, 'country_id' => 3, 'weather_risk' => 14.70, 'inflation_risk' => 0.30, 'currency_risk' => 20.00, 'news_risk' => 0.00, 'total_risk' => 6.47, 'risk_level' => 'low', 'calculated_at' => now()],
            ['id' => 13, 'country_id' => 35, 'weather_risk' => 3.00, 'inflation_risk' => 16.55, 'currency_risk' => 20.00, 'news_risk' => 0.00, 'total_risk' => 6.21, 'risk_level' => 'low', 'calculated_at' => now()],
        ];

        foreach ($scores as $score) {
            RiskScore::updateOrCreate(['id' => $score['id']], $score);
        }
    }
}
