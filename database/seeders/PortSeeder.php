<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\Port;
use Illuminate\Database\Seeder;

class PortSeeder extends Seeder
{
    public function run(): void
    {
        $ports = [
            ['unlocode' => 'DEHAM', 'name' => 'Port of Hamburg', 'iso3' => 'DEU', 'latitude' => 53.5459, 'longitude' => 9.9695, 'port_type' => 'seaport'],
            ['unlocode' => 'CNSHA', 'name' => 'Port of Shanghai', 'iso3' => 'CHN', 'latitude' => 31.2304, 'longitude' => 121.4737, 'port_type' => 'seaport'],
            ['unlocode' => 'IDJKT', 'name' => 'Tanjung Priok Port', 'iso3' => 'IDN', 'latitude' => -6.1045, 'longitude' => 106.8865, 'port_type' => 'seaport'],
            ['unlocode' => 'AUSYD', 'name' => 'Port of Sydney', 'iso3' => 'AUS', 'latitude' => -33.8523, 'longitude' => 151.2108, 'port_type' => 'seaport'],
            ['unlocode' => 'USLAX', 'name' => 'Port of Los Angeles', 'iso3' => 'USA', 'latitude' => 33.7292, 'longitude' => -118.2620, 'port_type' => 'seaport'],
        ];

        foreach ($ports as $port) {
            $country = Country::where('iso3', $port['iso3'])->first();

            if ($country) {
                Port::updateOrCreate(
                    ['unlocode' => $port['unlocode']],
                    [
                        'name' => $port['name'],
                        'country_id' => $country->id,
                        'latitude' => $port['latitude'],
                        'longitude' => $port['longitude'],
                        'port_type' => $port['port_type'],
                    ]
                );
            }
        }
    }
}