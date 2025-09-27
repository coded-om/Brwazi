<?php

namespace App\Http\Controllers;

use App\Models\VerificationRequest;
use App\Models\VerificationFormContent;
use App\Models\HomepageSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class VerificationRequestController extends Controller
{
    public function create()
    {
        $forms = VerificationFormContent::all()->keyBy('form_type');
        $uploadMaxMb = HomepageSetting::query()->value('upload_max_mb') ?? 40;
        return view('verification.apply', compact('forms', 'uploadMaxMb'));
    }

    public function store(Request $request)
    {
        $user = $request->user();

        // Prevent normal users from uploading artworks until verified by policy elsewhere
        // Here we only accept applications.

        // منع إنشاء طلب جديد إذا لدى المستخدم طلب قيد المراجعة
        if ($user && method_exists($user, 'hasPendingVerification') && $user->hasPendingVerification()) {
            return back()->withErrors(['general' => 'لديك طلب توثيق قيد المراجعة حالياً.'])->withInput();
        }

        $data = $request->validate([
            'formType' => 'required|in:visual,photo',
            'fullName' => 'required|string|max:255',
            'birthDate' => 'required|date',
            'gender' => 'required|string|max:10',
            'education' => 'required|string|max:255',
            'address' => 'required|string|max:255',
            'phone' => 'required|string|max:50',
            'email' => 'required|email',
            'nationality' => 'nullable|string|max:50',
            'specialties' => 'required|array|min:1',
            'specialties.*' => 'string|max:100',
            'idFile' => 'required|file|max:10240',
            'avatarFile' => 'required|file|max:10240',
            'cvFile' => 'nullable|file|max:10240',
            'works' => 'required|array',
            'works.*' => 'file|mimes:jpeg,png,jpg,gif,webp|max:10240',
        ]);

        // Extra rules based on admin-configured limits
        $content = VerificationFormContent::for($data['formType']);
        $worksCount = count($request->file('works', []));
        $min = (int) $content->works_min;
        $max = (int) $content->works_max;
        if ($worksCount < $min || $worksCount > $max) {
            $msg = $min === $max
                ? "الرجاء رفع {$min} صور بالضبط."
                : "الرجاء رفع ما بين {$min} إلى {$max} صور.";
            return back()->withErrors(['works' => $msg])->withInput();
        }

        // Enforce total upload size limit (admin-configurable)
        $uploadMaxMb = HomepageSetting::query()->value('upload_max_mb') ?? 40;
        $totalSize = 0;
        foreach (['idFile', 'avatarFile', 'cvFile'] as $f) {
            if ($request->hasFile($f)) {
                $totalSize += $request->file($f)->getSize() ?: 0;
            }
        }
        foreach ($request->file('works', []) as $file) {
            $totalSize += $file->getSize() ?: 0;
        }
        if ($totalSize > $uploadMaxMb * 1024 * 1024) {
            return back()->withErrors(['works' => "إجمالي حجم الملفات المرفوعة ({$uploadMaxMb}MB) تم تجاوزه. قلل عدد أو حجم الملفات."])->withInput();
        }

        $disk = 'public';
        $base = 'verification/' . $user->id . '/' . now()->format('Ymd_His') . '_' . Str::random(4);

        $idPath = $request->file('idFile')->store($base, $disk);
        $avatarPath = $request->file('avatarFile')->store($base, $disk);
        $cvPath = $request->file('cvFile')?->store($base, $disk);

        $worksPaths = [];
        foreach ($request->file('works') as $file) {
            $worksPaths[] = $file->store($base, $disk);
        }

        VerificationRequest::create([
            'user_id' => $user->id,
            'form_type' => $data['formType'],
            'full_name' => $data['fullName'],
            'birth_date' => $data['birthDate'],
            'gender' => $data['gender'],
            'education' => $data['education'],
            'address' => $data['address'],
            'phone' => $data['phone'],
            'email' => $data['email'],
            'nationality' => $data['nationality'] ?? null,
            'specialties' => $data['specialties'],
            'id_file_path' => $idPath,
            'avatar_file_path' => $avatarPath,
            'cv_file_path' => $cvPath,
            'works_files' => $worksPaths,
            'status' => VerificationRequest::STATUS_PENDING,
            'submitted_at' => now(),
        ]);

        return redirect()->route('verification.thanks');
    }

    public function thanks()
    {
        return view('verification.thanks');
    }

    public function show(VerificationRequest $verificationRequest)
    {
        // Authorize: only owner or admin (simple check, expand as needed)
        $user = request()->user();
        if ($user && $verificationRequest->user_id !== $user->id && !method_exists($user, 'isAdmin')) {
            abort(403);
        }

        $rawPaths = is_array($verificationRequest->works_files) ? $verificationRequest->works_files : [];
        $worksUrls = $verificationRequest->works_file_urls; // accessor returns array of URLs
        $disk = \Illuminate\Support\Facades\Storage::disk('public');
        $diagnostics = [];
        foreach ($rawPaths as $idx => $p) {
            $diagnostics[] = [
                'path' => $p,
                'exists' => $disk->exists($p),
                'url' => $worksUrls[$idx] ?? null,
            ];
        }
        $storageLinked = is_link(public_path('storage'));

        return view('verification.show', [
            'request' => $verificationRequest,
            'works' => $worksUrls,
            'worksDiagnostics' => $diagnostics,
            'storageLinked' => $storageLinked,
        ]);
    }
}
