<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckNotBanned
{
    /**
     * Handle an incoming request.
     *
     * If the authenticated user is banned, block the action.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && method_exists($user, 'isBanned') && $user->isBanned()) {
            // JSON/AJAX requests
            if ($request->expectsJson() || $request->wantsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'تم تعطيل الرفع لحسابك. يرجى التواصل مع قسم الدعم.'
                ], 403);
            }

            // Web requests: flash a notify and redirect somewhere safe
            if (function_exists('notify')) {
                try {
                    notify()->warning('تم تعطيل الرفع لحسابك. يرجى التواصل مع قسم الدعم.');
                } catch (\Throwable $e) {
                }
            } else {
                session()->flash('warning', 'تم تعطيل الرفع لحسابك. يرجى التواصل مع قسم الدعم.');
            }

            // Avoid redirecting back to the same blocked route
            $fallback = route('user.dashboard', [], false) ?: '/';
            return redirect()->to($fallback);
        }

        return $next($request);
    }
}
