<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Artwork;
use App\Models\User;
use App\Services\CountryService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function dashboard()
    {
        /** @var User $user */
        $user = Auth::user();
        $tab = request()->query('tab', 'all'); // all | favorites | drafts

        // Load artworks for gallery based on selected tab
        if ($tab === 'favorites') {
            // Artworks liked by current user (could be from any artist)
            $artworks = Artwork::with(['images', 'user'])
                ->whereHas('likes', function ($q) use ($user) {
                    $q->where('user_id', $user->id);
                })
                ->latest()
                ->get();
        } else {
            // Own artworks; 'all' means published (visible), 'drafts' shows drafts
            $query = Artwork::with('images')->where('user_id', $user->id)->latest();
            if ($tab === 'drafts') {
                $query->where('status', Artwork::STATUS_DRAFT);
            } else {
                $query->where('status', Artwork::STATUS_PUBLISHED);
            }
            $artworks = $query->get();
        }
        $artworkImageUrls = $artworks->map(function ($a) {
            return $a->primary_image_url;
        })->filter()->values();

        // TODO: Add real statistics queries later
        $stats = [
            'auctions_count' => 0,
            'wins_count' => 0,
            'posts_count' => 0,
            'insurance_balance' => 0
        ];

        return view("userViwes.index", compact('user', 'stats', 'artworks', 'artworkImageUrls', 'tab'));
    }

    public function profile()
    {
        /** @var User $user */
        $user = Auth::user();
        $countries = CountryService::getAllCountries();

        return view("userViwes.profile", compact('user', 'countries'));
    }

    public function updateProfile(Request $request)
    {
        /** @var User $user */
        $user = Auth::user();

        $request->validate([
            'fname' => 'required|string|max:255',
            'lname' => 'required|string|max:255',
            'email' => [
                'required',
                'email',
                Rule::unique('users')->ignore($user->id),
            ],
            'phone_number' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'birthday' => 'nullable|date|before:today',
            'bio' => 'nullable|string|max:500',
            'tagline' => 'nullable|string|in:' . implode(',', User::getTaglineOptions()),
            'profile_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        // Handle profile image upload
        $updateData = [
            'fname' => $request->fname,
            'lname' => $request->lname,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'country' => $request->country,
            'birthday' => $request->birthday,
            'bio' => $request->bio,
            'tagline' => $request->tagline,
        ];

        if ($request->hasFile('profile_image')) {
            // Delete old image if exists
            if ($user->ProfileImage && Storage::disk('public')->exists($user->ProfileImage)) {
                Storage::disk('public')->delete($user->ProfileImage);
            }

            // Store new image
            $imagePath = $request->file('profile_image')->store('profiles', 'public');
            $updateData['ProfileImage'] = $imagePath;
        }

        try {
            $user->update($updateData);
        } catch (\Illuminate\Database\QueryException $e) {
            // MySQL duplicate key error code 1062
            if ((int) $e->errorInfo[1] === 1062) {
                if (function_exists('notify')) {
                    notify()->error('هذا البريد الإلكتروني مستخدم بالفعل');
                }
                return back()->withErrors(['email' => 'هذا البريد الإلكتروني مستخدم بالفعل'])->withInput();
            }
            throw $e; // rethrow other DB exceptions
        }

        if (function_exists('notify')) {
            notify()->success('تم تحديث الملف الشخصي بنجاح');
        }
        return redirect()->route('user.profile')->with('success', 'تم تحديث الملف الشخصي بنجاح!');
    }

    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password' => 'required',
            'password' => 'required|min:8|confirmed',
        ]);

        /** @var User $user */
        $user = Auth::user();

        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'كلمة المرور الحالية غير صحيحة']);
        }

        $user->update([
            'password' => Hash::make($request->password),
        ]);

        return redirect()->route('user.profile')->with('success', 'تم تحديث كلمة المرور بنجاح');
    }

    public function updateProfileImage(Request $request)
    {
        $request->validate([
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        /** @var User $user */
        $user = Auth::user();

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($user->ProfileImage && file_exists(storage_path('app/public/' . $user->ProfileImage))) {
                unlink(storage_path('app/public/' . $user->ProfileImage));
            }

            $imagePath = $request->file('image')->store('profiles', 'public');

            $user->update([
                'ProfileImage' => $imagePath,
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'تم تحديث صورة الملف الشخصي بنجاح',
                'data' => [
                    'url' => asset('storage/' . $imagePath)
                ]
            ]);
        }

        return response()->json([
            'status' => 'error',
            'message' => 'فشل في رفع الصورة'
        ], 400);
    }

    /**
     * Simple messages placeholder page (redirects to messaging system)
     */
    public function messages()
    {
        return redirect()->route('user.messages');
    }
}
