<?php

namespace App\Providers\Filament;

use App\Filament\Pages\Login;
use App\Filament\Widgets\ArrivalsBoard;
use App\Filament\Widgets\MyRoles;
use App\Filament\Widgets\MyStatus;
use Filament\Enums\ThemeMode;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationGroup;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\AuthenticateSession;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AdminPanelProvider extends PanelProvider
{
    private const VATSIM_UK_DARK_BLUE = [
        'DEFAULT' => '#17375E',
        50 => '#3378cd',
        100 => '#2c6ab6',
        200 => '#265d9f',
        300 => '#215089',
        400 => '#1c4373',
        500 => '#17375E',
        600 => '#022b50',
        700 => '#001f43',
        800 => '#001436',
        900 => '#000000'
    ];

    private const VATSIM_UK_LIGHT_BLUE = [
        'DEFAULT' => '#25ADE3',
        50 => '#8dd8f6',
        100 => '#78cdf1',
        200 => '#62c3ec',
        300 => '#006192',
        400 => '#48b8e8',
        500 => '#007aab',
        600 => '#007aab',
        700 => '#005888',
        800 => '#00325e',
        900 => '#000000'
    ];

    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('')
            ->login(Login::class)
            ->viteTheme('resources/css/filament/admin/theme.css')
            ->colors([
                'vatuk-darkblue' => self::VATSIM_UK_DARK_BLUE,
                'vatuk-lightblue' => self::VATSIM_UK_LIGHT_BLUE,
                'danger' => Color::Rose,
                'primary' => self::VATSIM_UK_LIGHT_BLUE,
                'success' => Color::Green,
                'warning' => Color::Amber,
            ])
            ->darkModeBrandLogo(asset('images/logo-bright.png'))
            ->brandLogo(asset('images/logo-dark.png'))
            ->defaultThemeMode(ThemeMode::Dark)
            ->favicon(asset('images/favicon.png'))
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->widgets([
                MyRoles::class,
                MyStatus::class,
                ArrivalsBoard::class,
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
            ]);
    }
}
