<?php

namespace App\Services;

use App\Models\Aircraft\WakeCategory;
use App\Models\Aircraft\WakeCategoryScheme;
use Illuminate\Database\Eloquent\Collection;

class WakeService
{
    public function getWakeSchemesDependency(): array
    {
        return WakeCategoryScheme::with('categories', 'categories.departureIntervals')->get()->toArray();
    }

    private function mapCategories(Collection $categories): array
    {
        return $categories->map(
            function (WakeCategory $category) {
                return [
                    'id' => $category->id,
                    'code' => $category->code,
                    'subsequent_departure_intervals' => $this->mapDepartureIntervals($category->departureIntervals),
                ];
            }
        )->toArray();
    }

    private function mapDepartureIntervals(Collection $intervals): array
    {
        return $intervals->map(
            function (WakeCategory $subsequent) {
                return [
                    'id' => $subsequent->id,
                    'interval' => $subsequent->pivot->interval,
                    'intermediate' => $subsequent->pivot->intermediate,
                ];
            }
        )->toArray();
    }
}
