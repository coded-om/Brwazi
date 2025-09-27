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

        return view('workshops.register', [
            'workshop' => $workshop,
            'prefill' => [
                'name' => optional($user)->name,
                'email' => optional($user)->email,
                'phone' => optional($user)->phone,
            ],
        ]);
    }

    public function storeRegistration(StoreWorkshopRegistrationRequest $request, Workshop $workshop): RedirectResponse
    {
        $data = $request->validated();

        $workshop->registrations()->create([
            'user_id' => Auth::id(),
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'whatsapp_phone' => $data['whatsapp_phone'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);

        return redirect()
            ->route('workshops.index')
            ->with('success', 'تم استلام طلب المشاركة بنجاح. سنقوم بالتواصل معك قريبًا.');
    }
}
