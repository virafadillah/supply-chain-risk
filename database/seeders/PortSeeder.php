<?php

namespace Database\Seeders;

use App\Models\Port;
use Illuminate\Database\Seeder;

class PortSeeder extends Seeder
{
    public function run(): void
    {
        $ports = [
            ['id' => 1, 'country_id' => 3, 'name' => 'Port of Shanghai', 'latitude' => 31.2304, 'longitude' => 121.4737, 'unlocode' => 'CNSHA'],
            ['id' => 2, 'country_id' => 38, 'name' => 'Port of Singapore', 'latitude' => 1.2644, 'longitude' => 103.8200, 'unlocode' => 'SGSIN'],
            ['id' => 3, 'country_id' => 3, 'name' => 'Port of Ningbo-Zhoushan', 'latitude' => 29.8683, 'longitude' => 121.5440, 'unlocode' => 'CNNGB'],
            ['id' => 4, 'country_id' => 3, 'name' => 'Port of Shenzhen', 'latitude' => 22.5431, 'longitude' => 114.0579, 'unlocode' => 'CNSZX'],
            ['id' => 5, 'country_id' => 3, 'name' => 'Port of Guangzhou', 'latitude' => 23.1291, 'longitude' => 113.2644, 'unlocode' => 'CNGZG'],
            ['id' => 6, 'country_id' => null, 'name' => 'Port of Busan', 'latitude' => 35.1796, 'longitude' => 129.0756, 'unlocode' => 'KRPUS'],
            ['id' => 7, 'country_id' => 3, 'name' => 'Port of Qingdao', 'latitude' => 36.0671, 'longitude' => 120.3826, 'unlocode' => 'CNTAO'],
            ['id' => 8, 'country_id' => 3, 'name' => 'Port of Hong Kong', 'latitude' => 22.3193, 'longitude' => 114.1694, 'unlocode' => 'HKHKG'],
            ['id' => 9, 'country_id' => 3, 'name' => 'Port of Tianjin', 'latitude' => 39.0842, 'longitude' => 117.2009, 'unlocode' => 'CNTSN'],
            ['id' => 10, 'country_id' => 37, 'name' => 'Port Klang', 'latitude' => 3.0044, 'longitude' => 101.3936, 'unlocode' => 'MYPKG'],
            ['id' => 11, 'country_id' => 14, 'name' => 'Port of Rotterdam', 'latitude' => 51.9496, 'longitude' => 4.1453, 'unlocode' => 'NLRTM'],
            ['id' => 12, 'country_id' => 40, 'name' => 'Port of Antwerp', 'latitude' => 51.2993, 'longitude' => 4.4014, 'unlocode' => 'BEANR'],
            ['id' => 13, 'country_id' => 2, 'name' => 'Port of Hamburg', 'latitude' => 53.5459, 'longitude' => 9.9695, 'unlocode' => 'DEHAM'],
            ['id' => 14, 'country_id' => 2, 'name' => 'Port of Bremerhaven', 'latitude' => 53.5396, 'longitude' => 8.5809, 'unlocode' => 'DEBRV'],
            ['id' => 15, 'country_id' => 8, 'name' => 'Port of Valencia', 'latitude' => 39.4561, 'longitude' => -0.3222, 'unlocode' => 'ESVLC'],
            ['id' => 16, 'country_id' => 8, 'name' => 'Port of Algeciras', 'latitude' => 36.1408, 'longitude' => -5.4562, 'unlocode' => 'ESALG'],
            ['id' => 17, 'country_id' => 22, 'name' => 'Port of Piraeus', 'latitude' => 37.9475, 'longitude' => 23.6350, 'unlocode' => 'GRPIR'],
            ['id' => 18, 'country_id' => null, 'name' => 'Port of Felixstowe', 'latitude' => 51.9539, 'longitude' => 1.3510, 'unlocode' => 'GBFXT'],
            ['id' => 19, 'country_id' => 6, 'name' => 'Port of Le Havre', 'latitude' => 49.4938, 'longitude' => 0.1079, 'unlocode' => 'FRLEH'],
            ['id' => 20, 'country_id' => 7, 'name' => 'Port of Gioia Tauro', 'latitude' => 38.4241, 'longitude' => 15.8994, 'unlocode' => 'ITGIT'],
            ['id' => 21, 'country_id' => null, 'name' => 'Port of Los Angeles', 'latitude' => 33.7395, 'longitude' => -118.2610, 'unlocode' => 'USLAX'],
            ['id' => 22, 'country_id' => null, 'name' => 'Port of Long Beach', 'latitude' => 33.7550, 'longitude' => -118.2160, 'unlocode' => 'USLGB'],
            ['id' => 23, 'country_id' => null, 'name' => 'Port of New York and New Jersey', 'latitude' => 40.6700, 'longitude' => -74.1200, 'unlocode' => 'USNYC'],
            ['id' => 24, 'country_id' => null, 'name' => 'Port of Savannah', 'latitude' => 32.1178, 'longitude' => -81.1420, 'unlocode' => 'USSAV'],
            ['id' => 25, 'country_id' => 9, 'name' => 'Port of Vancouver', 'latitude' => 49.2827, 'longitude' => -123.1207, 'unlocode' => 'CAVAN'],
            ['id' => 26, 'country_id' => 10, 'name' => 'Port of Santos', 'latitude' => -23.9608, 'longitude' => -46.3339, 'unlocode' => 'BRSSZ'],
            ['id' => 27, 'country_id' => 11, 'name' => 'Port of Manzanillo', 'latitude' => 19.0531, 'longitude' => -104.3159, 'unlocode' => 'MXZLO'],
            ['id' => 28, 'country_id' => 29, 'name' => 'Port of Buenos Aires', 'latitude' => -34.6083, 'longitude' => -58.3712, 'unlocode' => 'ARBUE'],
            ['id' => 29, 'country_id' => 32, 'name' => 'Port of Callao', 'latitude' => -12.0553, 'longitude' => -77.1447, 'unlocode' => 'PECLL'],
            ['id' => 30, 'country_id' => 30, 'name' => 'Port of Valparaiso', 'latitude' => -33.0472, 'longitude' => -71.6127, 'unlocode' => 'CLVAP'],
            ['id' => 31, 'country_id' => 1, 'name' => 'Tanjung Priok Port', 'latitude' => -6.1045, 'longitude' => 106.8800, 'unlocode' => 'IDJKT'],
            ['id' => 32, 'country_id' => 1, 'name' => 'Tanjung Perak Port', 'latitude' => -7.2004, 'longitude' => 112.7370, 'unlocode' => 'IDSUB'],
            ['id' => 33, 'country_id' => 1, 'name' => 'Belawan Port', 'latitude' => 3.7861, 'longitude' => 98.6900, 'unlocode' => 'IDBLW'],
            ['id' => 34, 'country_id' => 34, 'name' => 'Port of Laem Chabang', 'latitude' => 13.0827, 'longitude' => 100.8833, 'unlocode' => 'THLCH'],
            ['id' => 35, 'country_id' => 35, 'name' => 'Port of Ho Chi Minh City', 'latitude' => 10.7626, 'longitude' => 106.6602, 'unlocode' => 'VNSGN'],
            ['id' => 36, 'country_id' => 36, 'name' => 'Port of Manila', 'latitude' => 14.5906, 'longitude' => 120.9647, 'unlocode' => 'PHMNL'],
            ['id' => 37, 'country_id' => null, 'name' => 'Jawaharlal Nehru Port', 'latitude' => 18.9490, 'longitude' => 72.9525, 'unlocode' => 'INNSA'],
            ['id' => 38, 'country_id' => null, 'name' => 'Port of Chennai', 'latitude' => 13.1067, 'longitude' => 80.2963, 'unlocode' => 'INMAA'],
            ['id' => 39, 'country_id' => 45, 'name' => 'Port of Karachi', 'latitude' => 24.8467, 'longitude' => 66.9853, 'unlocode' => 'PKKHI'],
            ['id' => 40, 'country_id' => null, 'name' => 'Jebel Ali Port', 'latitude' => 25.0117, 'longitude' => 55.0617, 'unlocode' => 'AEJEA'],
            ['id' => 41, 'country_id' => null, 'name' => 'Port of Jeddah', 'latitude' => 21.4858, 'longitude' => 39.1925, 'unlocode' => 'SAJED'],
            ['id' => 42, 'country_id' => 24, 'name' => 'Port Said', 'latitude' => 31.2565, 'longitude' => 32.2841, 'unlocode' => 'EGPSD'],
            ['id' => 43, 'country_id' => null, 'name' => 'Port of Durban', 'latitude' => -29.8587, 'longitude' => 31.0218, 'unlocode' => 'ZADUR'],
            ['id' => 44, 'country_id' => 26, 'name' => 'Port of Lagos (Apapa)', 'latitude' => 6.4531, 'longitude' => 3.3958, 'unlocode' => 'NGAPP'],
            ['id' => 45, 'country_id' => 27, 'name' => 'Port of Mombasa', 'latitude' => -4.0435, 'longitude' => 39.6682, 'unlocode' => 'KEMBA'],
            ['id' => 46, 'country_id' => 4, 'name' => 'Port of Melbourne', 'latitude' => -37.8400, 'longitude' => 144.9257, 'unlocode' => 'AUMEL'],
            ['id' => 47, 'country_id' => 4, 'name' => 'Port of Sydney (Botany Bay)', 'latitude' => -33.9614, 'longitude' => 151.1962, 'unlocode' => 'AUSYD'],
            ['id' => 48, 'country_id' => 4, 'name' => 'Port of Brisbane', 'latitude' => -27.3820, 'longitude' => 153.1670, 'unlocode' => 'AUBNE'],
            ['id' => 49, 'country_id' => null, 'name' => 'Port of Auckland', 'latitude' => -36.8434, 'longitude' => 174.7645, 'unlocode' => 'NZAKL'],
            ['id' => 50, 'country_id' => 5, 'name' => 'Port of Tokyo', 'latitude' => 35.6300, 'longitude' => 139.7900, 'unlocode' => 'JPTYO'],
            ['id' => 51, 'country_id' => 5, 'name' => 'Port of Yokohama', 'latitude' => 35.4437, 'longitude' => 139.6380, 'unlocode' => 'JPYOK'],
            ['id' => 52, 'country_id' => 5, 'name' => 'Port of Kobe', 'latitude' => 34.6901, 'longitude' => 135.1955, 'unlocode' => 'JPUKB'],
        ];

        foreach ($ports as $port) {
            $port['port_type'] = $port['port_type'] ?? 'seaport';
            Port::updateOrCreate(['id' => $port['id']], $port);
        }
    }
}