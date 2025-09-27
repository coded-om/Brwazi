<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Auction;
use App\Models\AuctionRequest;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AuctionAdminController extends Controller
{
    public function index()
    {
        $requests = AuctionRequest::with(['artwork:id,title', 'user:id,fname,lname'])
            ->orderBy('status')
            ->orderByDesc('created_at')
            ->paginate(20);
        return view('admin.auctions.requests', compact('requests'));
    }

    public function approve(Request $request, AuctionRequest $auctionRequest)
    {
        $data = $request->validate([
            'start_price' => ['required', 'numeric', 'min:0'],
            'bid_increment' => ['required', 'numeric', 'min:0.01'],
            'starts_at' => ['required', 'date', 'after_or_equal:now'],
            'duration_minutes' => ['required', 'integer', 'min:5', 'max:10080'],
            'reserve_price' => ['nullable', 'numeric', 'min:0'],
            'buy_now_price' => ['nullable', 'numeric', 'min:0'],
        ]);

        $startsAt = Carbon::parse($data['starts_at']);
        $endsAt = (clone $startsAt)->addMinutes($data['duration_minutes']);

        // Prevent duplicate active auctions for the same artwork
        $existsActive = Auction::where('artwork_id', $auctionRequest->artwork_id)
            ->whereIn('status', ['draft', 'scheduled', 'live'])
            ->exists();
        if ($existsActive) {
            return back()->withErrors(['start_price' => 'هناك مزاد نشط/مجدول مسبقاً لهذه اللوحة.'])->withInput();
        }

        $auction = Auction::create([
            'artwork_id' => $auctionRequest->artwork_id,
            'status' => 'scheduled',
            'starts_at' => $startsAt,
            'ends_at' => $endsAt,
            'start_price' => $data['start_price'],
            'bid_increment' => $data['bid_increment'],
            'reserve_price' => $data['reserve_price'] ?? null,
            'buy_now_price' => $data['buy_now_price'] ?? null,
            'approved_by_admin_id' => $request->user('admin')?->id,
            'notes' => null,
        ]);

        $auctionRequest->update([
            'status' => 'approved',
            'reviewed_by' => $request->user('admin')?->id,
            'reviewed_at' => now(),
        ]);

        return back()->with('success', 'تمت الموافقة وإنشاء المزاد #' . $auction->id);
    }

    public function reject(Request $request, AuctionRequest $auctionRequest)
    {
        $data = $request->validate([
            'admin_notes' => ['nullable', 'string', 'max:2000'],
        ]);
        $auctionRequest->update([
            'status' => 'rejected',
            'admin_notes' => $data['admin_notes'] ?? null,
            'reviewed_by' => $request->user('admin')?->id,
            'reviewed_at' => now(),
        ]);
        return back()->with('success', 'تم رفض الطلب');
    }
}
