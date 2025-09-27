<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\HomepageSetting;
use App\Models\Auction;
use App\Models\Book;
use Illuminate\Http\Request;

class IndexController extends Controller
{
    public function index()
    {
        $settings = HomepageSetting::first();

        // Fetch a small curated set of auctions for the homepage
        // Order priority: live -> soon -> recently ended (optional limit)
        $now = now();
        $live = Auction::with(['artwork.images'])
            ->where('starts_at', '<=', $now)
            ->where('ends_at', '>', $now)
            ->orderBy('ends_at')
            ->limit(3)
            ->get();

        $soon = Auction::with(['artwork.images'])
            ->where('starts_at', '>', $now)
            ->orderBy('starts_at')
            ->limit(max(0, 3 - $live->count()))
            ->get();

        $ended = collect();
        if ($live->count() + $soon->count() < 3) {
            $needed = 3 - ($live->count() + $soon->count());
            $ended = Auction::with(['artwork.images'])
                ->where('ends_at', '<=', $now)
                ->orderByDesc('ends_at')
                ->limit($needed)
                ->get();
        }

        $auctions = $live->concat($soon)->concat($ended);

        $books = Book::query()
            ->with(['authors:id,name', 'publisher:id,name'])
            ->where('status', 'published')
            ->latest('id')
            ->take(12)
            ->get();

        return view("index", compact('settings', 'auctions', 'books'));
    }
}
