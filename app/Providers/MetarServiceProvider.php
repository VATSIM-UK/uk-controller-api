<?php

namespace App\Providers;

use App\Services\Metar\MetarRetrievalService;
use App\Services\Metar\MetarService;
use App\Services\Metar\Parser\PressureParser;
use App\Services\Metar\Parser\VisibilityParser;
use App\Services\Metar\Parser\WindParser;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class MetarServiceProvider extends ServiceProvider
{
    public function register()
    {
        parent::register();
        $this->app->singleton(MetarService::class, function (Application $application) {
            return new MetarService(
                $application->make(MetarRetrievalService::class),
                collect([
                    $this->app->make(PressureParser::class),
                    $this->app->make(WindParser::class),
                    $this->app->make(VisibilityParser::class)
                ])
            );
        });
    }
}
