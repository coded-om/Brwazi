<?php

use App\Http\Controllers\AuthSystem;
use App\Http\Controllers\ArtController;
use App\Http\Controllers\LiteraryController;
use App\Http\Controllers\IndexController;
use App\Http\Controllers\MazadController;
use App\Http\Controllers\ImageController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MessagingController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\OrdersController;
use App\Http\Controllers\DisputeController;
use App\Http\Controllers\ArtistController;
use App\Http\Controllers\WorkshopController;
use App\Http\Controllers\LiteratureWorkshopController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


// Home
Route::get("/", [IndexController::class, "index"])->name('home');

// Brwaz Gallery (معرض برواز)
Route::get('/art-brwaz', function () {
    $settings = \App\Models\Gallery3DSetting::current();
    return view('artBrwaz.index', compact('settings'));
})->name('artbrwaz.index');

// Brwaz Gallery Exhibit (embedded iframe page)
Route::get('/art-brwaz/exhibit', function () {
    return view('artBrwaz.exhibit');
})->name('artbrwaz.exhibit');

// Workshops
Route::get('/workshops', [WorkshopController::class, 'index'])->name('workshops.index');
Route::middleware(['auth', 'not.banned'])->group(function () {
    Route::get('/workshops/{workshop:slug}/register', [WorkshopController::class, 'showRegistrationForm'])->name('workshops.register');
    Route::post('/workshops/{workshop:slug}/register', [WorkshopController::class, 'storeRegistration'])->name('workshops.register.store');
});
// User submission (verified users)
// Literature Workshops
Route::get('/literature-workshops', [LiteratureWorkshopController::class, 'index'])->name('literature_workshops.index');
Route::middleware(['auth', 'not.banned'])->group(function () {
    Route::get('/literature-workshops/{literatureWorkshop:slug}/register', [LiteratureWorkshopController::class, 'showRegistrationForm'])->name('literature_workshops.register');
    Route::post('/literature-workshops/{literatureWorkshop:slug}/register', [LiteratureWorkshopController::class, 'storeRegistration'])->name('literature_workshops.register.store');
});

Route::middleware(['auth', 'not.banned'])->group(function () {
    Route::get('/workshops/submit/new', [\App\Http\Controllers\UserWorkshopSubmissionController::class, 'create'])->name('workshops.submit.create');
    Route::post('/workshops/submit/new', [\App\Http\Controllers\UserWorkshopSubmissionController::class, 'store'])->name('workshops.submit.store');
});

// Artists directory (public)
Route::get('/artists', [ArtistController::class, 'index'])->name('artists.index');
// Single artist public profile
Route::get('/artists/{artist}', [ArtistController::class, 'show'])
    ->whereNumber('artist')
    ->name('artists.show');
// Report artist profile
Route::post('/artists/{artist}/report', [ArtistController::class, 'report'])
    ->whereNumber('artist')
    ->middleware('auth')
    ->name('artists.report');

// Auth
Route::get('/login', [AuthSystem::class, "login"])->name('login');
Route::post('/login', [AuthSystem::class, "processLogin"])->name('login.process');
Route::get('/register', [AuthSystem::class, "register"])->name('register');
Route::post('/register', [AuthSystem::class, "processRegister"])->name('register.process');
Route::get('/forgot-password', [AuthSystem::class, "forgotPassword"]);

Route::post('/logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();
    return redirect('/');
})->name('logout');

// User area (dashboard, profile, messages)
Route::middleware(['auth'])->prefix('user')->name('user.')->group(function () {
    Route::get('/dashboard', [UserController::class, 'dashboard'])->name('dashboard');
    Route::get('/profile', [UserController::class, 'profile'])->name('profile');
    Route::put('/profile', [UserController::class, 'updateProfile'])->name('profile.update');
    Route::put('/password', [UserController::class, 'updatePassword'])->name('password.update');
    Route::post('/profile-image', [UserController::class, 'updateProfileImage'])->name('profile.image.update');

    // Messages and conversations
    Route::get('/messages', [MessagingController::class, 'index'])->name('messages');
    Route::get('/conversation/{conversation}', [MessagingController::class, 'show'])->name('conversation');
    Route::get('/conversation/{conversation}/new-messages', [MessagingController::class, 'getNewMessages'])->name('conversation.new-messages');
    Route::post('/conversation/start', [MessagingController::class, 'startConversation'])->name('conversation.start');
    Route::post('/conversation/{conversation}/message', [MessagingController::class, 'sendMessage'])->name('message.send');
    Route::post('/conversation/{conversation}/artwork-request', [MessagingController::class, 'sendArtworkRequest'])->name('artwork-request.send');
    Route::post('/artwork-request/{artworkRequest}/respond', [MessagingController::class, 'respondToArtworkRequest'])->name('artwork-request.respond');
    Route::post('/conversation/{conversation}/mark-read', [MessagingController::class, 'markAsRead'])->name('conversation.mark-read');
    Route::get('/search-users', [MessagingController::class, 'searchUsers'])->name('search-users');

    // Auction requests
    Route::get('/auctions/request', [\App\Http\Controllers\AuctionRequestController::class, 'create'])->name('auctions.request.create');
    Route::post('/auctions/request', [\App\Http\Controllers\AuctionRequestController::class, 'store'])->name('auctions.request.store');
});

// Art
Route::prefix('art')->name('art.')->group(function () {
    // Public
    Route::get('/', [ArtController::class, 'index'])->name('index');
    Route::get('/{artwork}', [ArtController::class, 'show'])->whereNumber('artwork')->name('show');
    // Auth-only
    Route::middleware(['auth', 'not.banned'])->group(function () {
        Route::get('/create', [ArtController::class, 'create'])->name('create');
        Route::post('/', [ArtController::class, 'store'])->name('store');
        Route::get('/{artwork}/edit', [ArtController::class, 'edit'])->whereNumber('artwork')->name('edit');
        Route::put('/{artwork}', [ArtController::class, 'update'])->whereNumber('artwork')->name('update');
        Route::post('/upload-image', [ArtController::class, 'uploadTempImage'])->name('upload-image');
        Route::post('/preview', [ArtController::class, 'preview'])->name('preview');
        Route::post('/{artwork}/like', [ArtController::class, 'like'])->name('like');
        Route::delete('/{artwork}/like', [ArtController::class, 'unlike'])->name('unlike');
        Route::post('/{artwork}/report', [ArtController::class, 'report'])->name('report');
    });
});

// Cart (session-based minimal add)
Route::post('/cart/add', function () {
    $artworkId = (int) request('artwork_id');
    $qty = max(1, (int) request('quantity', 1));

    $art = \App\Models\Artwork::find($artworkId);
    if (!$art) {
        if (request()->expectsJson()) {
            return response()->json(['success' => false, 'message' => 'العمل غير موجود'], 404);
        }
        if (function_exists('notify'))
            notify()->error('العمل غير موجود');
        return back()->with('error', 'العمل غير موجود');
    }
    if ($art->user_id === \Illuminate\Support\Facades\Auth::id()) {
        if (request()->expectsJson()) {
            return response()->json(['success' => false, 'message' => 'لا يمكنك شراء عملك الخاص'], 403);
        }
        if (function_exists('notify'))
            notify()->warning('لا يمكنك شراء عملك الخاص');
        return back()->with('error', 'لا يمكنك شراء عملك الخاص');
    }

    $cart = session('cart', []);
    $cart[$artworkId] = ($cart[$artworkId] ?? 0) + $qty;
    session(['cart' => $cart]);
    $count = is_array($cart) ? array_sum($cart) : 0;
    if (request()->expectsJson()) {
        return response()->json(['success' => true, 'count' => $count]);
    }
    if (function_exists('notify'))
        notify()->success('تمت الإضافة إلى السلة');
    return back()->with('success', 'تمت الإضافة إلى السلة');
})->name('cart.add')->middleware('auth');

// Cart pages
Route::middleware('auth')->group(function () {
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart/update', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/remove/{artworkId}', [CartController::class, 'remove'])->name('cart.remove');

    // Checkout
    Route::get('/checkout/{artwork}', [CheckoutController::class, 'beginForArtwork'])->whereNumber('artwork')->name('checkout.begin');
    Route::post('/checkout', [CheckoutController::class, 'checkout'])->name('checkout.process');

    // Orders (buyer/seller)
    Route::get('/orders', [OrdersController::class, 'index'])->name('orders.index');
    Route::get('/orders/{order}', [OrdersController::class, 'show'])->name('orders.show');
    Route::post('/orders/{order}/shipping', [OrdersController::class, 'addShipping'])->name('orders.shipping');
    Route::post('/orders/{order}/delivered', [OrdersController::class, 'confirmDelivered'])->name('orders.delivered');
    Route::post('/orders/{order}/dispute', [DisputeController::class, 'open'])->name('orders.dispute');
    Route::get('/orders/{order}/invoice', [OrdersController::class, 'downloadInvoice'])->name('orders.invoice');

    // Verification application (membership/وثوق)
    Route::get('/verify/apply', [\App\Http\Controllers\VerificationRequestController::class, 'create'])->name('verification.apply');
    Route::post('/verify/apply', [\App\Http\Controllers\VerificationRequestController::class, 'store'])->name('verification.store');
    Route::get('/verify/thanks', [\App\Http\Controllers\VerificationRequestController::class, 'thanks'])->name('verification.thanks');
    Route::get('/verify/request/{verificationRequest}', [\App\Http\Controllers\VerificationRequestController::class, 'show'])->name('verification.show');
});

// Payment return/webhook and success/cancel should be accessible without auth (provider redirects may not carry session)
Route::get('/payment/return', [PaymentController::class, 'return'])->name('payment.return');
Route::post('/payment/webhook', [PaymentController::class, 'webhook'])->name('payment.webhook');
Route::get('/checkout/success', [CheckoutController::class, 'success'])->name('checkout.success');
Route::get('/checkout/cancel', [CheckoutController::class, 'cancel'])->name('checkout.cancel');

// Literary
Route::prefix('literary')->group(function () {
    Route::get('/', [LiteraryController::class, 'index']);
    Route::get('/all', [LiteraryController::class, 'allLitetaty']);
    Route::get('/book/{id}', [LiteraryController::class, 'book'])->whereNumber('id')->middleware('auth');
    // User review submission (requires auth, moderation enabled)
    Route::post('/book/{book}/review', [\App\Http\Controllers\ReviewController::class, 'store'])->middleware('auth');
});

// Mazad
Route::prefix('mazad')->name('mazad.')->group(function () {
    Route::get('/', [MazadController::class, 'index'])->name('index');
    Route::get('/{auction}', [MazadController::class, 'show'])->whereNumber('auction')->name('show');
    Route::get('/{auction}/state', [MazadController::class, 'state'])->whereNumber('auction')->name('state');
    Route::post('/{auction}/bid', [MazadController::class, 'bid'])->whereNumber('auction')->name('bid')->middleware('auth');
    Route::post('/{auction}/pay', [MazadController::class, 'pay'])->whereNumber('auction')->name('pay')->middleware('auth');
    // Auction insurance deposit page
    Route::get('/insurance', [MazadController::class, 'insurancePage'])->name('insurance');
    Route::post('/insurance/hold', [MazadController::class, 'insuranceHold'])->name('insurance.hold')->middleware('auth');
});

// Image upload routes
Route::prefix('images')->name('images.')->middleware(['auth', 'not.banned'])->group(function () {
    Route::post('/upload-profile', [ImageController::class, 'uploadProfileImage'])->name('upload.profile');
    Route::post('/upload-products', [ImageController::class, 'uploadProductImages'])->name('upload.products');
    Route::post('/preview-info', [ImageController::class, 'previewImageInfo'])->name('preview.info');
});

// Admin routes (legacy) — moved to /legacy-admin to avoid collision with Filament panel
Route::prefix('legacy-admin')->name('legacy-admin.')->group(function () {
    // Legacy login no longer used
    Route::get('/login', function () {
        return redirect('/admin/login');
    })->name('login')->middleware('guest:admin');
    Route::post('/login', function () {
        $creds = request()->validate(['email' => 'required|email', 'password' => 'required']);
        if (auth('admin')->attempt($creds)) {
            request()->session()->regenerate();
            return redirect()->route('admin.dashboard');
        }
        return back()->withErrors(['email' => 'بيانات غير صحيحة']);
    })->name('login.submit')->middleware('guest:admin');

    Route::post('/logout', function () {
        auth('admin')->logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
        return redirect()->route('admin.login');
    })->name('logout');

    Route::middleware('auth:admin')->group(function () {
        // Legacy dashboard
        Route::get('/', function () {
            return view('admin.dashboard');
        })->name('dashboard');

        // Auction requests review
        Route::get('/auctions/requests', [\App\Http\Controllers\Admin\AuctionAdminController::class, 'index'])->name('auctions.requests');
        Route::post('/auctions/requests/{auctionRequest}/approve', [\App\Http\Controllers\Admin\AuctionAdminController::class, 'approve'])->name('auctions.requests.approve');
        Route::post('/auctions/requests/{auctionRequest}/reject', [\App\Http\Controllers\Admin\AuctionAdminController::class, 'reject'])->name('auctions.requests.reject');

        // Admin orders: resolve dispute
        Route::post('/orders/{order}/resolve-dispute', [DisputeController::class, 'resolve'])->name('orders.resolve-dispute');

        // Admin orders: download invoice (used by Filament action)
        Route::get('/orders/{order}/invoice', [OrdersController::class, 'downloadInvoice'])->name('orders.invoice');
    });
});
