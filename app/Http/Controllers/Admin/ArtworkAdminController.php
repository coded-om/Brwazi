<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Artwork;
use Illuminate\Http\Request;

class ArtworkAdminController extends Controller
{
    public function index()
    {
        $arts = Artwork::with('user')->latest()->paginate(24);
        return view('admin.artworks.index', compact('arts'));
    }

    public function togglePublish(Artwork $artwork)
    {
        $artwork->status = $artwork->status === Artwork::STATUS_PUBLISHED
            ? Artwork::STATUS_DRAFT
            : Artwork::STATUS_PUBLISHED;
        $artwork->published_at = $artwork->status === Artwork::STATUS_PUBLISHED ? now() : null;
        $artwork->save();
        return back()->with('success', 'تم تحديث حالة النشر');
    }
}
