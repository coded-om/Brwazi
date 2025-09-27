<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Http\Exceptions\PostTooLargeException;

return Application::configure(basePath: dirname(__DIR__))
    ->withCommands([
        \App\Console\Commands\AuctionsTick::class,
        \App\Console\Commands\AutoCompleteOrder::class,
        \App\Console\Commands\RemindBuyerToConfirm::class,
    ])
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        api: __DIR__ . '/../routes/api.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withSchedule(function (Schedule $schedule) {
        $schedule->command('auctions:tick')->everyMinute();
        // Jobs: reminders & auto-complete
        $schedule->command('orders:remind-buyer --hours=48')->dailyAt('09:00');
        $schedule->command('orders:auto-complete --days=7')->dailyAt('03:00');
    })
    ->withMiddleware(function (Middleware $middleware): void {
        // Route middleware aliases
        $middleware->alias([
            'not.banned' => \App\Http\Middleware\CheckNotBanned::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Friendly message when POST exceeds php.ini post_max_size
        $exceptions->render(function (PostTooLargeException $e, $request) {
            $max = ini_get('post_max_size') ?: 'غير معروف';
            if (function_exists('notify')) {
                notify()->error(
                    "تم رفض الطلب لأن حجمه تجاوز الحد المسموح ({$max}). قلل حجم/عدد الملفات ثم حاول مرة أخرى.",
                    'حجم الرفع كبير'
                );
            }
            return back()->withErrors([
                'general' => "حجم الطلب تجاوز الحد المسموح ({$max}).",
            ]);
        });
    })->create();
