<?php

namespace App\Filament\Widgets;

use App\Models\Stand\StandRequest;
use App\Models\Vatsim\NetworkAircraft;
use App\Models\Vatsim\NetworkControllerPosition;
use Filament\Widgets\Widget;
use Illuminate\Support\Facades\Auth;

class MyStatus extends Widget
{
    protected static string $view = 'filament.widgets.my-status';

    protected function getViewData(): array
    {
        return [
            'controller' => NetworkControllerPosition::firstWhere('cid', Auth::id()),
            'aircraft' => NetworkAircraft::with('destinationAirfield', 'assignedStand', 'assignedStand.stand.airfield')
                ->firstWhere('cid', Auth::id()),
            'standRequest' => StandRequest::where('user_id', Auth::id())
                ->current()
                ->first(),
        ];
    }
}
