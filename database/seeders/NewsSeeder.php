<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\NewsCache;
use Illuminate\Database\Seeder;

class NewsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $idnd = Country::where('iso3', 'IDN')->first();
        $usa  = Country::where('iso3', 'USA')->first();
        $chn  = Country::where('iso3', 'CHN')->first();
        $sgp  = Country::where('iso3', 'SGP')->first();

        $sampleNews = [
            [
                'country_id' => $idnd?->id,
                'title' => 'Global Freight Rates Normalize Following Red Sea Route Adjustments',
                'description' => 'Container shipping rates have stabilized as major ocean carriers complete strategic route realignments around the Cape of Good Hope, reducing unexpected surcharges for international trade routes.',
                'image' => 'https://images.unsplash.com/photo-1578575437130-527eed3abbec?w=600&auto=format&fit=crop&q=80',
                'url' => 'https://example.com/news/freight-rates-normalize',
                'source' => 'Global Logistics Review',
                'sentiment' => 'positive',
                'category' => 'shipping',
                'positive_score' => 3,
                'negative_score' => 1,
                'published_at' => now()->subHours(2),
            ],
            [
                'country_id' => $usa?->id,
                'title' => 'US East Coast Port Automation Upgrade Enhances Cargo Throughput',
                'description' => 'Major ports along the US Atlantic coast implement next-generation automated yard cranes and digital tracking systems, reducing vessel turn-around times by up to 25%.',
                'image' => 'https://images.unsplash.com/photo-1586528116311-ad8dd3c8310d?w=600&auto=format&fit=crop&q=80',
                'url' => 'https://example.com/news/us-port-automation',
                'source' => 'Maritime Trade News',
                'sentiment' => 'positive',
                'category' => 'logistics',
                'positive_score' => 4,
                'negative_score' => 0,
                'published_at' => now()->subHours(5),
            ],
            [
                'country_id' => $sgp?->id,
                'title' => 'Singapore Maritime Hub Expands Green Shipping Corridors in Southeast Asia',
                'description' => 'The Port of Singapore launches eco-friendly bunkering facilities for modern LNG and methanol-powered container ships, pioneering sustainable maritime supply chains.',
                'image' => 'https://images.unsplash.com/photo-1518241353330-0f7941c2d9b5?w=600&auto=format&fit=crop&q=80',
                'url' => 'https://example.com/news/singapore-green-corridors',
                'source' => 'Asia Logistics Pulse',
                'sentiment' => 'positive',
                'category' => 'shipping',
                'positive_score' => 5,
                'negative_score' => 0,
                'published_at' => now()->subHours(8),
            ],
            [
                'country_id' => $chn?->id,
                'title' => 'New Regional Bilateral Trade Agreement Eases Semiconductor Supply Bottlenecks',
                'description' => 'Key Asian economies sign a streamlined customs treaty aimed at accelerating cross-border clearance for critical electronic components and industrial raw materials.',
                'image' => 'https://images.unsplash.com/photo-1542744094-3a31b272c490?w=600&auto=format&fit=crop&q=80',
                'url' => 'https://example.com/news/trade-agreement-electronics',
                'source' => 'International Trade Insights',
                'sentiment' => 'positive',
                'category' => 'trade',
                'positive_score' => 4,
                'negative_score' => 1,
                'published_at' => now()->subDay(),
            ],
            [
                'country_id' => $usa?->id,
                'title' => 'Global Inflation Slowdown Drives Stronger Demand for Consumer Freight',
                'description' => 'As central banks signal interest rate stability, retail inventory restocking boosts air freight volume and intermodal rail transport across North America and Europe.',
                'image' => 'https://images.unsplash.com/photo-1618042164219-62c820f10723?w=600&auto=format&fit=crop&q=80',
                'url' => 'https://example.com/news/inflation-freight-demand',
                'source' => 'Economy & Supply Chain Journal',
                'sentiment' => 'positive',
                'category' => 'economy',
                'positive_score' => 3,
                'negative_score' => 1,
                'published_at' => now()->subDays(2),
            ],
            [
                'country_id' => $idnd?->id,
                'title' => 'Indonesia Expands Logistics Infrastructure in Major Island Ports',
                'description' => 'Government initiatives to modernize inter-island maritime hubs improve cold chain logistics for seafood and agricultural exports across the archipelago.',
                'image' => 'https://images.unsplash.com/photo-1509316975850-ff9c5deb0cd9?w=600&auto=format&fit=crop&q=80',
                'url' => 'https://example.com/news/indonesia-port-expansion',
                'source' => 'Nusantara Logistics Briefing',
                'sentiment' => 'positive',
                'category' => 'logistics',
                'positive_score' => 4,
                'negative_score' => 0,
                'published_at' => now()->subDays(3),
            ],
        ];

        foreach ($sampleNews as $data) {
            NewsCache::updateOrCreate(
                ['title' => $data['title']],
                $data
            );
        }
    }
}
