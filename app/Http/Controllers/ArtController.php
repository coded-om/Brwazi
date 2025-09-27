<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\CountryService;
use App\Models\Artwork;
use App\Models\User as AppUser;
use App\Models\ArtworkImage;
use App\Models\ArtworkLike;
use App\Models\Report;
use App\Models\Tag;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use App\Services\ImageService;

class ArtController extends Controller
{

    public function index()
    {
        $categories = Artwork::categories();
        $perPage = (int) request('per_page', 40);
        $perPage = max(12, min(80, $perPage));
        $category = request('category');
        $tag = request('tag');
        $explore = request('mode', 'explore') === 'explore';
        $artistId = (int) request('artist', 0);

        $base = Artwork::query()
            ->with([
                'images' => function ($q) {
                    $q->orderBy('sort_order');
                }
            ])
            ->where('status', Artwork::STATUS_PUBLISHED)
            ->where('images_count', '>', 0);

        if ($category && isset($categories[$category])) {
            $base->where('category', $category);
        }
        if ($tag) {
            $base->whereHas('tags', function ($q) use ($tag) {
                $q->where('name', $tag);
            });
        }

        // Optional artist filter
        $selectedArtist = null;
        if ($artistId > 0) {
            $base->where('user_id', $artistId);
            $selectedArtist = \App\Models\User::select('id', 'fname', 'lname', 'email', 'ProfileImage')->find($artistId);
        }

        // Featured (unique artists, most liked/recent)
        if ($selectedArtist) {
            // If filtering by artist, spotlight their top artworks in the slider
            $featured = (clone $base)
                ->orderByDesc('likes_count')
                ->orderByDesc('published_at')
                ->limit(12)
                ->get();
        } else {
            $featuredSample = (clone $base)
                ->orderByDesc('likes_count')
                ->orderByDesc('published_at')
                ->limit(60)
                ->get();
            $featured = [];
            $seenUsers = [];
            foreach ($featuredSample as $art) {
                if (!isset($seenUsers[$art->user_id])) {
                    $featured[] = $art;
                    $seenUsers[$art->user_id] = true;
                }
                if (count($featured) >= 12)
                    break;
            }
        }

        // Explore-like feed: interleave artworks from many artists
        if ($explore) {
            $sample = (clone $base)->inRandomOrder()->limit(200)->get();
            // group by user
            $byUser = [];
            foreach ($sample as $art) {
                $byUser[$art->user_id][] = $art;
            }
            // round-robin pick
            $artworks = [];
            $keys = array_keys($byUser);
            $idx = 0;
            while (count($artworks) < $perPage && !empty($keys)) {
                $key = $keys[$idx % count($keys)];
                if (!empty($byUser[$key])) {
                    $artworks[] = array_shift($byUser[$key]);
                    if (empty($byUser[$key])) {
                        array_splice($keys, $idx % max(1, count($keys)), 1);
                        // do not increment idx to stay at same position after removal
                        continue;
                    }
                }
                $idx++;
                if ($idx > 10000)
                    break; // safety
            }
            $artworks = collect($artworks);
            $paginator = null; // no pagination for explore (could add cursor later)
        } else {
            // simple latest feed with pagination
            $paginator = (clone $base)->orderByDesc('published_at')->paginate($perPage)->withQueryString();
            $artworks = collect($paginator->items());
        }

        // Top artists sidebar: by total likes, fallback by artworks count (max 10)
        $topArtists = \App\Models\User::query()
            ->join('artworks', function ($q) {
                $q->on('users.id', '=', 'artworks.user_id')
                    ->where('artworks.status', Artwork::STATUS_PUBLISHED)
                    ->where('artworks.images_count', '>', 0);
            })
            ->where('users.status', '!=', \App\Models\User::STATUS_BANNED)
            ->groupBy('users.id', 'users.fname', 'users.lname', 'users.email', 'users.ProfileImage')
            ->select('users.id', 'users.fname', 'users.lname', 'users.email', 'users.ProfileImage')
            ->selectRaw('COALESCE(SUM(artworks.likes_count),0) as total_likes')
            ->selectRaw('COUNT(artworks.id) as artworks_count')
            ->orderByDesc('total_likes')
            ->orderByDesc('artworks_count')
            ->limit(10)
            ->get();

        // AJAX partial (for infinite scroll): return masonry HTML + next page URL
        if (request()->boolean('partial')) {
            $cols = (int) request('cols', 4);
            $html = view('components.masonry', [
                'items' => $artworks,
                'columns' => $cols,
            ])->render();
            $next = $paginator ? $paginator->appends(request()->except('page', 'partial', 'cols'))->nextPageUrl() : null;
            return response()->json(['html' => $html, 'next' => $next]);
        }

        return view('artViwes.index', [
            'categories' => $categories,
            'selectedCategory' => $category,
            'selectedTag' => $tag,
            'featured' => collect($featured),
            'artworks' => $artworks,
            'paginator' => $paginator,
            'explore' => $explore,
            'topArtists' => $topArtists,
            'selectedArtist' => $selectedArtist,
        ]);
    }

    /**
     * Show create artwork form.
     */
    public function create()
    {
        // Only verified users can create artworks
        $u = Auth::user();
        if (!($u instanceof AppUser) || !$u->isVerified()) {
            return redirect()->route('verification.apply')
                ->with('error', 'فقط الأعضاء الموثقون يمكنهم رفع الأعمال. الرجاء تقديم طلب التوثيق.');
        }
        $categories = Artwork::categories();
        $popularTags = Tag::query()
            ->select('tags.id', 'tags.name')
            ->join('artwork_tag', 'tags.id', '=', 'artwork_tag.tag_id')
            ->groupBy('tags.id', 'tags.name')
            ->orderByRaw('COUNT(artwork_tag.artwork_id) DESC')
            ->limit(15)
            ->pluck('name');
        $mediums = [];
        try {
            if (class_exists(\App\Models\ArtMedium::class)) {
                $mediums = \App\Models\ArtMedium::listOptions();
            }
        } catch (\Throwable $e) {
            $mediums = [];
        }
        return view('artViwes.create', compact('categories', 'popularTags', 'mediums'));
    }

    /**
     * Store new artwork placeholder (to be implemented later).
     */
    public function store(Request $request)
    {
        $u = Auth::user();
        if (!($u instanceof AppUser) || !$u->isVerified()) {
            return redirect()->route('verification.apply')
                ->with('error', 'فقط الأعضاء الموثقون يمكنهم رفع الأعمال. الرجاء تقديم طلب التوثيق.');
        }
        $action = $request->input('action', 'draft');
        // dynamic lists from DB-backed helpers
        $categoryOptions = array_keys(\App\Models\Artwork::categories());
        $mediumOptions = [];
        try {
            $mediumOptions = array_keys(\App\Models\ArtMedium::listOptions());
        } catch (\Throwable $e) {
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|min:10',
            'category' => ['required', 'string', Rule::in($categoryOptions)],
            'type' => ['required', 'string', Rule::in(['art', '3d', 'vector'])],
            'medium' => array_filter(['nullable', 'string', 'max:50', $mediumOptions ? Rule::in($mediumOptions) : null]),
            'weight' => 'nullable|numeric|min:0|max:9999.99',
            'year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'dimensions' => 'nullable|string|max:100',
            'price' => 'nullable|numeric|min:0|required_if:sale_mode,fixed',
            'sale_mode' => 'required|string|in:display,fixed,auction',
            'allow_offers' => 'nullable|boolean',
            'edition_type' => 'nullable|string|in:original,copy|required_if:sale_mode,fixed',
            'copy_digital' => 'nullable|boolean',
            'copy_printed' => 'nullable|boolean',
            'auction_start_price' => 'nullable|numeric|min:0|required_if:sale_mode,auction',
            'uploaded_images' => 'required|array|min:1',
            'uploaded_images.*' => 'string',
            'primary_image' => 'nullable|string',
            'tags' => 'nullable|array|max:10',
            'tags.*' => 'string|min:2|max:20'
        ], [
            'uploaded_images.required' => 'يجب رفع صورة واحدة على الأقل',
        ]);

        $status = $action === 'publish' ? Artwork::STATUS_PUBLISHED : Artwork::STATUS_DRAFT;

        // Normalize booleans
        $allowOffers = (bool) ($validated['allow_offers'] ?? false);
        $copyDigital = (bool) ($validated['copy_digital'] ?? false);
        $copyPrinted = (bool) ($validated['copy_printed'] ?? false);

        // Clear irrelevant fields based on sale_mode
        $price = $validated['sale_mode'] === 'fixed' ? ($validated['price'] ?? null) : null;
        $auctionStart = $validated['sale_mode'] === 'auction' ? ($validated['auction_start_price'] ?? null) : null;
        $edition = $validated['sale_mode'] === 'fixed' ? ($validated['edition_type'] ?? null) : null;

        $artwork = Artwork::create([
            'user_id' => Auth::id(),
            'title' => $validated['title'],
            'description' => $validated['description'],
            'category' => $validated['category'],
            'type' => $validated['type'] ?? 'art',
            'medium' => $validated['medium'] ?? null,
            'weight' => $validated['weight'] ?? null,
            'year' => $validated['year'] ?? null,
            'dimensions' => $validated['dimensions'] ?? null,
            'price' => $price,
            'sale_mode' => $validated['sale_mode'],
            'allow_offers' => $allowOffers,
            'edition_type' => $edition,
            'copy_digital' => $copyDigital,
            'copy_printed' => $copyPrinted,
            'auction_start_price' => $auctionStart,
            'status' => $status,
            'published_at' => $status === Artwork::STATUS_PUBLISHED ? now() : null,
        ]);

        // Move images from temp to permanent
        $primary = $validated['primary_image'] ?? $validated['uploaded_images'][0];
        $imagesCreated = 0;
        foreach ($validated['uploaded_images'] as $idx => $tempPath) {
            // Ensure path is inside temp_artworks
            if (!str_starts_with($tempPath, 'temp_artworks/'))
                continue;
            $filename = basename($tempPath);
            $newPath = 'artworks/' . $artwork->id . '/' . $filename;
            if (Storage::disk('public')->exists($tempPath)) {
                Storage::disk('public')->move($tempPath, $newPath);
                ArtworkImage::create([
                    'artwork_id' => $artwork->id,
                    'path' => $newPath,
                    'is_primary' => $tempPath === $primary,
                    'sort_order' => $idx,
                ]);
                $imagesCreated++;
            }
        }

        if ($imagesCreated > 0) {
            $artwork->increment('images_count', $imagesCreated);
        }

        // Tags
        if ($request->filled('tags')) {
            $rawTags = $request->input('tags');
            if (is_array($rawTags)) {
                $ids = [];
                foreach ($rawTags as $t) {
                    $name = trim(mb_strtolower(preg_replace('/^[#]+/u', '', $t)));
                    if ($name === '' || mb_strlen($name) < 2)
                        continue;
                    $name = mb_substr($name, 0, 20);
                    $tag = Tag::firstOrCreate(['name' => $name]);
                    $ids[] = $tag->id;
                }
                if ($ids)
                    $artwork->tags()->sync($ids);
            }
        }

        return redirect()->route('user.dashboard')
            ->with('success', $status === Artwork::STATUS_PUBLISHED ? 'تم نشر العمل بنجاح' : 'تم حفظ العمل كمسودة');
    }

    /**
     * Temporary single image upload (AJAX)
     */
    public function uploadTempImage(Request $request, ImageService $images)
    {
        if ($request->user() && method_exists($request->user(), 'isBanned') && $request->user()->isBanned()) {
            return response()->json([
                'success' => false,
                'message' => 'تم تعطيل الرفع لحسابك. يرجى التواصل مع قسم الدعم.'
            ], 403);
        }
        $maxMb = (int) config('app.image_upload_max_mb', 8);
        $request->validate([
            'image' => 'required|file|max:' . ($maxMb * 1024)
        ]);

        $file = $request->file('image');
        try {
            $result = $images->processToTempWebp($file, 'temp_artworks', 1600, 80);
            if (!($result['success'] ?? false)) {
                return response()->json(['success' => false, 'message' => $result['message'] ?? 'ملف غير صالح'], 422);
            }
            return response()->json($result);
        } catch (\Throwable $e) {
            return response()->json([
                'success' => false,
                'message' => 'تعذر معالجة الصورة حالياً'
            ], 422);
        }
    }

    /**
     * Simple preview (returns JSON with rendered snippet)
     */
    public function preview(Request $request)
    {
        $html = view('artViwes.partials.preview-card', [
            'data' => $request->all(),
        ])->render();
        return response()->json(['html' => $html]);
    }

    /**
     * Show artwork details
     */
    public function show(Artwork $artwork)
    {
        $artwork->load([
            'images' => function ($q) {
                $q->orderBy('sort_order');
            },
            'tags'
        ]);
        $canEdit = Auth::check() && $artwork->user_id === Auth::id();
        return view('artViwes.show', compact('artwork', 'canEdit'));
    }

    /**
     * Edit form
     */
    public function edit(Artwork $artwork)
    {
        $u = Auth::user();
        abort_unless($u && $u->id === $artwork->user_id, 403);
        $artwork->load(['images', 'tags']);
        $categories = Artwork::categories();
        $popularTags = Tag::query()
            ->select('tags.id', 'tags.name')
            ->join('artwork_tag', 'tags.id', '=', 'artwork_tag.tag_id')
            ->groupBy('tags.id', 'tags.name')
            ->orderByRaw('COUNT(artwork_tag.artwork_id) DESC')
            ->limit(15)
            ->pluck('name');
        $mediums = [];
        try {
            if (class_exists(\App\Models\ArtMedium::class)) {
                $mediums = \App\Models\ArtMedium::listOptions();
            }
        } catch (\Throwable $e) {
        }
        return view('artViwes.edit', compact('artwork', 'categories', 'popularTags', 'mediums'));
    }

    /**
     * Update artwork
     */
    public function update(Request $request, Artwork $artwork)
    {
        $u = Auth::user();
        abort_unless($u && $u->id === $artwork->user_id, 403);

        $action = $request->input('action', $artwork->status === Artwork::STATUS_PUBLISHED ? 'publish' : 'draft');
        $categoryOptions = array_keys(\App\Models\Artwork::categories());
        $mediumOptions = [];
        try {
            $mediumOptions = array_keys(\App\Models\ArtMedium::listOptions());
        } catch (\Throwable $e) {
        }

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string|min:10',
            'category' => ['required', 'string', Rule::in($categoryOptions)],
            'type' => ['nullable', 'string', Rule::in(['art', '3d', 'vector'])],
            'medium' => array_filter(['nullable', 'string', 'max:50', $mediumOptions ? Rule::in($mediumOptions) : null]),
            'weight' => 'nullable|numeric|min:0|max:9999.99',
            'year' => 'nullable|integer|min:1900|max:' . date('Y'),
            'dimensions' => 'nullable|string|max:100',
            'price' => 'nullable|numeric|min:0|required_if:sale_mode,fixed',
            'sale_mode' => 'required|string|in:display,fixed,auction',
            'allow_offers' => 'nullable|boolean',
            'edition_type' => 'nullable|string|in:original,copy|required_if:sale_mode,fixed',
            'copy_digital' => 'nullable|boolean',
            'copy_printed' => 'nullable|boolean',
            'auction_start_price' => 'nullable|numeric|min:0|required_if:sale_mode,auction',
            // Images editing: allow zero (keep existing) or array
            'uploaded_images' => 'nullable|array',
            'uploaded_images.*' => 'string',
            'primary_image' => 'nullable|string',
            'tags' => 'nullable|array|max:10',
            'tags.*' => 'string|min:2|max:20'
        ]);

        $status = $action === 'publish' ? Artwork::STATUS_PUBLISHED : Artwork::STATUS_DRAFT;

        $allowOffers = (bool) ($validated['allow_offers'] ?? false);
        $copyDigital = (bool) ($validated['copy_digital'] ?? false);
        $copyPrinted = (bool) ($validated['copy_printed'] ?? false);

        $price = $validated['sale_mode'] === 'fixed' ? ($validated['price'] ?? null) : null;
        $auctionStart = $validated['sale_mode'] === 'auction' ? ($validated['auction_start_price'] ?? null) : null;
        $edition = $validated['sale_mode'] === 'fixed' ? ($validated['edition_type'] ?? null) : null;

        $artwork->fill([
            'title' => $validated['title'],
            'description' => $validated['description'],
            'category' => $validated['category'],
            'type' => $validated['type'] ?? $artwork->type,
            'medium' => $validated['medium'] ?? null,
            'weight' => $validated['weight'] ?? null,
            'year' => $validated['year'] ?? null,
            'dimensions' => $validated['dimensions'] ?? null,
            'price' => $price,
            'sale_mode' => $validated['sale_mode'],
            'allow_offers' => $allowOffers,
            'edition_type' => $edition,
            'copy_digital' => $copyDigital,
            'copy_printed' => $copyPrinted,
            'auction_start_price' => $auctionStart,
            'status' => $status,
            'published_at' => $status === Artwork::STATUS_PUBLISHED ? ($artwork->published_at ?: now()) : null,
        ])->save();

        // Handle new uploads (optional)
        $newTemp = $validated['uploaded_images'] ?? [];
        $madePrimaryPath = $validated['primary_image'] ?? null;
        $imagesCreated = 0;
        foreach ($newTemp as $idx => $tempPath) {
            if (!str_starts_with($tempPath, 'temp_artworks/'))
                continue;
            $filename = basename($tempPath);
            $newPath = 'artworks/' . $artwork->id . '/' . $filename;
            if (Storage::disk('public')->exists($tempPath)) {
                Storage::disk('public')->move($tempPath, $newPath);
                ArtworkImage::create([
                    'artwork_id' => $artwork->id,
                    'path' => $newPath,
                    'is_primary' => false,
                    'sort_order' => ($artwork->images()->max('sort_order') ?? 0) + $idx + 1,
                ]);
                $imagesCreated++;
                if ($madePrimaryPath === $tempPath) {
                    // set primary after creation
                    ArtworkImage::where('artwork_id', $artwork->id)->update(['is_primary' => false]);
                    ArtworkImage::where('artwork_id', $artwork->id)->where('path', $newPath)->update(['is_primary' => true]);
                }
            }
        }
        if ($imagesCreated > 0) {
            $artwork->increment('images_count', $imagesCreated);
        }

        // If primary_image refers to an existing path, update flags
        if ($madePrimaryPath && !str_starts_with($madePrimaryPath, 'temp_artworks/')) {
            ArtworkImage::where('artwork_id', $artwork->id)->update(['is_primary' => false]);
            ArtworkImage::where('artwork_id', $artwork->id)->where('path', $madePrimaryPath)->update(['is_primary' => true]);
        }

        // Tags sync
        if ($request->filled('tags')) {
            $rawTags = $request->input('tags');
            if (is_array($rawTags)) {
                $ids = [];
                foreach ($rawTags as $t) {
                    $name = trim(mb_strtolower(preg_replace('/^[#]+/u', '', $t)));
                    if ($name === '' || mb_strlen($name) < 2)
                        continue;
                    $name = mb_substr($name, 0, 20);
                    $tag = Tag::firstOrCreate(['name' => $name]);
                    $ids[] = $tag->id;
                }
                $artwork->tags()->sync($ids);
            }
        } else {
            $artwork->tags()->sync([]);
        }

        // Feedback via notify
        if (function_exists('notify')) {
            notify()->success('تم تحديث العمل بنجاح');
        } else {
            session()->flash('success', 'تم تحديث العمل بنجاح');
        }

        return redirect()->route('art.show', $artwork);
    }

    /**
     * Like an artwork
     */
    public function like(Artwork $artwork)
    {
        $userId = Auth::id();
        if (!$userId)
            return response()->json(['success' => false], 401);
        $existing = ArtworkLike::where('artwork_id', $artwork->id)->where('user_id', $userId)->exists();
        if (!$existing) {
            ArtworkLike::create(['artwork_id' => $artwork->id, 'user_id' => $userId]);
            $artwork->increment('likes_count');
        }
        return response()->json(['success' => true, 'likes_count' => (int) $artwork->likes_count]);
    }

    /**
     * Unlike an artwork
     */
    public function unlike(Artwork $artwork)
    {
        $userId = Auth::id();
        if (!$userId)
            return response()->json(['success' => false], 401);
        $deleted = ArtworkLike::where('artwork_id', $artwork->id)->where('user_id', $userId)->delete();
        if ($deleted) {
            // avoid negative
            if ($artwork->likes_count > 0)
                $artwork->decrement('likes_count');
        }
        return response()->json(['success' => true, 'likes_count' => max(0, (int) $artwork->likes_count - 0)]);
    }

    /**
     * Submit a report about an artwork (AJAX)
     */
    public function report(Request $request, Artwork $artwork)
    {
        $user = $request->user();
        if (!$user) {
            return response()->json(['success' => false, 'message' => 'يلزم تسجيل الدخول'], 401);
        }
        // Validate type and optional details
        $types = array_keys(Report::types());
        $validated = $request->validate([
            'type' => 'required|string|in:' . implode(',', $types),
            'details' => 'nullable|string|max:2000',
            'image_id' => 'nullable|integer'
        ], [
            'type.required' => 'اختر نوع البلاغ',
            'type.in' => 'نوع البلاغ غير صالح'
        ]);

        // Rights violation requires details
        if ($validated['type'] === Report::TYPE_RIGHTS && empty($validated['details'])) {
            return response()->json(['success' => false, 'message' => 'فضلاً اشرح المشكلة المتعلقة بالحقوق'], 422);
        }

        $image = null;
        if (!empty($validated['image_id'])) {
            $image = $artwork->images()->where('id', $validated['image_id'])->first();
            if (!$image) {
                return response()->json(['success' => false, 'message' => 'الصورة المحددة غير مرتبطة بهذا العمل'], 422);
            }
        }

        try {
            $report = Report::create([
                'reporter_id' => $user->id,
                'type' => $validated['type'],
                'target_type' => $image ? ArtworkImage::class : Artwork::class,
                'target_id' => $image ? $image->id : $artwork->id,
                'details' => $validated['details'] ?? null,
                'status' => Report::STATUS_PENDING,
            ]);
        } catch (\Illuminate\Database\QueryException $e) {
            // Likely duplicate unique composite
            if (str_contains($e->getMessage(), 'reports_unique_user_target_type')) {
                $duplicateMsg = $image
                    ? 'سبق وأن أرسلت بلاغاً مماثلاً لهذه الصورة'
                    : 'سبق وأن أرسلت بلاغاً مماثلاً لهذا العمل';
                return response()->json(['success' => false, 'message' => $duplicateMsg], 409);
            }
            throw $e;
        }

        return response()->json(['success' => true, 'id' => $report->id]);
    }


}
