<?php

namespace App\Providers;

use App\Exceptions\InvalidMslCalculationException;
use App\Helpers\MinStack\DirectMinStackCalculation;
use App\Helpers\MinStack\LowestMinStackCalculation;
use App\Helpers\MinStack\MinStackCalculableInterface;
use App\Models\Airfield\Airfield;
use App\Services\MetarService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Foundation\Application;

class MinStackCalculationServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->app->bind(MinStackCalculableInterface::class, function (Application $app, array $calculation) {
            if (!isset($calculation['type']) ||
                ($calculation['type'] !== 'direct' && $calculation['type'] !== 'lowest')
            ) {
                throw new InvalidMslCalculationException('Invalid calculation type');
            }

            return $calculation['type'] === 'direct'
                ? $this->getDirectCalculation($calculation)
                : $this->getLowestCalculation($calculation);
        });
    }

    /**
     * Create a direct calculation where the MSL of an airfield is determined by the QNH
     * of a single other airfield.
     *
     * @param array $calculation
     * @return DirectMinStackCalculation
     */
    private function getDirectCalculation(array $calculation) : DirectMinStackCalculation
    {
        return new DirectMinStackCalculation(
            Airfield::where('code', $calculation['airfield'])->firstOrFail(),
            $this->app->make(MetarService::class)
        );
    }

    /**
     * Create a calculation where the MSL of an airfield is based on the lowest QNH of a number
     * of other airfields.
     *
     * @param array $calculation
     * @return LowestMinStackCalculation
     */
    private function getLowestCalculation(array $calculation) : LowestMinStackCalculation
    {
        $airfields = [];
        foreach ($calculation['airfields'] as $airfield) {
            $airfields[] = Airfield::where('code', $airfield)->firstOrFail();
        }

        return new LowestMinStackCalculation(
            $this->app->make(MetarService::class),
            ...$airfields
        );
    }
}
