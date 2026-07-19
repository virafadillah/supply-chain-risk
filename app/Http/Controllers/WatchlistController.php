<?php

namespace App\Http\Controllers;

use App\Models\Country;
use App\Models\Watchlist;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class WatchlistController extends Controller
{
    public function index(): View
    {
        $watchlists = auth()->user()
            ->watchlists()
            ->with(['country.latestRiskScore'])
            ->get();

        return view('watchlist.index', compact('watchlists'));
    }

    public function toggle(Country $country): RedirectResponse
    {
        $existing = Watchlist::where('user_id', auth()->id())
            ->where('country_id', $country->id)
            ->first();

        if ($existing) {
            $existing->delete();
            $message = "{$country->name} dihapus dari watchlist.";
        } else {
            Watchlist::create([
                'user_id' => auth()->id(),
                'country_id' => $country->id,
            ]);
            $message = "{$country->name} ditambahkan ke watchlist.";
        }

        return back()->with('success', $message);
    }
}