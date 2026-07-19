<?php

namespace Database\Seeders;

use App\Models\Country;
use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        $countries = [
            ['name' => 'Germany', 'iso2' => 'DE', 'iso3' => 'DEU', 'capital' => 'Berlin', 'region' => 'Europe', 'currency_code' => 'EUR', 'currency_name' => 'Euro', 'latitude' => 51.1657, 'longitude' => 10.4515],
            ['name' => 'China', 'iso2' => 'CN', 'iso3' => 'CHN', 'capital' => 'Beijing', 'region' => 'Asia', 'currency_code' => 'CNY', 'currency_name' => 'Chinese Yuan', 'latitude' => 35.8617, 'longitude' => 104.1954],
            ['name' => 'Indonesia', 'iso2' => 'ID', 'iso3' => 'IDN', 'capital' => 'Jakarta', 'region' => 'Asia', 'currency_code' => 'IDR', 'currency_name' => 'Indonesian Rupiah', 'latitude' => -0.7893, 'longitude' => 113.9213],
            ['name' => 'Australia', 'iso2' => 'AU', 'iso3' => 'AUS', 'capital' => 'Canberra', 'region' => 'Oceania', 'currency_code' => 'AUD', 'currency_name' => 'Australian Dollar', 'latitude' => -25.2744, 'longitude' => 133.7751],
            ['name' => 'United States', 'iso2' => 'US', 'iso3' => 'USA', 'capital' => 'Washington D.C.', 'region' => 'Americas', 'currency_code' => 'USD', 'currency_name' => 'US Dollar', 'latitude' => 37.0902, 'longitude' => -95.7129],
        ];

        foreach ($countries as $country) {
            Country::updateOrCreate(['iso3' => $country['iso3']], $country);
        }
    }
}