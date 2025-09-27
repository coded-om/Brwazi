<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreWorkshopRegistrationRequest;
use App\Models\Workshop;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class WorkshopController extends Controller
{
    public function index(): View
    {
        $workshops = Workshop::published()
            ->upcoming()
            ->orderBy('starts_at')
            ->get();

        return view('workshops.index', compact('workshops'));
    }

    public function showRegistrationForm(Workshop $workshop): View
    {
        $user = Auth::user();
        $alreadyRegistered = false;
        if ($user) {
            $alreadyRegistered = $workshop->registrations()->where('user_id', $user->id)->exists();
        }

        return view('workshops.register', [
            'workshop' => $workshop,
            'prefill' => [
                'name' => optional($user)->name,
                'email' => optional($user)->email,
                'phone' => optional($user)->phone,
            ],
            'alreadyRegistered' => $alreadyRegistered,
        ]);
    }

    public function storeRegistration(StoreWorkshopRegistrationRequest $request, Workshop $workshop): RedirectResponse
    {
        $data = $request->validated();

        // Prevent duplicate (same user already registered)
        $userId = Auth::id();
        if ($userId && $workshop->registrations()->where('user_id', $userId)->exists()) {
            return redirect()->route('workshops.index')->with('success', 'أنت مسجل مسبقاً في هذه الورشة.');
        }

        // Capacity check
        if ($workshop->capacity && $workshop->registrations()->count() >= $workshop->capacity) {
            return back()->with('error', 'تم الوصول للحد الأقصى للمشاركين.');
        }

        $workshop->registrations()->create([
            'user_id' => Auth::id(),
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'whatsapp_phone' => $data['whatsapp_phone'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);
        // Auto unpublish + notify if capacity reached exactly now
        if ($workshop->capacity && $workshop->registrations()->count() >= $workshop->capacity) {
            if ($workshop->is_published) {
                $workshop->update(['is_published' => false]);
            }
            return redirect()->route('workshops.index')->with('success', 'تم التسجيل بنجاح — واكتمل العدد وتم إيقاف التسجيل.');
        }

        return redirect()->route('workshops.index')->with('success', 'تم استلام طلب المشاركة بنجاح. سنقوم بالتواصل معك قريبًا.');
    }
}
