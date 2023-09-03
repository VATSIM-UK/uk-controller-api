<?php

namespace App\Filament\Pages;

use App\Models\Vatsim\NetworkAircraft;
use App\Services\Stand\ArrivalAllocationService;
use Filament\Pages\Page;

class StandPredictor extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static string $view = 'filament.pages.stand-predictor';

    protected $listeners = ['standPredictorFormSubmitted'];

    private ?array $currentPrediction = null;

    public function standPredictorFormSubmitted(array $data)
    {
        $this->currentPrediction = app()->make(ArrivalAllocationService::class)
            ->getAllocationRankingForAircraft(new NetworkAircraft($data));
    }
}
