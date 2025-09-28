<?php

namespace App\Http\Controllers;

use App\Models\Exhibition;
use Illuminate\Http\Request;

class ExhibitionController extends Controller
{
    public function index(Request $request)
    {
        $perPage = min(max((int) $request->get('per_page', 24), 1), 60);
        $exhibitions = Exhibition::published()
            ->orderByDesc('starts_at')
            ->orderByDesc('id')
            ->paginate($perPage);

        if ($request->wantsJson()) {
            return response()->json([
                'data' => $exhibitions->getCollection()->map(fn($e) => $this->mapResource($e)),
                'meta' => [
                    'current_page' => $exhibitions->currentPage(),
                    'last_page' => $exhibitions->lastPage(),
                    'per_page' => $exhibitions->perPage(),
                    'total' => $exhibitions->total(),
                    'next_page_url' => $exhibitions->nextPageUrl(),
                    'prev_page_url' => $exhibitions->previousPageUrl(),
                ],
            ]);
        }

        return view('exhibitions.index', [
            'exhibitions' => $exhibitions, // paginator instance
        ]);
    }

    public function show(Exhibition $exhibition)
    {
        abort_unless($exhibition->is_published, 404);

        return view('exhibitions.show', [
            'exhibition' => $exhibition,
        ]);
    }

    protected function mapResource(Exhibition $e): array
    {
        return [
            'id' => $e->id,
            'title' => $e->title,
            'slug' => $e->slug,
            'short' => $e->short_description,
            'city' => $e->city,
            'country' => $e->country,
            'address' => $e->address,
            'lat' => $e->latitude,
            'lng' => $e->longitude,
            'starts_at' => optional($e->starts_at)->toIso8601String(),
            'ends_at' => optional($e->ends_at)->toIso8601String(),
            'cover' => $e->cover_image_path ? asset_url($e->cover_image_path, 'imgs/pic/Book.png') : asset('imgs/pic/Book.png'),
            'url' => route('exhibitions.show', $e),
        ];
    }
}
