<?php

namespace App\Providers;

use App\SocialiteProviders\CoreProvider;
use Bugsnag\BugsnagLaravel\Facades\Bugsnag;
use Filament\Facades\Filament;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\HtmlString;
use Laravel\Passport\Passport;
use Illuminate\Validation\Rule;
use App\Services\SectorfileService;
use Illuminate\Support\ServiceProvider;
use Laravel\Socialite\Contracts\Factory;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        /*
         * Disable Passports migrations. We need to do this because the current production host runs
         * on a managed database server on which primary keys are enforced. Laravel currently does not allow
         * the creation of string primary keys in this environment as the queries are run in two parts. This is
         * currently a wontfix on Laravel, so we have to write our own migrations to circumvent.
         */
        Passport::ignoreMigrations();
    }
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
            Filament::registerTheme(mix('css/vatukfilament.css'));
        });
    }
}
