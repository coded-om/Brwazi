<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\CountryService;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Auth\Events\Verified;

class AuthSystem extends Controller
{
    public function login()
    {
        return view("login");
    }

    public function processLogin(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $credentials = $request->only('email', 'password');

        if (Auth::attempt($credentials, $request->boolean('remember'))) {
            $request->session()->regenerate();

            // Redirect to dashboard after successful login
            return redirect()->intended(route('user.dashboard'));
        }

        return back()->withErrors([
            'email' => 'البيانات المدخلة غير صحيحة.',
        ])->onlyInput('email');
    }

    public function register(): View
    {
        $countries = CountryService::getAllCountries();
        $taglines = \App\Models\User::getTaglineOptions();
        return view("register", compact('countries', 'taglines'));
    }

    public function processRegister(Request $request)
    {
        $request->validate([
            'fname' => 'required|string|max:255',
            'lname' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'phone_number' => 'nullable|string|max:20',
            'country' => 'nullable|string|max:100',
            'birthday' => 'nullable|date|before:today',
            'bio' => 'nullable|string|max:500',
            'tagline' => 'nullable|string|in:' . implode(',', User::getTaglineOptions()),
        ]);

        $user = User::create([
            'fname' => $request->fname,
            'lname' => $request->lname,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone_number' => $request->phone_number,
            'country' => $request->country,
            'birthday' => $request->birthday,
            'bio' => $request->bio,
            'tagline' => $request->tagline,
        ]);

        Auth::login($user);
        return redirect()->route('user.dashboard');
    }

    public function forgotPassword()
    {
        return view("forgot-password");
    }
}

