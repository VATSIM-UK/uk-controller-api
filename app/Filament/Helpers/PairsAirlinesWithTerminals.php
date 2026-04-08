<?php

namespace App\Filament\Helpers;

use Filament\Actions\DetachAction;
use App\Filament\Resources\TranslatesStrings;
use Closure;
use Illuminate\Support\Facades\DB;

trait PairsAirlinesWithTerminals
{
    use HasAirlinePairingCommonFields;
    use TranslatesStrings;

    private static function airlineTerminalPairingTableColumns(): array
    {
        return self::commonPairingTableColumns();
    }

    private static function airlineTerminalPairingFormFields(): array
    {
        return self::commonPairingFormFields();
    }

    private static function unpairingClosure(): Closure
    {
        return function (DetachAction $action) {
            DB::table('airline_terminal')
                ->where('id', $action->getRecord()->pivot_id)
                ->delete();
        };
    }
}
