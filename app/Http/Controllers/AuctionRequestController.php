<?php

namespace App\Http\Controllers;

use App\Models\Artwork;
use App\Models\AuctionRequest;
use Illuminate\Http\Request;

class AuctionRequestController extends Controller
{
    public function create(Request $request)
    {
        $user = $request->user();
        // User's artworks (prefer published and draft)
        $artworks = Artwork::where('user_id', $user->id)
            ->orderByDesc('created_at')
            ->get(['id', 'title', 'status', 'auction_start_price']);

        return view('auctions.request', compact('artworks'));
    }

    public function store(Request $request)
    {
        $user = $request->user();
        $data = $request->validate([
            'artwork_id' => ['required', 'integer', 'exists:artworks,id'],
            'desired_start_price' => ['required', 'numeric', 'min:0'],
            'suggested_start_at' => ['nullable', 'date', 'after_or_equal:now'],
            'suggested_duration' => ['nullable', 'integer', 'min:5', 'max:10080'], // up to 7 days
            'admin_notes' => ['nullable', 'string', 'max:2000'],
        ]);

        // Ensure the artwork belongs to the user
        $artwork = Artwork::where('id', $data['artwork_id'])->where('user_id', $user->id)->firstOrFail();

        AuctionRequest::create([
            'artwork_id' => $artwork->id,
            'user_id' => $user->id,
            'status' => 'pending',
            'desired_start_price' => $data['desired_start_price'],
            'suggested_start_at' => $data['suggested_start_at'] ?? null,
            'suggested_duration' => $data['suggested_duration'] ?? null,
            'admin_notes' => $data['admin_notes'] ?? null,
        ]);

        return redirect()->route('user.dashboard')->with('success', 'تم إرسال طلب المزاد للمراجعة');
    }
}
