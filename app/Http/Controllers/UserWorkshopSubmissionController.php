<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreUserWorkshopRequest;
use App\Models\Workshop;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class UserWorkshopSubmissionController extends Controller
{
    public function create(): View
    {
        return view('workshops.submit');
    }

    public function store(StoreUserWorkshopRequest $request): RedirectResponse
    {
        $data = $request->validated();

        $slugBase = Str::slug($data['title']);
        $slug = $slugBase;
        $i = 1;
        while (Workshop::where('slug', $slug)->exists()) {
            $slug = $slugBase . '-' . $i++;
        }

        $coverPath = null;
        if ($request->hasFile('cover_image')) {
            $imageService = app(\App\Services\ImageService::class);
            $coverProcessed = $imageService->coverWideWebp($request->file('cover_image'), 1280, 'workshops/covers');
            $coverPath = $coverProcessed['path'];
        }

        $presenterAvatar = null;
        if ($request->hasFile('presenter_avatar')) {
            $imageService = $imageService ?? app(\App\Services\ImageService::class);
            $avatarProcessed = $imageService->uploadAndCrop($request->file('presenter_avatar'), 'workshops/presenters', 400, 400);
            $presenterAvatar = $avatarProcessed['path'];
        }

        Workshop::create([
            'title' => $data['title'],
            'slug' => $slug,
            'presenter_name' => $data['presenter_name'],
            'presenter_bio' => $data['presenter_bio'] ?? null,
            'presenter_avatar_path' => $presenterAvatar,
            'art_type' => $data['art_type'] ?? null,
            'starts_at' => $data['starts_at'],
            'duration_minutes' => $data['duration_minutes'] ?? null,
            'location' => $data['location'] ?? null,
            'short_description' => $data['short_description'] ?? null,
            'external_apply_url' => $data['external_apply_url'] ?? null,
            'cover_image_path' => $coverPath,
            'is_published' => false, // user submissions start unpublished
            'is_approved' => false, // require admin approval
            'submitted_by_user_id' => Auth::id(),
        ]);

        return redirect()->route('workshops.index')
            ->with('success', 'تم إرسال الورشة للمراجعة من قبل الإدارة. سيتم نشرها بعد الموافقة.');
    }
}
