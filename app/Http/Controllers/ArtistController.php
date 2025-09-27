<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Report;
use Illuminate\Support\Facades\Auth;

class ArtistController extends Controller
{
    /**
     * Display a listing of artists (users with published artworks or with a profile image/name).
     */
    public function index(Request $request)
    {
        $q = (string) $request->query('q', '');
        $sort = (string) $request->query('sort', 'latest'); // latest | popular

        $artists = User::query()
            ->where(function ($w) use ($q) {
                if ($q !== '') {
                    $w->where('fname', 'like', "%{$q}%")
                        ->orWhere('lname', 'like', "%{$q}%")
                        ->orWhere('email', 'like', "%{$q}%");
                }
            })
            ->where('status', '!=', User::STATUS_BANNED)
            ->withCount([
                'artworks as artworks_published_count' => function ($q) {
                    $q->where('status', \App\Models\Artwork::STATUS_PUBLISHED);
                }
            ])
            ->when($sort === 'popular', function ($q) {
                $q->orderByDesc('artworks_published_count')->orderByDesc('id');
            }, function ($q) {
                $q->orderByDesc('id');
            })
            ->paginate(24)
            ->appends($request->except('page'));

        return view('artists.index', compact('artists', 'q', 'sort'));
    }

    /**
     * Display a single artist public profile.
     */
    public function show(User $artist, Request $request)
    {
        // Basic guard: prevent showing banned users publicly
        if ($artist->isBanned()) {
            abort(404);
        }

        // Fetch published artworks (paginate) - small page size for profile
        $artworks = $artist->artworks()
            ->where('status', \App\Models\Artwork::STATUS_PUBLISHED)
            ->with(['images:id,artwork_id,path,is_primary,sort_order'])
            ->paginate(12)
            ->appends($request->except('page'));

        return view('artists.show', [
            'artist' => $artist,
            'artworks' => $artworks,
        ]);
    }

    /**
     * Report an artist (user) profile.
     */
    public function report(User $artist, Request $request)
    {
        if (!Auth::check()) {
            return $request->expectsJson()
                ? response()->json(['success' => false, 'message' => 'يجب تسجيل الدخول'], 401)
                : abort(401);
        }
        if ($artist->id === Auth::id()) {
            return $request->expectsJson()
                ? response()->json(['success' => false, 'message' => 'لا يمكنك الإبلاغ عن نفسك'], 422)
                : back()->withErrors(['report' => 'لا يمكنك الإبلاغ عن نفسك']);
        }
        $data = $request->validate([
            'type' => 'required|string|max:50',
            'details' => 'nullable|string|max:2000',
        ]);

        // Prevent duplicate active report by same reporter on same target & type
        $exists = Report::where('reporter_id', Auth::id())
            ->where('target_type', User::class)
            ->where('target_id', $artist->id)
            ->where('type', $data['type'])
            ->whereIn('status', [Report::STATUS_PENDING, Report::STATUS_REVIEWING])
            ->exists();
        if ($exists) {
            return $request->expectsJson()
                ? response()->json(['success' => false, 'message' => 'تم تقديم بلاغ سابق قيد المراجعة'], 409)
                : back()->withErrors(['report' => 'تم تقديم بلاغ سابق']);
        }

        $report = Report::create([
            'reporter_id' => Auth::id(),
            'type' => $data['type'],
            'target_type' => User::class,
            'target_id' => $artist->id,
            'details' => $data['details'] ?? null,
            'status' => Report::STATUS_PENDING,
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'id' => $report->id]);
        }
        if (function_exists('notify'))
            notify()->success('تم إرسال البلاغ');
        return back()->with('success', 'تم إرسال البلاغ');
    }
}
