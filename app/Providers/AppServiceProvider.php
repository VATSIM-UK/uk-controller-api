<?php

namespace App\Providers;

use App\Http\Livewire\CurrentStandRequest;
use App\Http\Livewire\RequestAStandForm;
use App\Http\Livewire\StandPredictorForm;
use App\SocialiteProviders\CoreProvider;
use Bugsnag\BugsnagLaravel\Facades\Bugsnag;
use Filament\Facades\Filament;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Laravel\Passport\Passport;
use Illuminate\Validation\Rule;
use App\Services\SectorfileService;
use Illuminate\Support\ServiceProvider;
use Laravel\Socialite\Contracts\Factory;
use Livewire\Livewire;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        Bugsnag::registerCallback(function ($report) {
            if (Auth::check()) {
                $user = Auth::user();

                $report->setUser([
                    'id' => $user->id,
                    'name' => $user->name
                ]);
            }
        });

        Rule::macro('latitudeString', function () {
            return 'regex:' . SectorfileService::SECTORFILE_LATITUDE_REGEX;
        });

        Rule::macro('longitudeString', function () {
            return 'regex:' . SectorfileService::SECTORFILE_LONGITUDE_REGEX;
        });

        // Register our custom VATSIM UK Core SSO Socialite Provider
        $socialite = $this->app->make(Factory::class);
        $socialite->extend(
            'vatsimuk',
            function ($app) use ($socialite) {
                $config = $app['config']['services.vatsim_uk_core'];
                $config['redirect'] = route('auth.login.callback');

                return $socialite->buildProvider(CoreProvider::class, $config);
            }
        );

        // Filament styling
        Filament::serving(function () {
            Filament::registerNavigationGroups(
                [
                    'Preferences',
                    'Airline',
                    'Airfield',
                    'Controller',
                    'Enroute',
                    'Intention Codes',
                    'Squawk Ranges',
                    'Plugin',
                    'Administration',
                    'Documentation'
                ]
            );
        });

        // Livewire
        Livewire::component('request-a-stand-form', RequestAStandForm::class);
        Livewire::component('current-stand-request', CurrentStandRequest::class);
        Livewire::component('stand-predictor-form', StandPredictorForm::class);

        // Hoppie ACARS must limit requests to 1 every 10+ seconds
        RateLimiter::for('hoppie', fn () => Limit::perMinute(5));
    }
}
