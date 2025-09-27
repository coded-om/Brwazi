<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

Route::post('/auth/login', function (Request $request) {
    $request->validate([
        'email' => 'required|email',
        'password' => 'required',
        'device_name' => 'nullable|string|max:100'
    ]);
    if (!Auth::attempt($request->only('email', 'password'))) {
        return response()->json(['message' => 'Invalid credentials'], 422);
    }
    /** @var User $user */
    $user = $request->user();
    $token = $user->createToken($request->input('device_name', 'api'));
    return response()->json([
        'token' => $token->plainTextToken,
        'user' => [
            'id' => $user->id,
            'fname' => $user->fname,
            'lname' => $user->lname,
            'email' => $user->email,
        ]
    ]);
});

Route::middleware('auth:sanctum')->group(function () {
    Route::get('/auth/me', function (Request $request) {
        return $request->user();
    });
    Route::post('/auth/logout', function (Request $request) {
        $request->user()->currentAccessToken()->delete();
        return response()->json(['message' => 'Logged out']);
    });
});
