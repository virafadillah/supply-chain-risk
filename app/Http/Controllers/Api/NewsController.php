<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\NewsCache;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NewsController extends Controller
{
    public function index(Request $request): JsonResponse
    {
        $query = NewsCache::with('country')->latest('published_at');

        if ($request->filled('country')) {
            $query->whereHas('country', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->input('country') . '%');
            });
        }

        if ($request->filled('sentiment')) {
            $query->where('sentiment', $request->input('sentiment'));
        }

        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }

        $limit = min((int) $request->input('limit', 20), 100);

        $news = $query->limit($limit)->get()->map(function (NewsCache $item) {
            return [
                'id' => $item->id,
                'title' => $item->title,
                'description' => $item->description,
                'url' => $item->url,
                'source' => $item->source,
                'sentiment' => $item->sentiment,
                'category' => $item->category,
                'positive_score' => $item->positive_score,
                'negative_score' => $item->negative_score,
                'published_at' => optional($item->published_at)->toIso8601String(),
                'country' => $item->country?->name,
            ];
        });

        return response()->json([
            'total' => $news->count(),
            'data' => $news,
        ]);
    }
}