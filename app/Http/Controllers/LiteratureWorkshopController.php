<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreLiteratureWorkshopRegistrationRequest;
use App\Models\LiteratureWorkshop;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LiteratureWorkshopController extends Controller
{
    public function index(): View
    {
        $query = LiteratureWorkshop::published()->upcoming()->orderBy('starts_at')->withCount('registrations');
        $workshops = $query->get();
        // Preload user registration workshop ids to avoid per-card query
        $userRegistered = [];
        if (Auth::check()) {
            $userRegistered = \App\Models\LiteratureWorkshopRegistration::where('user_id', Auth::id())
                ->whereIn('literature_workshop_id', $workshops->pluck('id'))
                ->pluck('literature_workshop_id')
                ->all();
        }
        return view('literature_workshops.index', [
            'workshops' => $workshops,
            'userRegisteredIds' => $userRegistered,
        ]);
    }

    public function showRegistrationForm(LiteratureWorkshop $literatureWorkshop): View
    {
        $user = Auth::user();
        $alreadyRegistered = false;
        if ($user) {
            $alreadyRegistered = $literatureWorkshop->registrations()->where('user_id', $user->id)->exists();
        }
        return view('literature_workshops.register', [
            'workshop' => $literatureWorkshop,
            'prefill' => [
                'name' => optional($user)->name,
                'email' => optional($user)->email,
                'phone' => optional($user)->phone,
            ],
            'alreadyRegistered' => $alreadyRegistered,
        ]);
    }

    public function storeRegistration(StoreLiteratureWorkshopRegistrationRequest $request, LiteratureWorkshop $literatureWorkshop): RedirectResponse
    {
        $data = $request->validated();
        $userId = Auth::id();
        if ($userId && $literatureWorkshop->registrations()->where('user_id', $userId)->exists()) {
            return redirect()->route('literature_workshops.index')->with('success', 'أنت مسجل مسبقاً في هذه الورشة الأدبية.');
        }
        if ($literatureWorkshop->capacity && $literatureWorkshop->registrations()->count() >= $literatureWorkshop->capacity) {
            return back()->with('error', 'تم الوصول للحد الأقصى للمشاركين.');
        }
        $literatureWorkshop->registrations()->create([
            'user_id' => $userId,
            'name' => $data['name'],
            'email' => $data['email'],
            'phone' => $data['phone'] ?? null,
            'whatsapp_phone' => $data['whatsapp_phone'] ?? null,
            'notes' => $data['notes'] ?? null,
        ]);
        if ($literatureWorkshop->capacity && $literatureWorkshop->registrations()->count() >= $literatureWorkshop->capacity) {
            if ($literatureWorkshop->is_published) {
                $literatureWorkshop->update(['is_published' => false]);
            }
            return redirect()->route('literature_workshops.index')->with('success', 'تم التسجيل واكتمل العدد وتم إيقاف التسجيل.');
        }
        return redirect()->route('literature_workshops.index')->with('success', 'تم استلام طلب المشاركة بنجاح.');
    }
}
