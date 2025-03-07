<?php

namespace App\Providers\Filament;

use App\Models\Setting;
use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use App\Helpers\Helpers;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Navigation\NavigationGroup;
use Filament\Support\Colors\ColorManager;
use Filament\Http\Middleware\Authenticate;
use Filament\Support\Facades\FilamentColor;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Filament\Http\Middleware\AuthenticateSession;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\View\Middleware\ShareErrorsFromSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        FilamentColor::register([
            'primary' => Color::Pink
        ]);

        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->login()
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                //
            ])
            ->widgets([
                Widgets\AccountWidget::class,
                \App\Filament\Widgets\StatsOverview::class,
                \App\Filament\Widgets\PostChart::class,
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
                Authenticate::class,
            ])
            ->unsavedChangesAlerts()
            ->databaseTransactions()
            ->spa()
            ->colors(colors: [
                'primary' => Helpers::getColorRgb('primary', 500),
            ])
            ->brandName(Setting::retrieve('app_name', config('app.name')))
            ->font('Montserrat')
            ->favicon(asset('favicon.ico'))
            ->navigationGroups([
                NavigationGroup::make('Blog'),
                NavigationGroup::make('User'),
                NavigationGroup::make('Other')
                    ->collapsed()
            ])
            ->sidebarCollapsibleOnDesktop();
    }
}
