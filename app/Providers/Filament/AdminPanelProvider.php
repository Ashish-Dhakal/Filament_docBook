<?php

namespace App\Providers\Filament;

use Filament\Pages;
use Filament\Panel;
use Filament\Widgets;
use Filament\PanelProvider;
use Filament\Navigation\MenuItem;
use Filament\Support\Colors\Color;
use App\Filament\Pages\Auth\Register;
use App\Filament\Pages\UpdateProfile;
use App\Filament\Widgets\StatsOverview;
use App\Filament\Widgets\BlogPostsChart;
use Filament\Navigation\NavigationGroup;
use Filament\Http\Middleware\Authenticate;
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
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->profile(UpdateProfile::class)
            ->registration(Register::class)
            ->login()
            ->colors([
                'primary' => Color::Indigo,
                'secondary' => Color::Sky,
                'success' => Color::Emerald,
                'danger' => Color::Rose,
                'warning' => Color::Orange,
                'info' => Color::Amber,
            ])
            ->sidebarWidth('17rem')
            ->spa()
            ->sidebarCollapsibleOnDesktop()
            ->brandLogo(asset('image/favicon.png'))
            ->favicon(asset('image/favicon.png'))
            ->brandLogoHeight('4rem')
            ->font('poppins')
            ->brandName('DocBook')


            ->userMenuItems([
                MenuItem::make()
                    ->label('Dashboard')
                    ->icon('heroicon-o-cog-6-tooth')
                    // ->url(route('filament.admin.pages.dashboard')),
            ])

            ->navigationGroups([
                NavigationGroup::make()
                    ->label('User Management')
                    ->collapsible(false), 
                NavigationGroup::make()
                    ->label('Specialization')
                    ->collapsible(false), 
                NavigationGroup::make()
                    ->label('Appointments Management')
                    ->collapsible(false), 
                NavigationGroup::make()
                    ->label('Remarks')
                    ->collapsible(false), 
                NavigationGroup::make()
                    ->label('Payments Management')
                    ->collapsible(false), 
            ])


            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->pages([
                Pages\Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
            //   StatsOverview::class,
            //   BlogPostsChart::class,
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

    public function layout(): array
{
    return [
        'sidebar' => [
            'collapsible' => true,
            'brandLogo' => asset('image/favicon.png'),
        ],
    ];
}

}
