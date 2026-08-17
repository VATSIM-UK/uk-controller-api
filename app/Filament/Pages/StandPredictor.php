<?php

namespace App\Filament\Pages;

use App\Allocator\Stand\AirlineAircraftArrivalStandAllocator;
use App\Allocator\Stand\AirlineAircraftTerminalArrivalStandAllocator;
use App\Allocator\Stand\AirlineCallsignArrivalStandAllocator;
use App\Allocator\Stand\AirlineCallsignSlugArrivalStandAllocator;
use App\Allocator\Stand\AirlineCallsignSlugTerminalArrivalStandAllocator;
use App\Allocator\Stand\AirlineCallsignTerminalArrivalStandAllocator;
use App\Allocator\Stand\AirlineDestinationArrivalStandAllocator;
use App\Allocator\Stand\AirlineDestinationTerminalArrivalStandAllocator;
use App\Allocator\Stand\AirlineGeneralArrivalStandAllocator;
use App\Allocator\Stand\AirlineGeneralTerminalArrivalStandAllocator;
use App\Allocator\Stand\CargoAirlineFallbackStandAllocator;
use App\Allocator\Stand\DomesticInternationalStandAllocator;
use App\Allocator\Stand\FallbackArrivalStandAllocator;
use App\Allocator\Stand\OriginAirfieldStandAllocator;
use App\Models\Stand\Stand;
use App\Models\User\RoleKeys;
use App\Models\Vatsim\NetworkAircraft;
use App\Services\Stand\ArrivalAllocationService;
use Filament\Pages\Page;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class StandPredictor extends Page
{
    protected static string|\BackedEnum|null $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationLabel = 'Stand Predictor';

    protected static ?string $slug = 'stand-predictor';

    protected string $view = 'filament.pages.stand-predictor';

    protected static string|\UnitEnum|null $navigationGroup = 'Airfield';

    protected $listeners = ['standPredictorFormSubmitted'];

    private ?array $currentPrediction = null;

    private const ALLOCATOR_LABELS = [
        AirlineAircraftArrivalStandAllocator::class => 'Airline & Aircraft Type',
        AirlineAircraftTerminalArrivalStandAllocator::class => 'Airline & Aircraft Type Terminal',
        AirlineCallsignArrivalStandAllocator::class => 'Airline Callsign',
        AirlineCallsignSlugArrivalStandAllocator::class => 'Airline Callsign Slug',
        AirlineCallsignSlugTerminalArrivalStandAllocator::class => 'Airline Callsign Slug Terminal',
        AirlineCallsignTerminalArrivalStandAllocator::class => 'Airline Callsign Terminal',
        AirlineDestinationArrivalStandAllocator::class => 'Airline Destination',
        AirlineDestinationTerminalArrivalStandAllocator::class => 'Airline Destination Terminal',
        AirlineGeneralArrivalStandAllocator::class => 'Airline General',
        AirlineGeneralTerminalArrivalStandAllocator::class => 'Airline General Terminal',
        CargoAirlineFallbackStandAllocator::class => 'Cargo Airline Fallback',
        DomesticInternationalStandAllocator::class => 'Domestic / International',
        FallbackArrivalStandAllocator::class => 'Fallback',
        OriginAirfieldStandAllocator::class => 'Origin Airfield',
    ];

    private const RANK_COLORS = ['primary', 'success', 'warning', 'danger', 'info'];

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
                fn (Collection $stands) => $stands->map(
                    fn (Collection $standsForRank) => $standsForRank->sortBy('identifier', SORT_NATURAL)
                        ->map(fn (Stand $stand) => $stand->identifier)->values()
                )
            )->toArray();
    }

    public function getAllocatorLabel(string $allocator): string
    {
        return self::ALLOCATOR_LABELS[$allocator] ?? class_basename($allocator);
    }

    public function getRankColor(int $rank): string
    {
        return self::RANK_COLORS[$rank % count(self::RANK_COLORS)];
    }

    public function getCurrentPrediction(): ?array
    {
        return $this->currentPrediction;
    }

    public function getTotalStandCount(): int
    {
        if ($this->currentPrediction === null) {
            return 0;
        }

        return collect($this->currentPrediction)
            ->flatten(2)
            ->count();
    }
}
