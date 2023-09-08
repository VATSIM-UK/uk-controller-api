<?php

namespace App\Filament\Pages;

use App\Models\Stand\Stand;
use App\Models\User\RoleKeys;
use App\Models\Vatsim\NetworkAircraft;
use App\Services\Stand\ArrivalAllocationService;
use Filament\Pages\Page;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class StandPredictor extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected static ?string $navigationLabel = 'Stand Predictor';
    protected static ?string $slug = 'stand-predictor';
    protected static string $view = 'filament.pages.stand-predictor';

    protected $listeners = ['standPredictorFormSubmitted'];

    private ?array $currentPrediction = null;

    public static function shouldRegisterNavigation(): bool
    {
        return self::userCanAccess();
    }

    public function mount(): void
    {
        abort_unless(self::userCanAccess(), 403);
    }

    private static function userCanAccess()
    {
        return Auth::user()->roles()
            ->whereIn('key', [
                RoleKeys::OPERATIONS_TEAM,
                RoleKeys::WEB_TEAM,
                RoleKeys::DIVISION_STAFF_GROUP,
                RoleKeys::OPERATIONS_CONTRIBUTOR,
            ])->exists();
    }

    public function standPredictorFormSubmitted(array $data)
    {
        $this->currentPrediction = app()->make(ArrivalAllocationService::class)
            ->getAllocationRankingForAircraft(new NetworkAircraft($data))
            ->map(
                fn (Collection $stands) =>
                $stands->map(
                    fn (Collection $standsForRank) =>
                    $standsForRank->sortBy('identifier', SORT_NATURAL)
                        ->map(fn (Stand $stand) => $stand->identifier)->values()
                )
            )->toArray();
    }
}
