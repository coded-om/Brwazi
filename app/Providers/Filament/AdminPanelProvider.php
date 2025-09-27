<?php

namespace App\Providers\Filament;

use Filament\Panel;
use Filament\PanelProvider;

use Filament\Pages\Dashboard;
use Filament\Support\Colors\Color;
use Filament\Http\Middleware\Authenticate as FilamentAuthenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            // Serve panel under /admin
            ->path('admin')
            ->brandName('لوحة التحكم')
            ->brandLogo('/imgs/icons-color/logo-color-word.svg')
            ->favicon('/logo-color.ico')
            ->colors([
                'primary' => Color::Blue,
            ])
            ->authGuard('admin')
            ->login(\App\Filament\Pages\Auth\Login::class)
            ->passwordReset()
            // Discover Filament components under the app/Filament directory if present
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            // Provide a default dashboard page so the panel has a landing page
            ->pages([
                \App\Filament\Pages\Dashboard::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([
                FilamentAuthenticate::class,
            ])
            ->renderHook('panels::topbar.start', function (): string {
                return view('filament.custom.topbar')->render();
            })
            ->sidebarCollapsibleOnDesktop();
    }
}
