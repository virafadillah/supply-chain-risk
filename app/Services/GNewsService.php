<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class GNewsService
{
    protected ?string $apiKey;

    public function __construct()
    {
        $this->apiKey = config('services.gnews.key');
    }

    public function search(string $query, int $max = 10): array
    {
        if (empty($this->apiKey)) {
            return []; // Skip kalau API key belum diset, jangan sampai error
        }

        $response = Http::get('https://gnews.io/api/v4/search', [
            'q' => $query,
            'lang' => 'en',
            'max' => $max,
            'apikey' => $this->apiKey,
        ]);

        if (!$response->successful()) {
            return [];
        }

        return $response->json()['articles'] ?? [];
    }

    // Lexicon-based sentiment analysis sederhana
    public function analyzeSentiment(string $text): array
    {
        $positiveWords = ['growth', 'increase', 'profit', 'stable', 'improve', 'recovery', 'boost'];
        $negativeWords = ['war', 'crisis', 'inflation', 'delay', 'disaster', 'conflict', 'shortage'];

        $words = str_word_count(strtolower($text), 1);

        $positiveScore = count(array_intersect($words, $positiveWords));
        $negativeScore = count(array_intersect($words, $negativeWords));

        $sentiment = $positiveScore > $negativeScore ? 'positive'
            : ($negativeScore > $positiveScore ? 'negative' : 'neutral');

        return compact('positiveScore', 'negativeScore', 'sentiment');
    }

    public function calculateNewsRisk(array $articles): float
    {
        if (empty($articles)) return 0;

        $negativeCount = 0;
        foreach ($articles as $article) {
            $analysis = $this->analyzeSentiment(($article['title'] ?? '') . ' ' . ($article['description'] ?? ''));
            if ($analysis['sentiment'] === 'negative') $negativeCount++;
        }

        return round(($negativeCount / count($articles)) * 100, 2);
    }
}